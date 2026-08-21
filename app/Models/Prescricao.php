<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescricao extends AbstractBaseModel
{
    use HasFactory;

    protected $table = 'prescricoes';

    protected $fillable = [
        'fonoaudiologo_id',
        'paciente_id',
        'exercicio_id',
        'data_inicio',
        'data_fim',
        'frequencia_diaria',
        'realizada',
    ];

    protected $casts = [
        'data_inicio' => 'date:Y-m-d',
        'data_fim'  => 'date:Y-m-d',
        'frequencia_diaria' => 'integer',
        'realizada' => 'boolean',
    ];

    protected $appends = [
        'is_vigente',
        'status_vigencia', // 'vigente' | 'futura' | 'expirada'
    ];

    public function getIsVigenteAttribute(): bool
    {
        $hoje = Carbon::today();
        $inicio = Carbon::parse($this->data_inicio)->startOfDay();
        $fim = $this->data_fim ? Carbon::parse($this->data_fim)->endOfDay() : null;

        if ($hoje->lt($inicio)) {
            return false;
        }

        if ($fim && $hoje->gt($fim)) {
            return false;
        }

        return true;
    }

    public function getStatusVigenciaAttribute(): string
    {
        $hoje = Carbon::today();
        $inicio = Carbon::parse($this->data_inicio)->startOfDay();
        $fim = $this->data_fim ? Carbon::parse($this->data_fim)->endOfDay() : null;

        if ($hoje->lt($inicio)) {
            return 'futura';
        }

        if ($fim && $hoje->gt($fim)) {
            return 'expirada';
        }

        return 'vigente';
    }

    public function fonoaudiologo(): BelongsTo
    {
        return $this->belongsTo(Fonoaudiologo::class, 'fonoaudiologo_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function exercicio(): BelongsTo
    {
        return $this->belongsTo(Exercicio::class, 'exercicio_id');
    }
}
