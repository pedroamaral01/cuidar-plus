<?php

declare(strict_types=1);

namespace App\Http\Requests\Paciente;

use App\Enums\TipoDeLembrete;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalvarLembreteRequest extends FormRequest
{
    /** A posse do lembrete é verificada pela LembretePolicy no Controller. */
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
            'horario' => ['required', 'date_format:H:i'],
            'ativo' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'Dê um nome ao lembrete.',
            'tipo.required' => 'Escolha o tipo de cuidado.',
            'horario.required' => 'Informe o horário.',
            'horario.date_format' => 'Informe o horário no formato 08:00.',
        ];
    }
}
