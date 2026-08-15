<?php

declare(strict_types=1);

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Troca de senha de quem já está logado (tela de perfil).
 */
class SenhaController extends Controller
{
    public function atualizar(Request $request): RedirectResponse
    {
        $validado = $request->validate(
            [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ],
            [
                'current_password.required' => 'Informe sua senha atual.',
                'current_password.current_password' => 'A senha atual está incorreta.',
                'password.confirmed' => 'A confirmação da nova senha não confere.',
            ],
        );

        $request->user()->update([
            'senha' => Hash::make($validado['password']),
        ]);

        return back()->with('status', 'Senha atualizada.');
    }
}
