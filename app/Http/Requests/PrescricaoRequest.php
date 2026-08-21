<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PrescricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fonoId = Auth::id();

        return [
            'paciente_id' => [
                'required',
                'integer',
                Rule::exists('pacientes', 'id')->where('fonoaudiologo_id', $fonoId),
            ],
            'exercicio_id' => [
                'required',
                'integer',
                Rule::exists('exercicios', 'id')->where(function ($query) use ($fonoId) {
                    $query->where('fonoaudiologo_id', $fonoId);
                }),
            ],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after_or_equal:data_inicio'],
            'frequencia_diaria' => ['required', 'integer', 'min:1', 'max:20'],
            'realizada'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required'  => 'Selecione o paciente.',
            'paciente_id.exists'    => 'Paciente inválido ou não encontrado.',
            'exercicio_id.required' => 'Selecione o exercício a ser prescrito.',
            'exercicio_id.exists'   => 'Exercício inválido ou não encontrado.',
            'data_inicio.required'  => 'A data de início é obrigatória.',
            'data_inicio.date'      => 'Informe uma data de início válida.',
            'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data de início.',
            'frequencia_diaria.required' => 'Informe a frequência diária.',
            'frequencia_diaria.min'      => 'A frequência diária deve ser de pelo menos 1 vez ao dia.',
        ];
    }
}
