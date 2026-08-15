<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Lembrete;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Disparado pelo agendador quando um lembrete ativo entra na janela do seu
 * horário. O Listener é quem decide notificar.
 */
class LembreteProximoDoHorario
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Lembrete $lembrete,
    ) {}
}
