<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

/**
 * A cada minuto o sistema verifica quais lembretes entraram na hora e dispara
 * as notificações. `withoutOverlapping` evita duas varreduras concorrentes se
 * uma execução demorar mais que um minuto.
 */
Schedule::command('cuidar:avisar-lembretes')
    ->everyMinute()
    ->withoutOverlapping();
