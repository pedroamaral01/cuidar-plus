<?php

declare(strict_types=1);

namespace App\Http\Requests\Paciente;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtualizarPerfilRequest extends FormRequest
{
    /** O paciente só edita o próprio perfil: a rota já usa o usuário da sessão. */
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
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Usuario::class)->ignore($this->user()->id),
            ],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_de_nascimento' => ['nullable', 'date', 'before:today'],
            'cuidador_nome' => ['nullable', 'string', 'max:255'],
            'cuidador_telefone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'Informe seu nome.',
            'email.required' => 'Informe seu e-mail.',
            'email.unique' => 'Já existe uma conta com esse e-mail.',
            'data_de_nascimento.before' => 'A data de nascimento precisa ser no passado.',
        ];
    }
}
