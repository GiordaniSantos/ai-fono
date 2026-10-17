<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExecucaoExercicioController;
use App\Http\Controllers\ExercicioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PrescricaoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('pacientes', PacienteController::class);
    Route::resource('categorias', CategoriaController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('/exercicios/gerar-ia', [ExercicioController::class, 'gerarDescricaoIa'])->name('exercicios.gerar-ia');
    Route::resource('exercicios', ExercicioController::class);

    Route::resource('prescricoes', PrescricaoController::class)->parameters([
        'prescricoes' => 'prescricao',
    ]);

    Route::get('/execucoes', [ExecucaoExercicioController::class, 'index'])->name('execucoes.index');
    Route::patch('/execucoes/{execucao}/comentario', [ExecucaoExercicioController::class, 'updateComentario'])->name('execucoes.comentario');
});

Route::get('/dashboard2', fn () => Inertia::render('Dashboard2'))->name('dashboard2');

require __DIR__.'/auth.php';
