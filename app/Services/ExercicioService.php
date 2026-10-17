<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Exercicio;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
            'pacientes' => Paciente::where('fonoaudiologo_id', Auth::id())->select('id', 'nome', 'data_nascimento', 'diagnostico', 'interesses')->orderBy('nome')->get(),
            'categorias' => Categoria::where('fonoaudiologo_id', Auth::id())->select('id', 'nome')->orderBy('nome')->get(),
        ];
    }

    public function create(array $data): Exercicio
    {
        return DB::transaction(function () use ($data) {
            $arquivos = $data['arquivos'] ?? [];
            unset($data['arquivos']);

            /** @var Exercicio $exercicio */
            $exercicio = $this->model->create($data);

            $this->processarArquivos($exercicio, $arquivos);

            return $exercicio;
        });
    }

    public function update(int $id, array $data): Exercicio
    {
        $exercicio = $this->model->findOrFail($id);

        return DB::transaction(function () use ($exercicio, $data) {
            $arquivos = $data['arquivos'] ?? [];
            $removerMidias = $data['remover_midias'] ?? [];

            unset($data['arquivos'], $data['remover_midias']);

            $exercicio->update($data);

            if (!empty($removerMidias)) {
                Media::whereIn('id', $removerMidias)->delete();
            }

            $this->processarArquivos($exercicio, $arquivos);

            return $exercicio;
        });
    }

    private function processarArquivos(Exercicio $exercicio, array $arquivos): void
    {
        foreach ($arquivos as $arquivo) {
            if (!$arquivo instanceof UploadedFile) {
                continue;
            }

            $collection = $this->determinarColecao($arquivo);

            $exercicio->addMedia($arquivo)
                ->toMediaCollection($collection);
        }
    }

    private function determinarColecao(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/')) {
            return Exercicio::COLLECTION_IMAGES;
        }

        if (str_starts_with($mime, 'video/')) {
            return Exercicio::COLLECTION_VIDEOS;
        }

        if (str_starts_with($mime, 'audio/')) {
            return Exercicio::COLLECTION_AUDIOS;
        }

        return Exercicio::COLLECTION_DOCS;
    }
}
