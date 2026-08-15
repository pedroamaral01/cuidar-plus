<?php

declare(strict_types=1);

use App\Http\Controllers\Administracao\PainelController;
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
    });

require __DIR__.'/auth.php';
