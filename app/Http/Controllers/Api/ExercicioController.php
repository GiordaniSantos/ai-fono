<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercicio;
use App\Models\Prescricao;
use App\Models\ExecucaoExercicio;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExercicioController extends Controller
{
 
    public function index(Request $request): JsonResponse
    {
        $paciente = $request->user();
        $hoje = Carbon::today();
        $categoriaNome = $request->query('categoria');

        $prescricoesVigentes = Prescricao::with([
                'exercicios' => function ($query) {
                    $query->select('exercicios.id', 'exercicios.nome', 'exercicios.descricao', 'exercicios.categoria_id', 'exercicios.fonoaudiologo_id');
                },
                'exercicios.categoria'
            ])
            ->where('paciente_id', $paciente->id)
            ->where('data_inicio', '<=', $hoje)
            ->where(function ($query) use ($hoje) {
                $query->whereNull('data_fim')
                    ->orWhere('data_fim', '>=', $hoje);
            })
            ->get();

        $listaExercicios = [];
        $exerciciosProcessados = [];

        foreach ($prescricoesVigentes as $prescricao) {
            $diasRestantes = 0;
            if ($prescricao->data_fim) {
                $fim = Carbon::parse($prescricao->data_fim)->endOfDay();
                $diasRestantes = max(0, $hoje->diffInDays($fim, false));
            }

            foreach ($prescricao->exercicios as $exercicio) {
                if (in_array($exercicio->id, $exerciciosProcessados)) {
                    continue;
                }

                if ($categoriaNome && $categoriaNome !== 'Todas') {
                    if ($exercicio->categoria?->nome !== $categoriaNome) {
                        continue;
                    }
                }

                $exerciciosProcessados[] = $exercicio->id;

                $listaExercicios[] = [
                    'id' => $exercicio->id,
                    'titulo' => $exercicio->nome ?? 'Exercício',
                    'categoria' => $exercicio->categoria?->nome ?? 'Geral',
                    'concluido' => $exercicio->foiRealizadoHojePor($paciente->id),
                    'dias_restantes' => (int) $diasRestantes,
                ];
            }
        }

        return response()->json([
            'data' => $listaExercicios,
        ]);
    }

    public function show(Request $request, Exercicio $exercicio): JsonResponse
    {
        $pacienteId = $request->user()?->id;

        $exercicio->load(['categoria', 'media']);
        return response()->json([
            'data' => [
                'id' => $exercicio->id,
                'nome' => $exercicio->nome,
                'descricao' => $exercicio->descricao,
                'categoria' => $exercicio->categoria?->nome,
                'imagens' => $exercicio->imagens,
                'videos' => $exercicio->videos,
                'audios' => $exercicio->audios,
                'realizado_hoje' => $exercicio->foiRealizadoHojePor($pacienteId),
            ],
        ]);
    }

    public function concluir(Request $request, Exercicio $exercicio): JsonResponse
    {
        $paciente = $request->user();

        $temAcesso = Prescricao::where('paciente_id', $paciente->id)
            ->whereHas('exercicios', function ($query) use ($exercicio) {
                $query->where('exercicios.id', $exercicio->id);
            })
            ->exists();

        if (!$temAcesso) {
            return response()->json(['message' => 'Acesso não autorizado a este exercício.'], 403);
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

        $execucao = ExecucaoExercicio::updateOrCreate(
            [
                'paciente_id'   => $paciente->id,
                'exercicio_id'  => $exercicio->id,
                'data_execucao' => $hoje,
            ],
            [
                'nivel_satisfacao' => $validated['nivel_satisfacao'] ?? null,
            ]
        );

        if ($request->hasFile('arquivo')) {
            $execucao->addMediaFromRequest('arquivo')->toMediaCollection('anexo');
        }

        $execucao->load(['exercicio.categoria', 'exercicio.media']);

        return response()->json([
            'message' => 'Exercício registrado com sucesso!',
            'data' => [
                'exercicio_id' => $exercicio->id,
                'realizado_hoje' => true,
                'execucao' => [
                    'id' => $execucao->id,
                    'data_execucao' => $execucao->data_execucao?->format('Y-m-d') ?? $hoje,
                    'arquivo_url' => $execucao->arquivo_url,
                    'tipo_arquivo' => $execucao->tipo_arquivo,
                ],
            ],
        ], 200);
    }
}