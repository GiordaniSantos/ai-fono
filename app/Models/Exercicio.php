<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercicio extends AbstractBaseModel
{
    use HasFactory;

    protected $table = 'exercicios';

    protected $fillable = [
        'fonoaudiologo_id',
        'paciente_id',
        'categoria_id',
        'nome',
        'descricao',
    ];

    protected $appends = [
        'imagens',
        'videos',
        'audios',
        'documentos',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_IMAGES);
        $this->addMediaCollection(self::COLLECTION_VIDEOS);
        $this->addMediaCollection(self::COLLECTION_AUDIOS);
        $this->addMediaCollection(self::COLLECTION_DOCS);
    }

    public function getImagensAttribute(): array
    {
        return $this->getMedia(self::COLLECTION_IMAGES)->map->getUrl($this::VERSION_MID)->toArray();
    }

    public function getVideosAttribute(): array
    {
        return $this->getMedia(self::COLLECTION_VIDEOS)->map->getUrl()->toArray();
    }

    public function getAudiosAttribute(): array
    {
        return $this->getMedia(self::COLLECTION_AUDIOS)->map->getUrl()->toArray();
    }

    public function getDocumentosAttribute(): array
    {
        return $this->getMedia(self::COLLECTION_DOCS)->map->getUrl()->toArray();
    }

    public function fonoaudiologo(): BelongsTo
    {
        return $this->belongsTo(Fonoaudiologo::class, 'fonoaudiologo_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function prescricoes(): BelongsToMany
    {
        return $this->belongsToMany(Prescricao::class, 'exercicio_prescricao', 'exercicio_id', 'prescricao_id')->withTimestamps();
    }

    public function execucoes(): HasMany
    {
        return $this->hasMany(ExecucaoExercicio::class, 'exercicio_id');
    }

    public function foiRealizadoHojePor(?int $pacienteId): bool
    {
        if (!$pacienteId) {
            return false;
        }

        $dataHoje = now()->format('Y-m-d');

        return $this->execucoes()->where('paciente_id', $pacienteId)->where('data_execucao', $dataHoje)->exists();
    }
}