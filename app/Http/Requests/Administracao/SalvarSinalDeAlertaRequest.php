<?php

declare(strict_types=1);

namespace App\Http\Requests\Administracao;

use App\Enums\GravidadeDoAlerta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalvarSinalDeAlertaRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:255'],
            'orientacao' => ['required', 'string', 'max:2000'],
            'gravidade' => ['required', Rule::enum(GravidadeDoAlerta::class)],
            // Nulo = sinal geral, exibido para todos os pacientes.
            'dispositivo_id' => ['nullable', 'exists:dispositivos,id'],
            'publicado' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do sinal de alerta.',
            'orientacao.required' => 'Descreva o que o paciente deve fazer.',
            'gravidade.required' => 'Selecione a gravidade.',
        ];
    }
}
