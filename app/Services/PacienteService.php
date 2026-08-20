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

    public function findByCodigoAcesso(string $codigo): ?Paciente
    {
        $codigoLimpo = trim(strtoupper($codigo));

        return $this->model->newQuery()->where('codigo_acesso', $codigoLimpo)->with(['fonoaudiologo:id,name'])->first();
    }

    public function create(array $attributes): Model
    {
        $foto = $attributes['foto'] ?? null;
        $anexo = $attributes['anexo'] ?? null;
        unset($attributes['foto'], $attributes['anexo']);

        $model = parent::create($attributes);

        if ($foto) {
            $model->addMedia($foto)->toMediaCollection(Paciente::COLLECTION_FOTO ?? 'foto');
        }

        if ($anexo) {
            $model->addMedia($anexo)->toMediaCollection(Paciente::COLLECTION_ANEXO ?? 'anexo');
        }

        return $model;
    }

    public function update(int $id, array $attributes): Model
    {
        $foto = $attributes['foto'] ?? null;
        $removerFoto = filter_var($attributes['remover_foto'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $anexo = $attributes['anexo'] ?? null;
        $removerAnexo = filter_var($attributes['remover_anexo'] ?? false, FILTER_VALIDATE_BOOLEAN);

        unset(
            $attributes['foto'],
            $attributes['remover_foto'],
            $attributes['anexo'],
            $attributes['remover_anexo']
        );

        $model = parent::update($id, $attributes);

        $collectionFoto = $model::COLLECTION_FOTO;
        $collectionAnexo = $model::COLLECTION_ANEXO;

        if ($removerFoto || $foto) {
            $model->clearMediaCollection($collectionFoto);
        }
        if ($foto) {
            $model->addMedia($foto)->toMediaCollection($collectionFoto);
        }

        if ($removerAnexo || $anexo) {
            $model->clearMediaCollection($collectionAnexo);
        }
        if ($anexo) {
            $model->addMedia($anexo)->toMediaCollection($collectionAnexo);
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
