<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PacienteResource;
use App\Models\Paciente;
use App\Services\PacienteService;
use App\Http\Requests\Api\PacienteRequest;
use Illuminate\Http\JsonResponse;

class PacienteController extends Controller
{
    public function __construct(
        protected PacienteService $pacienteService
    ) {}

    public function update(PacienteRequest $request, Paciente $paciente): JsonResponse
    {
        $pacienteAtualizado = $this->pacienteService->update($paciente->id, $request->validated());

        return response()->json([
            'message' => 'Perfil do paciente atualizado com sucesso!',
            'data' => new PacienteResource($pacienteAtualizado)
        ], 200);
    }
}