<?php

namespace App\Services;

use App\Models\Exercicio;
use App\Models\Paciente;
use App\Models\Prescricao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'exercicios.categoria',
        ])->where('fonoaudiologo_id', $fonoaudiologoId)
          ->latest('data_inicio')
          ->get();
    }

    public function getFormData(?Model $model = null): array
    {
        $fonoId = Auth::id();

        return [
            'prescricao' => $model ? $model->loadMissing(['paciente', 'exercicios.categoria']) : null,
            'pacientes'  => Paciente::where('fonoaudiologo_id', $fonoId)->select('id', 'nome')->orderBy('nome')->get(),
            'exercicios' => Exercicio::where('fonoaudiologo_id', $fonoId)->select('id', 'nome', 'paciente_id', 'categoria_id')->with('categoria:id,nome')->orderBy('nome')->get(),
        ];
    }

    public function create(array $data): Prescricao
    {
        return DB::transaction(function () use ($data) {
            $exerciciosIds = $data['exercicios'] ?? [];
            unset($data['exercicios']);

            /** @var Prescricao $prescricao */
            $prescricao = $this->model->create($data);

            if (!empty($exerciciosIds)) {
                $prescricao->exercicios()->sync($exerciciosIds);
            }

            return $prescricao;
        });
    }

    public function update(int $id, array $data): Prescricao
    {
        $prescricao = $this->model->findOrFail($id);

        return DB::transaction(function () use ($prescricao, $data) {
            $exerciciosIds = $data['exercicios'] ?? [];
            unset($data['exercicios']);

            $prescricao->update($data);

            $prescricao->exercicios()->sync($exerciciosIds);

            return $prescricao;
        });
    }

    public function toggleRealizada(Prescricao $prescricao): bool
    {
        return $prescricao->update([
            'realizada' => !$prescricao->realizada,
        ]);
    }
}