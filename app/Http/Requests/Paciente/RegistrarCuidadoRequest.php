<?php

declare(strict_types=1);

namespace App\Http\Requests\Paciente;

use App\Enums\TipoDeLembrete;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarCuidadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::enum(TipoDeLembrete::class)],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'Descreva o cuidado realizado.',
            'tipo.required' => 'Escolha o tipo de cuidado.',
        ];
    }
}
