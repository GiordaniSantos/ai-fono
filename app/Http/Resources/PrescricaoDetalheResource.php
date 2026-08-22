<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescricaoDetalheResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $exercicio = $this->exercicio;

        return [
            'id' => $this->id,
            'frequencia_diaria' => (int) ($this->frequencia_diaria ?? 1),
            'realizacoes_hoje' => (int) ($this->realizacoes_hoje ?? ($this->realizada ? 1 : 0)),
            'data_fim' => $this->data_fim ? $this->data_fim->format('Y-m-d') : null,
            'realizada' => (bool) $this->realizada,
            'is_vigente' => (bool) $this->is_vigente,
            'exercicio' => [
                'id' => $exercicio?->id,
                'titulo' => $exercicio?->titulo ?? 'Exercício de Treino',
                'categoria' => $exercicio?->categoria?->nome ?? 'Geral',
                'tipo' => $exercicio?->tipo ?? 'Treino Prático',
                'instrucoes' => $exercicio?->instrucoes ?? 'Execute as repetições conforme orientações.',
                'texto' => $exercicio?->texto ?? $exercicio?->descricao ?? '',
                'imagem_url' => $exercicio?->imagem_url ?? $exercicio?->getFirstMediaUrl('imagem') ?: null,
                'video_url' => $exercicio?->video_url ?? $exercicio?->getFirstMediaUrl('video') ?: null,
                'video_thumb_url' => $exercicio?->video_thumb_url ?? null,
            ],
        ];
    }
}