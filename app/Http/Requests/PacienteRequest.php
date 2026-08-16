<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pacienteId = $this->route('paciente') instanceof \App\Models\Paciente ? $this->route('paciente')->id : $this->route('paciente');

        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('pacientes', 'email')->ignore($pacienteId),
            ],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date'],
            'diagnostico' => ['nullable', 'string'],
            'anexo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remover_anexo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do paciente é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado para outro paciente.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.date' => 'Informe uma data de nascimento válida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome do paciente',
            'data_nascimento' => 'data de nascimento',
            'diagnostico' => 'diagnóstico',
        ];
    }
}