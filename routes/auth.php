<?php

declare(strict_types=1);

use App\Http\Controllers\Autenticacao\CadastroDePacienteController;
use App\Http\Controllers\Autenticacao\RecuperacaoDeSenhaController;
use App\Http\Controllers\Autenticacao\SenhaController;
use App\Http\Controllers\Autenticacao\SessaoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    // Acesso
    Route::get('entrar', [SessaoController::class, 'mostrarFormulario'])->name('login');
    // O limite que o paciente enxerga são 6 tentativas com senha errada por
    // minuto, aplicado no AcessoRequest — ele devolve uma mensagem no
    // formulário em vez de uma página 429. O throttle da rota é só uma
    // barreira contra flood, com folga para não disparar antes daquele.
    Route::post('entrar', [SessaoController::class, 'entrar'])
        ->middleware('throttle:30,1')
        ->name('login.entrar');

    // Cadastro do paciente em 3 passos
    Route::get('cadastro', [CadastroDePacienteController::class, 'mostrarFormulario'])->name('cadastro.criar');
    Route::post('cadastro', [CadastroDePacienteController::class, 'salvar'])->name('cadastro.salvar');

    // Recuperação de senha
    Route::get('esqueci-a-senha', [RecuperacaoDeSenhaController::class, 'mostrarFormularioDeSolicitacao'])
        ->name('senha.solicitar');
    Route::post('esqueci-a-senha', [RecuperacaoDeSenhaController::class, 'enviarLink'])
        ->middleware('throttle:6,1')
        ->name('senha.enviar-link');
    Route::get('redefinir-senha/{token}', [RecuperacaoDeSenhaController::class, 'mostrarFormularioDeRedefinicao'])
        ->name('senha.redefinir');
    Route::post('redefinir-senha', [RecuperacaoDeSenhaController::class, 'redefinir'])
        ->name('senha.atualizar');
});

Route::middleware('auth')->group(function (): void {
    Route::put('senha', [SenhaController::class, 'atualizar'])->name('senha.trocar');
    Route::post('sair', [SessaoController::class, 'sair'])->name('logout');
});
