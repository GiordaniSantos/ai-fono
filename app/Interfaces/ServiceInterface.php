<?php
namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ServiceInterface
{
    public function getAll(string $orderBy, string $orderDirection): Collection;
    public function create(array $attributes): Model|null;
    public function delete(int $id): bool;
    public function find(int $id): Model|null;
    public function update(int $id, array $attributes): Model;
    public function getFormData(?Model $model = null): array;
}
