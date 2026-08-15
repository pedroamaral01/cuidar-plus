<?php

declare(strict_types=1);

use App\Http\Controllers\Administracao\ConteudoEducativoController;
use App\Http\Controllers\Administracao\DispositivoController;
use App\Http\Controllers\Administracao\NotificacaoEnviadaController;
use App\Http\Controllers\Administracao\OrientacaoController;
use App\Http\Controllers\Administracao\PacienteController;
use App\Http\Controllers\Administracao\PainelController;
use App\Http\Controllers\Administracao\SinalDeAlertaController;
use App\Http\Controllers\Paciente\ConteudoEducativoController as ConteudoDoPacienteController;
use App\Http\Controllers\Paciente\DiarioController;
use App\Http\Controllers\Paciente\EquipeController;
use App\Http\Controllers\Paciente\PerfilController;
use App\Http\Controllers\Paciente\DispositivoController as DispositivoDoPacienteController;
use App\Http\Controllers\Paciente\InicioController;
use App\Http\Controllers\Paciente\LembreteController;
use App\Http\Controllers\Paciente\NotificacaoController;
use App\Http\Controllers\Paciente\OrientacaoController as OrientacaoDoPacienteController;
use App\Http\Controllers\Paciente\SinalDeAlertaController as SinalDeAlertaDoPacienteController;
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

        // Meu dispositivo
        Route::get('meu-dispositivo', [DispositivoDoPacienteController::class, 'mostrar'])->name('dispositivo');
        Route::put('meu-dispositivo', [DispositivoDoPacienteController::class, 'definir'])->name('dispositivo.definir');

        // Orientações
        Route::get('orientacoes', [OrientacaoDoPacienteController::class, 'index'])->name('orientacoes.index');
        Route::get('orientacoes/{orientacao}', [OrientacaoDoPacienteController::class, 'show'])->name('orientacoes.show');

        // Sinais de alerta
        Route::get('sinais-de-alerta', [SinalDeAlertaDoPacienteController::class, 'index'])->name('alertas');

        // Lembretes
        Route::get('lembretes', [LembreteController::class, 'index'])->name('lembretes.index');
        Route::post('lembretes', [LembreteController::class, 'store'])->name('lembretes.store');
        Route::put('lembretes/{lembrete}', [LembreteController::class, 'update'])->name('lembretes.update');
        Route::patch('lembretes/{lembrete}/alternar', [LembreteController::class, 'alternar'])->name('lembretes.alternar');
        Route::post('lembretes/{lembrete}/concluir', [LembreteController::class, 'concluir'])->name('lembretes.concluir');
        Route::delete('lembretes/{lembrete}', [LembreteController::class, 'destroy'])->name('lembretes.destroy');

        // Diário / histórico
        Route::get('diario', [DiarioController::class, 'mostrar'])->name('diario');
        Route::post('diario', [DiarioController::class, 'registrar'])->name('diario.registrar');

        // Conteúdos educativos
        Route::get('conteudos', [ConteudoDoPacienteController::class, 'index'])->name('conteudos');
        Route::get('conteudos/{conteudo}', [ConteudoDoPacienteController::class, 'show'])->name('conteudos.show');
        Route::post('conteudos/{conteudo}/favorito', [ConteudoDoPacienteController::class, 'alternarFavorito'])
            ->name('conteudos.favorito');

        // Perfil
        Route::get('perfil', [PerfilController::class, 'mostrar'])->name('perfil');
        Route::put('perfil', [PerfilController::class, 'atualizar'])->name('perfil.atualizar');

        // Central de notificações
        Route::get('notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes');
        Route::patch('notificacoes/lidas', [NotificacaoController::class, 'marcarTodasComoLidas'])
            ->name('notificacoes.todas-lidas');
        Route::patch('notificacoes/{notificacao}', [NotificacaoController::class, 'marcarComoLida'])
            ->name('notificacoes.lida');

        // Falar com a equipe — somente o ponto de entrada (ver seção 5.4).
        Route::get('falar-com-a-equipe', [EquipeController::class, 'mostrar'])->name('equipe');
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
