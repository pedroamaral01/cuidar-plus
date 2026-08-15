<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administracao;

use App\Http\Controllers\Controller;
use App\Models\ConteudoEducativo;
use App\Models\Dispositivo;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PainelController extends Controller
{
    public function mostrar(): Response
    {
        return Inertia::render('Administracao/Painel', [
            'estatisticas' => [
                'pacientesAtivos' => Usuario::pacientes()->where('ativo', true)->count(),
                'dispositivosCadastrados' => Dispositivo::ativos()->count(),
                'notificacoesHoje' => DB::table('notifications')->whereDate('created_at', today())->count(),
                'conteudosPublicados' => ConteudoEducativo::publicados()->count(),
            ],
        ]);
    }
}
