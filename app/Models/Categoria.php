<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends AbstractBaseModel
{
    protected $fillable = [
        'fonoaudiologo_id',
        'nome',
    ];

    public function fonoaudiologo(): BelongsTo
    {
        return $this->belongsTo(Fonoaudiologo::class);
    }

    public function exercicios(): HasMany
    {
        return $this->hasMany(Exercicio::class);
    }
}
