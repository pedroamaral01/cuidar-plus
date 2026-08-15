<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administracao;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Auditoria simples das notificações disparadas pelo sistema (seção 2.3).
 */
class NotificacaoEnviadaController extends Controller
{
    public function index(): Response
    {
        $nomesPorId = Usuario::pluck('nome', 'id');

        $notificacoes = DB::table('notifications')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function (object $notificacao) use ($nomesPorId): array {
                $dados = json_decode((string) $notificacao->data, true) ?: [];

                return [
                    'id' => $notificacao->id,
                    'destinatario' => $nomesPorId[$notificacao->notifiable_id] ?? 'Paciente removido',
                    'tipo' => $dados['tipo'] ?? class_basename($notificacao->type),
                    'titulo' => $dados['titulo'] ?? '—',
                    // As notificações do sistema usam sempre os dois canais.
                    'canal' => 'database + broadcast',
                    'enviadoEm' => Carbon::parse($notificacao->created_at)->format('d/m/Y H:i'),
                    'lida' => $notificacao->read_at !== null,
                ];
            })->all();

        return Inertia::render('Administracao/Notificacoes/Index', [
            'notificacoes' => $notificacoes,
        ]);
    }
}
