<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecucaoPrescricao extends AbstractBaseModel
{
    use HasFactory;
    protected $table = 'execucoes_prescricoes';

    protected $fillable = [
        'paciente_id',
        'prescricao_id',
        'data_execucao',
        'comentario_fono',
        'nivel_satisfacao',
    ];

    protected $casts = [
        'data_execucao' => 'date:Y-m-d',
        'nivel_satisfacao' => 'integer',
    ];

    protected $appends = [
        'arquivo_url',
        'tipo_arquivo',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_ANEXO)->singleFile();
    }

    public function getArquivoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl(self::COLLECTION_ANEXO) ?: null;
    }

    public function getTipoArquivoAttribute(): ?string
    {
        $media = $this->getFirstMedia(self::COLLECTION_ANEXO);
        if (!$media) {
            return null;
        }

        $extensao = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
        $mime = strtolower($media->mime_type);

        if (in_array($extensao, ['m4a', 'mp3', 'wav', 'aac', 'ogg', 'flac', 'oga'])) {
            return 'audio';
        }

        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }

        if ($mime === 'video/mp4' && $extensao === 'm4a') {
            return 'audio';
        }

        return 'video';
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function prescricao(): BelongsTo
    {
        return $this->belongsTo(Prescricao::class, 'prescricao_id');
    }
}