<?php 
namespace App\Services;

use App\Interfaces\ServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AbstractService implements ServiceInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(string $orderBy = 'created_at', string $orderDirection = 'desc'): Collection
    {
        return $this->model->orderBy($orderBy, $orderDirection)->get();
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): Model
    {
        $entity = $this->find($id);
        $entity->update($attributes);
        return $entity;
    }

    public function find(int $id): ?Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function getFormData(?Model $model = null): array
    {
        return [];
    }
}
