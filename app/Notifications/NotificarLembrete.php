<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Lembrete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso de que um lembrete está na hora.
 *
 * Vai pelos dois canais: `database` (fica na central de notificações) e
 * `broadcast` (chega na hora, via Reverb). Implementa ShouldQueue para não
 * segurar a requisição HTTP esperando o broadcast.
 *
 * Os métodos via/toDatabase/toBroadcast estão em inglês por serem contrato do
 * Laravel; a lógica dentro deles segue em português.
 */
class NotificarLembrete extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Lembrete $lembrete,
    ) {}

    /**
     * Contrato do Laravel.
     *
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Contrato do Laravel.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->montarPayloadDoLembrete();
    }

    /** Contrato do Laravel. */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->montarPayloadDoLembrete());
    }

    /**
     * O mesmo conteúdo alimenta a central de notificações e o aviso em tempo
     * real, para o paciente ver exatamente a mesma coisa nos dois lugares.
     *
     * @return array<string, mixed>
     */
    private function montarPayloadDoLembrete(): array
    {
        return [
            'tipo' => 'lembrete',
            'titulo' => "Hora de: {$this->lembrete->titulo}",
            'descricao' => "Seu lembrete das {$this->lembrete->horarioFormatado()} está próximo.",
            'icone' => 'Clock',
            'cor' => 'amber',
            'lembrete_id' => $this->lembrete->id,
            'url' => route('paciente.lembretes.index', absolute: false),
        ];
    }
}
