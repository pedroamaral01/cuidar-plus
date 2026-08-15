<?php

declare(strict_types=1);

use App\Http\Controllers\Administracao\ConteudoEducativoController;
use App\Http\Controllers\Administracao\DispositivoController;
use App\Http\Controllers\Administracao\NotificacaoEnviadaController;
use App\Http\Controllers\Administracao\OrientacaoController;
use App\Http\Controllers\Administracao\PacienteController;
use App\Http\Controllers\Administracao\PainelController;
use App\Http\Controllers\Administracao\SinalDeAlertaController;
use App\Http\Controllers\Paciente\InicioController;
use App\Support\DestinoPorPerfil;
use Illuminate\Support\Facades\Route;

/**
 * A raiz manda cada um para o seu lugar: visitante para o acesso, paciente
 * para o Início, administrador para o painel.
 */
Route::get('/', function () {
    return redirect(DestinoPorPerfil::rotaInicial(request()->user()));
})->name('raiz');

// ---------------------------------------------------------------------------
// Área do paciente
// ---------------------------------------------------------------------------
Route::middleware(['auth', 'paciente'])
    ->prefix('inicio')
    ->name('paciente.')
    ->group(function (): void {
        Route::get('/', [InicioController::class, 'mostrar'])->name('inicio');
    });

// ---------------------------------------------------------------------------
// Área administrativa
// ---------------------------------------------------------------------------
Route::middleware(['auth', 'administrador'])
    ->prefix('administracao')
    ->name('administracao.')
    ->group(function (): void {
        Route::get('/', [PainelController::class, 'mostrar'])->name('painel');

        // Visualização apenas — o MVP não prevê editar paciente pelo admin.
        Route::get('pacientes', [PacienteController::class, 'index'])->name('pacientes.index');

        Route::resource('dispositivos', DispositivoController::class)->except(['show']);

        // O singularizador do Laravel é inglês: "orientacoes" viraria
        // "orientacoe", "alertas" viraria "alerta" (ok) e "conteudos" viraria
        // "conteudo" (ok). Onde o resultado não bate com o parâmetro do
        // Controller, o model binding não acontece — por isso os nomes
        // explícitos abaixo.
        Route::resource('orientacoes', OrientacaoController::class)
            ->except(['show'])
            ->parameters(['orientacoes' => 'orientacao']);
        Route::resource('alertas', SinalDeAlertaController::class)
            ->except(['show'])
            ->parameters(['alertas' => 'alerta']);
        Route::resource('conteudos', ConteudoEducativoController::class)
            ->except(['show'])
            ->parameters(['conteudos' => 'conteudo']);

        Route::get('notificacoes', [NotificacaoEnviadaController::class, 'index'])->name('notificacoes');
    });

require __DIR__.'/auth.php';
