<?php

declare(strict_types=1);

namespace App\Http\Requests\Administracao;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A orientação chega junto com suas abas e passos, em um único envio — é
 * assim que o formulário administrativo funciona.
 */
class SalvarOrientacaoRequest extends FormRequest
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
            'dispositivo_id' => ['required', 'exists:dispositivos,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'subtitulo' => ['nullable', 'string', 'max:255'],
            'publicada' => ['boolean'],

            // Uma orientação sem aba não tem o que mostrar ao paciente.
            'abas' => ['required', 'array', 'min:1'],
            'abas.*.nome' => ['required', 'string', 'max:255'],
            'abas.*.passos' => ['required', 'array', 'min:1'],
            'abas.*.passos.*' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dispositivo_id.required' => 'Selecione o dispositivo desta orientação.',
            'titulo.required' => 'Informe o título da orientação.',
            'abas.required' => 'Cadastre ao menos um tipo de cuidado.',
            'abas.min' => 'Cadastre ao menos um tipo de cuidado.',
            'abas.*.nome.required' => 'Dê um nome ao tipo de cuidado.',
            'abas.*.passos.required' => 'Cadastre ao menos um passo.',
            'abas.*.passos.min' => 'Cadastre ao menos um passo.',
            'abas.*.passos.*.required' => 'O passo não pode ficar em branco.',
        ];
    }
}
