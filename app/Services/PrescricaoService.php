<?php

namespace App\Services;

use App\Models\Exercicio;
use App\Models\Paciente;
use App\Models\Prescricao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PrescricaoService extends AbstractService
{
    public function __construct(Prescricao $model)
    {
        parent::__construct($model);
    }

    public function getAllByFonoaudiologo(int $fonoaudiologoId): Collection
    {
        return $this->model->newQuery()->with([
            'paciente.media',
            'exercicio.categoria',
        ])->where('fonoaudiologo_id', $fonoaudiologoId)->latest('data_inicio')->get();
    }

    public function getFormData(?Model $model = null): array
    {
        $fonoId = Auth::id();

        return [
            'prescricao' => $model ? $model->loadMissing(['paciente', 'exercicio']) : null,
            'pacientes'  => Paciente::where('fonoaudiologo_id', $fonoId)->select('id', 'nome')->orderBy('nome')->get(),
            'exercicios' => Exercicio::where('fonoaudiologo_id', $fonoId)->select('id', 'nome', 'paciente_id', 'categoria_id')->with('categoria:id,nome')->orderBy('nome')->get(),
        ];
    }

    public function toggleRealizada(Prescricao $prescricao): bool
    {
        return $prescricao->update([
            'realizada' => !$prescricao->realizada,
        ]);
    }
}
