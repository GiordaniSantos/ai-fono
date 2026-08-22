<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\PrescricaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update']);

    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/prescricoes', [PrescricaoController::class, 'index']);
    Route::get('/prescricoes/{prescricao}', [PrescricaoController::class, 'show']);
    Route::post('/prescricoes/{prescricao}/concluir', [PrescricaoController::class, 'concluir']);
});
