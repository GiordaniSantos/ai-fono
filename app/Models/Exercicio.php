<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}