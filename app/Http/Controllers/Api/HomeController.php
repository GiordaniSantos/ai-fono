<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Prescricao;
use App\Models\ExecucaoExercicio;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $paciente = $request->user();
        $hoje = Carbon::today();

        $prescricoesVigentes = Prescricao::with(['exercicios.categoria'])
            ->where('paciente_id', $paciente->id)
            ->where('data_inicio', '<=', $hoje)
            ->where(function ($query) use ($hoje) {
                $query->whereNull('data_fim')
                      ->orWhere('data_fim', '>=', $hoje);
            })
            ->get();

        $exerciciosIds = $prescricoesVigentes->flatMap(function ($p) {
            return $p->exercicios->pluck('id');
        })->unique()->values();

        $totalExercicios = $exerciciosIds->count();

        $concluidosHoje = ExecucaoExercicio::where('paciente_id', $paciente->id)
            ->whereIn('exercicio_id', $exerciciosIds)
            ->whereDate('data_execucao', $hoje->toDateString())
            ->count();

        $progressoGeral = $totalExercicios > 0 
            ? round(($concluidosHoje / $totalExercicios) * 100) 
            : 0;

        $categorias = Categoria::where('fonoaudiologo_id', $paciente->fonoaudiologo_id)
            ->get()
            ->map(function ($categoria) use ($prescricoesVigentes, $paciente, $hoje) {
                
                $exerciciosDaCategoria = $prescricoesVigentes->flatMap(function ($p) use ($categoria) {
                    return $p->exercicios;
                })->filter(function ($e) use ($categoria) {
                    return $e->categoria_id === $categoria->id;
                })->unique('id');

                $total = $exerciciosDaCategoria->count();
                if ($total === 0) {
                    return null;
                }

                $idsDaCategoria = $exerciciosDaCategoria->pluck('id');

                $concluidosCat = ExecucaoExercicio::where('paciente_id', $paciente->id)
                    ->whereIn('exercicio_id', $idsDaCategoria)
                    ->whereDate('data_execucao', $hoje->toDateString())
                    ->count();

                $pendentes = max(0, $total - $concluidosCat);

                return [
                    'id' => $categoria->id,
                    'nome' => $categoria->nome,
                    'novos_count' => $pendentes,
                    'concluidas_count' => $concluidosCat,
                    'total_count' => $total,
                    'progresso_percent' => $total > 0 ? round(($concluidosCat / $total) * 100) : 0,
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'paciente' => [
                'id' => $paciente->id,
                'nome' => $paciente->nome,
                'foto_url' => $paciente->foto_url,
            ],
            'progresso_geral' => [
                'total' => $totalExercicios,
                'concluidas' => $concluidosHoje,
                'porcentagem' => $progressoGeral,
            ],
            'categorias' => $categorias,
        ]);
    }
}