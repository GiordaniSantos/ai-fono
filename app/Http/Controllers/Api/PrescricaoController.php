<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrescricaoDetalheResource;
use App\Models\ExecucaoPrescricao;
use App\Models\Prescricao;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescricaoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $paciente = $request->user();
        $categoriaNome = $request->query('categoria');

        $query = Prescricao::with(['exercicio.categoria', 'exercicio.media'])
            ->where('paciente_id', $paciente->id);

        if ($categoriaNome && $categoriaNome !== 'Todas') {
            $query->whereHas('exercicio.categoria', function ($q) use ($categoriaNome) {
                $q->where('nome', $categoriaNome);
            });
        }

        $prescricoes = $query->orderBy('realizada', 'asc')
            ->orderBy('data_fim', 'asc')
            ->get()
            ->map(function ($p) {
                $diasRestantes = 0;
                if ($p->data_fim) {
                    $fim = Carbon::parse($p->data_fim)->endOfDay();
                    $diasRestantes = max(0, Carbon::today()->diffInDays($fim, false));
                }

                return [
                    'id' => $p->id,
                    'titulo' => $p->exercicio?->nome ?? 'Exercício sem título',
                    'subtitulo' => $p->exercicio?->subtitulo ?? $p->exercicio?->descricao_curta ?? '',
                    'tipo' => $p->exercicio?->tipo ?? 'Texto',
                    'categoria' => $p->exercicio?->categoria?->nome ?? 'Geral',
                    'concluido' => (bool) $p->realizada,
                    'dias_restantes' => (int) $diasRestantes,
                ];
            });

        return response()->json([
            'data' => $prescricoes,
        ]);
    }

    public function show(Request $request, Prescricao $prescricao): JsonResponse
    {
        if ($prescricao->paciente_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $prescricao->load(['exercicio.categoria', 'exercicio.media']);

        return response()->json([
            'data' => new PrescricaoDetalheResource($prescricao),
        ]);
    }

    public function concluir(Request $request, Prescricao $prescricao): JsonResponse
    {
        if ($prescricao->paciente_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }
    
        $validated = $request->validate([
            'nivel_satisfacao' => 'nullable|integer|min:1|max:5',
            'arquivo' => [
                'nullable',
                'file',
                'mimes:mp3,wav,m4a,mp4,mov,webm,ogg,aac,m4v,qt',
                'max:51200',
            ],
        ]);

        $hoje = Carbon::today()->toDateString();

        $execucao = ExecucaoPrescricao::updateOrCreate(
            [
                'prescricao_id' => $prescricao->id,
                'data_execucao' => $hoje,
            ],
            [
                'paciente_id'      => $request->user()->id,
                'nivel_satisfacao' => $validated['nivel_satisfacao'] ?? null,
            ]
        );

        if ($request->hasFile('arquivo')) {
            $execucao->addMediaFromRequest('arquivo')->toMediaCollection('anexo');
        }

        $prescricao->update([
            'realizada' => true,
        ]);

        $prescricao->load(['exercicio.categoria', 'exercicio.media', 'execucaoHoje.media']);

        return response()->json([
            'message' => 'Exercício registrado com sucesso!',
            'data' => [
                'prescricao_id' => $prescricao->id,
                'realizada' => (bool) $prescricao->realizada,
                'execucao' => [
                    'id' => $execucao->id,
                    'data_execucao' => $execucao->data_execucao->format('Y-m-d'),
                    'arquivo_url' => $execucao->arquivo_url,
                    'tipo_arquivo' => $execucao->tipo_arquivo,
                ],
            ],
        ], 200);
    }
}