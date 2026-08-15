<?php

declare(strict_types=1);

namespace App\Http\Requests\Administracao;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalvarDispositivoRequest extends FormRequest
{
    /** O middleware `administrador` já protege o grupo de rotas. */
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
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dispositivos', 'nome')->ignore($this->route('dispositivo')),
            ],
            'descricao' => ['nullable', 'string', 'max:255'],
            'icone' => ['required', 'string', 'max:40'],
            'cor' => ['required', Rule::in(['teal', 'amber', 'coral', 'lavender'])],
            'ativo' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do dispositivo.',
            'nome.unique' => 'Já existe um dispositivo com esse nome.',
            'icone.required' => 'Escolha um ícone.',
            'cor.in' => 'Escolha uma cor da paleta do sistema.',
        ];
    }
}
