<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\SinalDeAlerta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso de que a equipe publicou um novo sinal de alerta relevante para o
 * paciente. Mesmos dois canais: database + broadcast (Reverb).
 */
class NotificarNovoSinalDeAlerta extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SinalDeAlerta $sinalDeAlerta,
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
        return $this->montarPayloadDoSinal();
    }

    /** Contrato do Laravel. */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->montarPayloadDoSinal());
    }

    /**
     * @return array<string, mixed>
     */
    private function montarPayloadDoSinal(): array
    {
        return [
            'tipo' => 'alerta',
            'titulo' => 'Novo sinal de alerta cadastrado',
            'descricao' => "A equipe adicionou orientações sobre {$this->sinalDeAlerta->nome}.",
            'icone' => 'AlertTriangle',
            'cor' => $this->sinalDeAlerta->gravidade->cor(),
            'sinal_de_alerta_id' => $this->sinalDeAlerta->id,
            'url' => route('paciente.alertas', absolute: false),
        ];
    }
}
