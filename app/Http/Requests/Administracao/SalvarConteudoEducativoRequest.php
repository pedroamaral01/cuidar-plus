<?php

declare(strict_types=1);

namespace App\Http\Requests\Administracao;

use App\Enums\TipoDeConteudo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalvarConteudoEducativoRequest extends FormRequest
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
            'resumo' => ['nullable', 'string', 'max:255'],
            'tipo' => ['required', Rule::enum(TipoDeConteudo::class)],
            // Vídeo exige URL; texto exige corpo. Um conteúdo sem nenhum dos
            // dois chegaria vazio ao paciente.
            'url_do_video' => [
                Rule::requiredIf(fn (): bool => $this->input('tipo') === TipoDeConteudo::Video->value),
                'nullable',
                'url',
                'max:255',
            ],
            'corpo' => [
                Rule::requiredIf(fn (): bool => $this->input('tipo') === TipoDeConteudo::Texto->value),
                'nullable',
                'string',
            ],
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
            'titulo.required' => 'Informe o título do conteúdo.',
            'tipo.required' => 'Escolha se é texto ou vídeo.',
            'url_do_video.required' => 'Informe o endereço do vídeo.',
            'url_do_video.url' => 'Informe um endereço válido, começando com http.',
            'corpo.required' => 'Escreva o conteúdo do texto.',
        ];
    }
}
