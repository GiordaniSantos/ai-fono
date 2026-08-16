<?php 
namespace App\Services;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PacienteService extends AbstractService
{
    public function __construct(Paciente $model)
    {
        parent::__construct($model);
    }

    public function getAllByFonoaudiologo(int $fonoaudiologoId): Collection
    {
        return $this->model->newQuery()->with('media')->where('fonoaudiologo_id', $fonoaudiologoId)->latest()->get();
    }

    public function create(array $attributes): Model
    {
        $model = parent::create($attributes);

        if (isset($attributes['anexo'])) {
            $model->addMedia($attributes['anexo'])->toMediaCollection('anexo');
        }

        return $model;
    }

    public function update(int $id, array $attributes): Model
    {
        $anexo = $attributes['anexo'] ?? null;
        $removerAnexo = filter_var($attributes['remover_anexo'] ?? false, FILTER_VALIDATE_BOOLEAN);
        unset($attributes['anexo'], $attributes['remover_anexo']);

        $model = parent::update($id, $attributes);

        if ($removerAnexo) {
            $model->clearMediaCollection($model::COLLECTION_ANEXO);
        }

        if ($anexo) {
            $model->addMedia($anexo)->toMediaCollection($model::COLLECTION_ANEXO);
        }

        return $model;
    }


    public function getFormData(?Model $model = null): array
    {
        return [
            'paciente' => $model,
        ];
    }
}
