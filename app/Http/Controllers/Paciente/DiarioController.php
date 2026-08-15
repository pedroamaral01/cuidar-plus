<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Enums\TipoDeLembrete;
use App\Http\Controllers\Controller;
use App\Http\Requests\Paciente\RegistrarCuidadoRequest;
use App\Models\RegistroDeCuidado;
use App\Services\Paciente\RegistroDeCuidadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiarioController extends Controller
{
    public function __construct(
        private readonly RegistroDeCuidadoService $registrosDeCuidados,
    ) {}

    public function mostrar(Request $request): Response
    {
        $paciente = $request->user();

        return Inertia::render('Paciente/Diario', [
            'primeiroNome' => explode(' ', trim($paciente->nome))[0],
            'resumoDaSemana' => $this->registrosDeCuidados->montarResumoDaSemana($paciente)->paraArray(),
            'historico' => $this->registrosDeCuidados->buscarHistoricoPorPaciente($paciente)
                ->map(fn (RegistroDeCuidado $registro): array => [
                    'id' => $registro->id,
                    'titulo' => $registro->titulo,
                    'tipo' => $registro->tipo->value,
                    'cor' => $registro->tipo->cor(),
                    'observacao' => $registro->observacao,
                    'quando' => $this->descreverQuando($registro),
                ])->values()->all(),
            'tipos' => array_map(
                fn (TipoDeLembrete $tipo): array => [
                    'valor' => $tipo->value,
                    'rotulo' => $tipo->rotulo(),
                ],
                TipoDeLembrete::cases(),
            ),
        ]);
    }

    public function registrar(RegistrarCuidadoRequest $request): RedirectResponse
    {
        $validado = $request->validated();

        $this->registrosDeCuidados->registrarCuidado(
            $request->user(),
            $validado['titulo'],
            TipoDeLembrete::from($validado['tipo']),
            $validado['observacao'] ?? null,
        );

        return back()->with('status', 'Cuidado registrado no seu diário.');
    }

    /** "Hoje · 08:10", "Ontem · 20:30" ou "12/08 · 16:00". */
    private function descreverQuando(RegistroDeCuidado $registro): string
    {
        $quando = $registro->realizado_em;
        $hora = $quando->format('H:i');

        return match (true) {
            $quando->isToday() => "Hoje · {$hora}",
            $quando->isYesterday() => "Ontem · {$hora}",
            default => $quando->format('d/m')." · {$hora}",
        };
    }
}
