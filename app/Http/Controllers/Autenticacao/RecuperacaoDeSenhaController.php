<?php

declare(strict_types=1);

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Fluxo "esqueci minha senha": pedir o link e redefinir com o token.
 */
class RecuperacaoDeSenhaController extends Controller
{
    public function mostrarFormularioDeSolicitacao(): Response
    {
        return Inertia::render('Autenticacao/SolicitarNovaSenha', [
            'status' => session('status'),
        ]);
    }

    public function enviarLink(Request $request): RedirectResponse
    {
        $request->validate(
            ['email' => 'required|email'],
            ['email.required' => 'Informe seu e-mail.', 'email.email' => 'Informe um e-mail válido.'],
        );

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => [trans($status)]]);
        }

        return back()->with('status', 'Enviamos um link de recuperação para o seu e-mail.');
    }

    public function mostrarFormularioDeRedefinicao(Request $request): Response
    {
        return Inertia::render('Autenticacao/RedefinirSenha', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    public function redefinir(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($usuario) use ($request): void {
                $usuario->forceFill([
                    'senha' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($usuario));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Senha alterada. Faça o acesso com a nova senha.');
        }

        throw ValidationException::withMessages(['email' => [trans($status)]]);
    }
}
