<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExercicioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => ['nullable', 'exists:pacientes,id'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.exists' => 'O paciente selecionado não existe.',
            'categoria_id.exists' => 'A categoria selecionada é inválida.',
            'nome.required' => 'O nome do exercício é obrigatório.',
            'nome.max' => 'O nome do exercício não pode ultrapassar 255 caracteres.',
        ];
    }
}
