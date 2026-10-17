<?php

namespace App\Services;

use App\Models\ExecucaoPrescricao;
use App\Models\Exercicio;
use App\Models\Paciente;
use App\Models\Prescricao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardService
{
    public function getDashboardData(): array
    {
        $fonoId = Auth::id();
        
        return [
            'stats' => $this->getStats($fonoId),
            'weeklyAdherence' => $this->getWeeklyAdherence($fonoId),
            'topCategories' => $this->getTopCategories($fonoId),
            'recentActivities' => $this->getRecentActivities($fonoId),
            'attentionPatients' => $this->getAttentionPatients($fonoId),
        ];
    }

    private function getStats(int $fonoId): array
    {
        $pacientesAtivos = Paciente::where('fonoaudiologo_id', $fonoId)->count();
        $exerciciosCadastrados = Exercicio::where('fonoaudiologo_id', $fonoId)->count();
        
        $prescricoesAtivas = Prescricao::whereHas('paciente', fn($q) => $q->where('fonoaudiologo_id', $fonoId))->whereDate('data_fim', '>=', now())->count();

        $pacientesComTreino = ExecucaoPrescricao::whereHas('paciente', fn($q) => $q->where('fonoaudiologo_id', $fonoId))->where('data_execucao', '>=', now()->subDays(7))->distinct('paciente_id')->count('paciente_id');

        $taxaAdesao = $pacientesAtivos > 0 ? round(($pacientesComTreino / $pacientesAtivos) * 100) : 0;

        return [
            [
                'name' => 'Pacientes Ativos',
                'value' => (string) $pacientesAtivos,
                'change' => 'Cadastrados na clínica',
                'changeType' => 'neutral',
                'icon' => 'UsersIcon',
                'color' => 'bg-blue-500',
            ],
            [
                'name' => 'Exercícios Cadastrados',
                'value' => (string) $exerciciosCadastrados,
                'change' => 'Na base de dados',
                'changeType' => 'neutral',
                'icon' => 'SparklesIcon',
                'color' => 'bg-indigo-500',
            ],
            [
                'name' => 'Prescrições Ativas',
                'value' => (string) $prescricoesAtivas,
                'change' => 'Sincronizadas com o App',
                'changeType' => 'positive',
                'icon' => 'ClipboardDocumentListIcon',
                'color' => 'bg-sky-500',
            ],
            [
                'name' => 'Adesão Semanal',
                'value' => $taxaAdesao . '%',
                'change' => 'Pacientes que treinaram',
                'changeType' => $taxaAdesao >= 70 ? 'positive' : ($taxaAdesao > 0 ? 'neutral' : 'negative'),
                'icon' => 'CheckCircleIcon',
                'color' => 'bg-emerald-500',
            ],
        ];
    }

    private function getWeeklyAdherence(int $fonoId): array
    {
        $weekly = [];

        $expectedDaily = Prescricao::whereHas('paciente', fn($q) => $q->where('fonoaudiologo_id', $fonoId))->whereDate('data_inicio', '<=', now())->whereDate('data_fim', '>=', now())->count();
            
        $expectedDaily = $expectedDaily > 0 ? $expectedDaily : 1;

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            $done = ExecucaoPrescricao::whereHas('paciente', fn($q) => $q->where('fonoaudiologo_id', $fonoId))->whereDate('data_execucao', $date->toDateString())->count();

            $pct = min(100, round(($done / $expectedDaily) * 100));

            $weekly[] = [
                'day' => ucfirst($date->locale('pt-BR')->shortDayName),
                'pct' => $pct,
                'done' => $done,
                'total' => $expectedDaily
            ];
        }

        return $weekly;
    }

    private function getTopCategories(int $fonoId): array
    {
        $categories = DB::table('exercicio_prescricao')
            ->join('exercicios', 'exercicio_prescricao.exercicio_id', '=', 'exercicios.id')
            ->join('categorias', 'exercicios.categoria_id', '=', 'categorias.id')
            ->where('exercicios.fonoaudiologo_id', $fonoId)
            ->select('categorias.nome', DB::raw('count(exercicio_prescricao.exercicio_id) as total'))
            ->groupBy('categorias.nome')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-sky-400', 'bg-emerald-500'];
        $maxTotal = $categories->max('total') ?: 1;

        return $categories->map(function ($cat, $index) use ($colors, $maxTotal) {
            return [
                'name' => $cat->nome,
                'total' => $cat->total,
                'color' => $colors[$index % 4],
                'pct' => round(($cat->total / $maxTotal) * 100)
            ];
        })->toArray();
    }

    private function getRecentActivities(int $fonoId): array
    {
        return ExecucaoPrescricao::with(['paciente', 'prescricao.exercicios'])
            ->whereHas('paciente', fn($q) => $q->where('fonoaudiologo_id', $fonoId))
            ->orderBy('data_execucao', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($exec) {
                $exercicios = $exec->prescricao && $exec->prescricao->exercicios->isNotEmpty()? $exec->prescricao->exercicios->pluck('nome')->implode(', '): 'Treino Diário';

                $hasMedia = !empty($exec->arquivo_url);
                
                return [
                    'id' => $exec->id,
                    'paciente' => $exec->paciente->nome,
                    'exercicio' => Str::limit($exercicios, 45),
                    'data' => Carbon::parse($exec->data_execucao)->locale('pt-BR')->diffForHumans(),
                    'status' => $hasMedia ? 'Com Gravação' : 'Concluído',
                    'statusColor' => $hasMedia ? 'text-purple-700 bg-purple-50 ring-purple-600/20' : 'text-emerald-700 bg-emerald-50 ring-emerald-600/20',
                    'score' => $exec->nivel_satisfacao ? "Autoavaliação: {$exec->nivel_satisfacao} estrelas" : 'Sessão concluída',
                ];
            })->toArray();
    }

    private function getAttentionPatients(int $fonoId): array
    {
        $threeDaysAgo = now()->subDays(3);

        return Paciente::where('fonoaudiologo_id', $fonoId)
            ->whereHas('prescricoes', fn($q) => $q->whereDate('data_fim', '>=', now()))
            ->whereDoesntHave('execucoes', fn($q) => $q->whereDate('data_execucao', '>=', $threeDaysAgo))
            ->with(['execucoes' => fn($q) => $q->latest('data_execucao')->limit(1)])
            ->take(4)
            ->get()
            ->map(function ($p) {
                $lastExec = $p->execucoes->first();
                $days = $lastExec 
                    ? Carbon::parse($lastExec->data_execucao)->diffInDays(now()) 
                    : 'Sem registros';

                return [
                    'name' => $p->nome,
                    'daysWithoutTraining' => $days,
                    'appCode' => $p->codigo_app ?? sprintf("APP-%04d", $p->id)
                ];
            })->toArray();
    }
}