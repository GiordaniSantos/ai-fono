<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categoria') instanceof \App\Models\Categoria
            ? $this->route('categoria')->id
            : $this->route('categoria');

        return [
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categorias', 'nome')
                    ->where('fonoaudiologo_id', Auth::id())
                    ->ignore($categoriaId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique'   => 'Você já possui uma categoria com este nome.',
            'nome.max'      => 'O nome não pode ter mais de 255 caracteres.',
        ];
    }
}