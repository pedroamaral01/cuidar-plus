<?php

declare(strict_types=1);

namespace App\Http\Requests\Autenticacao;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AcessoRequest extends FormRequest
{
    /** Contrato do FormRequest — a tela de acesso é pública. */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Contrato do FormRequest.
     *
     * O campo continua sendo `password` porque é a chave que
     * `Auth::attempt()` trata de forma especial (comparação por hash).
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'Informe sua senha.',
        ];
    }

    /**
     * @throws ValidationException
     */
    public function autenticar(): void
    {
        $this->garantirQueNaoExcedeuTentativas();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->chaveDeTentativas());

            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }

        RateLimiter::clear($this->chaveDeTentativas());
    }

    /**
     * Rate limiting do login: 6 tentativas por minuto, conforme a seção 11.
     *
     * @throws ValidationException
     */
    public function garantirQueNaoExcedeuTentativas(): void
    {
        if (! RateLimiter::tooManyAttempts($this->chaveDeTentativas(), 6)) {
            return;
        }

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->chaveDeTentativas());

        throw ValidationException::withMessages([
            'email' => "Muitas tentativas de acesso. Tente novamente em {$segundos} segundos.",
        ]);
    }

    public function chaveDeTentativas(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
