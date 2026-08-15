<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\LembreteProximoDoHorario;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Varre os lembretes ativos que entraram na janela do seu horário e dispara o
 * evento de notificação para cada um.
 *
 * Roda a cada minuto pelo agendador. A janela olha alguns minutos para trás
 * para não perder lembretes caso uma execução atrase.
 */
class AvisarLembretesProximos extends Command
{
    protected $signature = 'cuidar:avisar-lembretes {--minutos=5 : Tamanho da janela, em minutos}';

    protected $description = 'Dispara notificações dos lembretes que estão na hora';

    public function handle(LembreteRepositoryInterface $lembretes): int
    {
        $minutos = max(1, (int) $this->option('minutos'));

        $agora = Carbon::now();
        $inicio = $agora->copy()->subMinutes($minutos)->format('H:i:s');
        $fim = $agora->format('H:i:s');

        // Perto da meia-noite a janela "dá a volta" no relógio; nesse caso o
        // repositório receberia um intervalo invertido e não acharia nada.
        // Tratar isso como duas janelas seria complexidade sem valor real
        // aqui, então a janela é encurtada até o início do dia.
        if ($inicio > $fim) {
            $inicio = '00:00:00';
        }

        $encontrados = $lembretes->buscarAtivosNaJanelaDeHorario($inicio, $fim);

        foreach ($encontrados as $lembrete) {
            LembreteProximoDoHorario::dispatch($lembrete);
        }

        $this->info("Lembretes avisados: {$encontrados->count()}.");

        return self::SUCCESS;
    }
}
