<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExercicioController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PacienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update']);

    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/exercicios', [ExercicioController::class, 'index']);
    Route::get('/exercicios/{exercicio}', [ExercicioController::class, 'show']);
    Route::post('/exercicios/{exercicio}/concluir', [ExercicioController::class, 'concluir']);
});
