<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\SinalDeAlerta;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Disparado quando a administração publica um novo sinal de alerta. Só os
 * pacientes afetados (do dispositivo do sinal, ou todos se ele for geral)
 * recebem a notificação.
 */
class SinalDeAlertaCadastrado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly SinalDeAlerta $sinalDeAlerta,
    ) {}
}
