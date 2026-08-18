<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Exercicio;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExercicioService extends AbstractService
{
    public function __construct(Exercicio $model)
    {
        parent::__construct($model);
    }

    public function getAllByFonoaudiologo(int $fonoaudiologoId): Collection
    {
        return $this->model->newQuery()
            ->with(['paciente', 'categoria'])
            ->where('fonoaudiologo_id', $fonoaudiologoId)
            ->latest()
            ->get();
    }

    public function getFormData(?Model $model = null): array
    {
        return [
            'exercicio' => $model ? $model->loadMissing('paciente') : null,
            'pacientes' => Paciente::where('fonoaudiologo_id', Auth::id())->select('id', 'nome')->orderBy('nome')->get(),
            'categorias' => Categoria::where('fonoaudiologo_id', Auth::id())->select('id', 'nome')->orderBy('nome')->get(),
        ];
    }
}