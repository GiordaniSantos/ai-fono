<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PacienteResource;
use App\Services\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        protected PacienteService $pacienteService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'codigo_acesso' => ['required', 'string'],
        ]);

        $paciente = $this->pacienteService->findByCodigoAcesso($request->input('codigo_acesso'));

        if (!$paciente) {
            return response()->json([
                'message' => 'Código de acesso inválido ou paciente não encontrado.',
            ], 422);
        }

        $token = $paciente->createToken('app-paciente')->plainTextToken;

        return response()->json([
            'paciente' => new PacienteResource($paciente),
            'token'    => $token,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'paciente' => new PacienteResource($request->user()->loadMissing('fonoaudiologo:id,name')),
        ]);
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}