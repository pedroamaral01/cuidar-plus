<?php

declare(strict_types=1);

namespace App\Http\Requests\Autenticacao;

use App\DTOs\DadosDoCadastroDePaciente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CadastroDePacienteRequest extends FormRequest
{
    /** Contrato do FormRequest — o cadastro é público. */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Contrato do FormRequest. Cobre os 3 passos do cadastro de uma vez, já
     * que o formulário é enviado inteiro ao concluir.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Passo 1
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'data_de_nascimento' => ['nullable', 'date', 'before:today'],
            'cpf' => ['nullable', 'string', 'max:14'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'cuidador_nome' => ['nullable', 'string', 'max:255'],
            'cuidador_telefone' => ['nullable', 'string', 'max:20'],

            // Passo 2 — só dispositivos ativos podem ser escolhidos.
            'dispositivo_id' => [
                'required',
                Rule::exists('dispositivos', 'id')->where('ativo', true),
            ],

            // Passo 3
            'aplicar_plano_de_cuidados' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'Informe seu nome completo.',
            'email.required' => 'Informe seu e-mail.',
            'email.unique' => 'Já existe uma conta com esse e-mail.',
            'email.lowercase' => 'Use apenas letras minúsculas no e-mail.',
            'password.required' => 'Escolha uma senha.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'data_de_nascimento.before' => 'A data de nascimento precisa ser no passado.',
            'dispositivo_id.required' => 'Selecione o dispositivo que você utiliza.',
            'dispositivo_id.exists' => 'Dispositivo indisponível. Escolha outro.',
        ];
    }

    public function paraDto(): DadosDoCadastroDePaciente
    {
        return DadosDoCadastroDePaciente::apartirDoFormulario($this->validated());
    }
}
