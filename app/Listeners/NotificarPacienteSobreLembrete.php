<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LembreteProximoDoHorario;
use App\Notifications\NotificarLembrete;
use App\Repositories\Contracts\LembreteRepositoryInterface;

/**
 * Notifica o dono do lembrete e marca o lembrete como já notificado, para que
 * o agendador não avise duas vezes no mesmo dia.
 */
class NotificarPacienteSobreLembrete
{
    public function __construct(
        private readonly LembreteRepositoryInterface $lembretes,
    ) {}

    /** Contrato do Laravel. */
    public function handle(LembreteProximoDoHorario $evento): void
    {
        $lembrete = $evento->lembrete;
        $paciente = $lembrete->usuario;

        // Lembrete desligado ou sem dono não gera aviso.
        if ($paciente === null || ! $lembrete->ativo) {
            return;
        }

        $paciente->notify(new NotificarLembrete($lembrete));

        $this->lembretes->marcarComoNotificado($lembrete);
    }
}
