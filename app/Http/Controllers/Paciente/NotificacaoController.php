<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Central de notificações do paciente (canal `database`).
 */
class NotificacaoController extends Controller
{
    public function index(Request $request): Response
    {
        $paciente = $request->user();

        return Inertia::render('Paciente/Notificacoes', [
            'notificacoes' => $paciente->notifications()
                ->latest()
                ->limit(50)
                ->get()
                ->map(fn (DatabaseNotification $notificacao): array => [
                    'id' => $notificacao->id,
                    'titulo' => $notificacao->data['titulo'] ?? 'Aviso',
                    'descricao' => $notificacao->data['descricao'] ?? '',
                    'icone' => $notificacao->data['icone'] ?? 'Circle',
                    'cor' => $notificacao->data['cor'] ?? 'teal',
                    'url' => $notificacao->data['url'] ?? null,
                    'quando' => $notificacao->created_at->diffForHumans(),
                    'lida' => $notificacao->read_at !== null,
                ])->values()->all(),
        ]);
    }

    public function marcarComoLida(Request $request, string $notificacao): RedirectResponse
    {
        // Busca dentro das notificações do próprio paciente: um id de outro
        // paciente simplesmente não é encontrado.
        $encontrada = $request->user()->notifications()->find($notificacao);

        if (! $encontrada instanceof DatabaseNotification) {
            throw new NotFoundHttpException('Notificação não encontrada.');
        }

        $encontrada->markAsRead();

        return back();
    }

    public function marcarTodasComoLidas(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'Todas as notificações foram marcadas como lidas.');
    }
}
