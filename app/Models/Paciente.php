<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Paciente extends Model
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
}