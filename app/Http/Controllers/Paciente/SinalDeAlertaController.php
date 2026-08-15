<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\SinalDeAlerta;
use App\Services\Paciente\SinalDeAlertaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SinalDeAlertaController extends Controller
{
    public function __construct(
        private readonly SinalDeAlertaService $sinais,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Paciente/Alertas', [
            'sinais' => $this->sinais->listarParaPaciente($request->user())
                ->map(fn (SinalDeAlerta $sinal): array => [
                    'id' => $sinal->id,
                    'nome' => $sinal->nome,
                    'orientacao' => $sinal->orientacao,
                    'gravidade' => $sinal->gravidade->value,
                    'gravidadeRotulo' => $sinal->gravidade->rotulo(),
                    'cor' => $sinal->gravidade->cor(),
                ])->values()->all(),
        ]);
    }
}
