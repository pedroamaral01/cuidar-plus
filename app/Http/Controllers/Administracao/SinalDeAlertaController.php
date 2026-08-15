<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administracao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administracao\SalvarSinalDeAlertaRequest;
use App\Models\Dispositivo;
use App\Models\SinalDeAlerta;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SinalDeAlertaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Administracao/Alertas/Index', [
            'sinais' => SinalDeAlerta::with('dispositivo')
                ->orderByRaw("FIELD(gravidade, 'alta', 'media', 'baixa')")
                ->orderBy('nome')
                ->get()
                ->map(fn (SinalDeAlerta $sinal): array => [
                    'id' => $sinal->id,
                    'nome' => $sinal->nome,
                    'gravidade' => $sinal->gravidade->value,
                    'gravidadeRotulo' => $sinal->gravidade->rotulo(),
                    'gravidadeCor' => $sinal->gravidade->cor(),
                    'dispositivo' => $sinal->dispositivo?->nome ?? 'Todos',
                    'publicado' => $sinal->publicado,
                ])->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Administracao/Alertas/Formulario', [
            'sinal' => null,
            'dispositivos' => $this->dispositivosDisponiveis(),
        ]);
    }

    public function store(SalvarSinalDeAlertaRequest $request): RedirectResponse
    {
        SinalDeAlerta::create($request->validated());

        return redirect()
            ->route('administracao.alertas.index')
            ->with('status', 'Sinal de alerta cadastrado.');
    }

    public function edit(SinalDeAlerta $alerta): Response
    {
        return Inertia::render('Administracao/Alertas/Formulario', [
            'sinal' => [
                'id' => $alerta->id,
                'nome' => $alerta->nome,
                'orientacao' => $alerta->orientacao,
                'gravidade' => $alerta->gravidade->value,
                'dispositivo_id' => $alerta->dispositivo_id,
                'publicado' => $alerta->publicado,
            ],
            'dispositivos' => $this->dispositivosDisponiveis(),
        ]);
    }

    public function update(SalvarSinalDeAlertaRequest $request, SinalDeAlerta $alerta): RedirectResponse
    {
        $alerta->update($request->validated());

        return redirect()
            ->route('administracao.alertas.index')
            ->with('status', 'Sinal de alerta atualizado.');
    }

    public function destroy(SinalDeAlerta $alerta): RedirectResponse
    {
        $alerta->delete();

        return redirect()
            ->route('administracao.alertas.index')
            ->with('status', 'Sinal de alerta excluído.');
    }

    /**
     * @return list<array{valor: int, rotulo: string}>
     */
    private function dispositivosDisponiveis(): array
    {
        return Dispositivo::ativos()
            ->orderBy('nome')
            ->get()
            ->map(fn (Dispositivo $dispositivo): array => [
                'valor' => $dispositivo->id,
                'rotulo' => $dispositivo->nome,
            ])->all();
    }
}
