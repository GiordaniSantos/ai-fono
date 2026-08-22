<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Prescricao;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $paciente = $request->user();
        $hoje = Carbon::today();

        $prescricoesVigentes = Prescricao::with(['exercicio.categoria'])
            ->where('paciente_id', $paciente->id)
            ->where('data_inicio', '<=', $hoje)
            ->where(function ($query) use ($hoje) {
                $query->whereNull('data_fim')
                      ->orWhere('data_fim', '>=', $hoje);
            })
            ->get();

        $totalPrescricoes = $prescricoesVigentes->count();
        $concluidas = $prescricoesVigentes->where('realizada', true)->count();
        $progressoGeral = $totalPrescricoes > 0 
            ? round(($concluidas / $totalPrescricoes) * 100) 
            : 0;

        $categorias = Categoria::where('fonoaudiologo_id', $paciente->fonoaudiologo_id)
            ->get()
            ->map(function ($categoria) use ($prescricoesVigentes) {
                $prescricoesDaCategoria = $prescricoesVigentes->filter(function ($p) use ($categoria) {
                    return $p->exercicio?->categoria_id === $categoria->id;
                });

                $total = $prescricoesDaCategoria->count();
                $concluidasCat = $prescricoesDaCategoria->where('realizada', true)->count();
                $novas = $prescricoesDaCategoria->where('realizada', false)->count();

                return [
                    'id' => $categoria->id,
                    'nome' => $categoria->nome,
                    'novos_count' => $novas,
                    'concluidas_count' => $concluidasCat,
                    'total_count' => $total,
                    'progresso_percent' => $total > 0 ? round(($concluidasCat / $total) * 100) : 0,
                ];
            })
            ->filter(fn($cat) => $cat['total_count'] > 0)
            ->values();

        return response()->json([
            'paciente' => [
                'id' => $paciente->id,
                'nome' => $paciente->nome,
                'foto_url' => $paciente->foto_url,
            ],
            'progresso_geral' => [
                'total' => $totalPrescricoes,
                'concluidas' => $concluidas,
                'porcentagem' => $progressoGeral,
            ],
            'categorias' => $categorias,
        ]);
    }
}