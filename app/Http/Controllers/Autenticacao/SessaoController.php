<?php

declare(strict_types=1);

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Autenticacao\AcessoRequest;
use App\Support\DestinoPorPerfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class SessaoController extends Controller
{
    public function mostrarFormulario(): Response
    {
        return Inertia::render('Autenticacao/Acesso', [
            'podeRedefinirSenha' => Route::has('senha.solicitar'),
            'status' => session('status'),
        ]);
    }

    public function entrar(AcessoRequest $request): RedirectResponse
    {
        $request->autenticar();

        // Troca o id da sessão após o login, contra fixação de sessão.
        $request->session()->regenerate();

        return redirect()->intended(DestinoPorPerfil::rotaInicial($request->user()));
    }

    public function sair(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
