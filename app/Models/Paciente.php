<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Paciente extends AbstractBaseModel
{
    use HasFactory;

    protected $fillable = [
        'fonoaudiologo_id',
        'nome',
        'email',
        'telefone',
        'data_nascimento',
        'diagnostico',
        'codigo_acesso',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    protected $appends = [
        'anexo_url',
    ];

    protected static function booted(): void
    {
        static::creating(function (Paciente $paciente) {
            if (empty($paciente->codigo_acesso)) {
                $paciente->codigo_acesso = self::gerarCodigoAcessoUnico();
            }
        });
    }

    public static function gerarCodigoAcessoUnico(): string
    {
        do {
            $codigo = strtoupper(Str::random(6));
        } while (self::where('codigo_acesso', $codigo)->exists());

        return $codigo;
    }

    public function fonoaudiologo(): BelongsTo
    {
        return $this->belongsTo(Fonoaudiologo::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_ANEXO)->singleFile();
    }

    public function getAnexoUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia(self::COLLECTION_ANEXO);
        return $media ? $media->getUrl() : null;
    }

}