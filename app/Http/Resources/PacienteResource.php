<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PacienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nome'           => $this->nome,
            'foto_url'       => $this->foto_url,
            'interesses'     => $this->interesses ?? [],
            'codigo_acesso'  => $this->codigo_acesso,
            'fonoaudiologo'  => [
                'id'   => $this->fonoaudiologo?->id,
                'nome' => $this->fonoaudiologo?->name,
            ],
        ];
    }
}
