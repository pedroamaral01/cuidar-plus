<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * "Falar com a equipe" — apenas o ponto de entrada.
 *
 * O mecanismo real de contato (chat em tempo real, e-mail, ticket, WhatsApp
 * Business) ainda NÃO foi decidido. Por isso aqui existe só a rota e a tela.
 * Não há Event, Notification, Model de mensagem nem tabela de conversa: isso
 * é decisão em aberto, não esquecimento (seção 5.4 da especificação).
 */
class EquipeController extends Controller
{
    public function mostrar(): Response
    {
        return Inertia::render('Paciente/Equipe');
    }
}
