<?php

namespace App\Http\Controllers;

use App\Models\ExecucaoPrescricao;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ExecucaoPrescricaoController extends Controller
{
    public function index(Request $request): Response
    {
        $fonoId = Auth::id();
        $pacienteId = $request->query('paciente_id');
        $dataInicio = $request->query('data_inicio');
        $dataFim = $request->query('data_fim');

        $query = ExecucaoPrescricao::whereHas('paciente', function ($q) use ($fonoId) {
            $q->where('fonoaudiologo_id', $fonoId);
        })
        ->with([
            'paciente:id,nome',
            'paciente.media',
            'prescricao.exercicio:id,nome,categoria_id',
            'prescricao.exercicio.categoria:id,nome',
            'media',
        ]);

        if ($pacienteId) {
            $query->where('paciente_id', $pacienteId);
        }

        if ($dataInicio) {
            $query->whereDate('data_execucao', '>=', $dataInicio);
        }

        if ($dataFim) {
            $query->whereDate('data_execucao', '<=', $dataFim);
        }

        $execucoes = (clone $query)->latest('data_execucao')->latest('id')->get();

        $metricas = $this->calcularMetricas($execucoes);

        $pacientes = Paciente::where('fonoaudiologo_id', $fonoId)->select('id', 'nome')->orderBy('nome')->get();

        return Inertia::render('Execucoes/Index', [
            'execucoes' => $execucoes,
            'pacientes' => $pacientes,
            'metricas'  => $metricas,
            'filtros'   => [
                'paciente_id' => $pacienteId ?? '',
                'data_inicio' => $dataInicio ?? '',
                'data_fim'    => $dataFim ?? '',
            ],
        ]);
    }

    public function updateComentario(Request $request, ExecucaoPrescricao $execucao): RedirectResponse
    {
        $this->authorizeAccess($execucao);

        $request->validate([
            'comentario_fono' => ['nullable', 'string', 'max:1000'],
        ]);

        $execucao->update([
            'comentario_fono' => $request->input('comentario_fono'),
        ]);

        return back()->with('success', 'Feedback registrado com sucesso!');
    }

    private function calcularMetricas($execucoes): array
    {
        $total = $execucoes->count();
        $comMidia = $execucoes->filter(fn ($e) => !empty($e->arquivo_url))->count();
        $comFeedback = $execucoes->filter(fn ($e) => !empty($e->comentario_fono))->count();

        $comSatisfacao = $execucoes->filter(fn ($e) => $e->nivel_satisfacao > 0);
        $mediaSatisfacao = $comSatisfacao->count() > 0 ? round($comSatisfacao->avg('nivel_satisfacao'), 1) : 0;

        $diasSemana = [
            1 => ['nome' => 'Seg', 'total' => 0],
            2 => ['nome' => 'Ter', 'total' => 0],
            3 => ['nome' => 'Qua', 'total' => 0],
            4 => ['nome' => 'Qui', 'total' => 0],
            5 => ['nome' => 'Sex', 'total' => 0],
            6 => ['nome' => 'Sáb', 'total' => 0],
            7 => ['nome' => 'Dom', 'total' => 0],
        ];

        foreach ($execucoes as $e) {
            if ($e->data_execucao) {
                $diaIso = Carbon::parse($e->data_execucao)->isoFormat('E');
                if (isset($diasSemana[(int) $diaIso])) {
                    $diasSemana[(int) $diaIso]['total']++;
                }
            }
        }

        return [
            'total_execucoes'   => $total,
            'com_midia'         => $comMidia,
            'com_feedback'      => $comFeedback,
            'media_satisfacao'  => $mediaSatisfacao,
            'grafico_semanal'   => array_values($diasSemana),
        ];
    }

    private function authorizeAccess(ExecucaoPrescricao $execucao): void
    {
        if ((int) $execucao->paciente?->fonoaudiologo_id !== (int) Auth::id()) {
            abort(403);
        }
    }
}