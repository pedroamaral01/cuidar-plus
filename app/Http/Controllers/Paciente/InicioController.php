<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Services\Paciente\PainelDoPacienteService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InicioController extends Controller
{
    public function __construct(
        private readonly PainelDoPacienteService $painel,
    ) {}

    public function mostrar(Request $request): Response
    {
        return Inertia::render('Paciente/Inicio', [
            'painel' => $this->painel->montarPainel($request->user()),
        ]);
    }
}
