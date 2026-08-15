<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SinalDeAlertaCadastrado;
use App\Notifications\NotificarNovoSinalDeAlerta;
use App\Repositories\Contracts\SinalDeAlertaRepositoryInterface;
use Illuminate\Support\Facades\Notification;

/**
 * Avisa os pacientes alcançados por um novo sinal de alerta: os que usam o
 * dispositivo do sinal, ou todos os pacientes se ele for geral.
 */
class NotificarPacientesSobreSinalDeAlerta
{
    public function __construct(
        private readonly SinalDeAlertaRepositoryInterface $sinais,
    ) {}

    /** Contrato do Laravel. */
    public function handle(SinalDeAlertaCadastrado $evento): void
    {
        $sinal = $evento->sinalDeAlerta;

        // Sinal despublicado não chega a ninguém.
        if (! $sinal->publicado) {
            return;
        }

        $pacientes = $this->sinais->buscarPacientesAfetados($sinal->dispositivo_id);

        if ($pacientes->isEmpty()) {
            return;
        }

        Notification::send($pacientes, new NotificarNovoSinalDeAlerta($sinal));
    }
}
