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
        return $this->model->newQuery()->where('fonoaudiologo_id', $fonoaudiologoId)->latest()->get();
    }

    public function create(array $attributes): Model
    {
        return parent::create($attributes);
    }

    public function update(int $id, array $attributes): Model
    {
        return parent::update($id, $attributes);
    }

    public function getFormData(?Model $model = null): array
    {
        return [
            'paciente' => $model,
        ];
    }
}
