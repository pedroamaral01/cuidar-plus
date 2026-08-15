<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Enums\TipoDeLembrete;
use App\Http\Controllers\Controller;
use App\Http\Requests\Paciente\SalvarLembreteRequest;
use App\Models\Lembrete;
use App\Services\Paciente\AgendaDeLembretesService;
use App\Services\Paciente\LembreteService;
use App\Services\Paciente\RegistroDeCuidadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LembreteController extends Controller
{
    public function __construct(
        private readonly LembreteService $lembretes,
        private readonly AgendaDeLembretesService $agenda,
        private readonly RegistroDeCuidadoService $registrosDeCuidados,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Paciente/Lembretes', [
            'agenda' => $this->agenda->montarAgendaDoDia($request->user(), $request->query('dia')),
            'tipos' => $this->tiposDisponiveis(),
        ]);
    }

    public function store(SalvarLembreteRequest $request): RedirectResponse
    {
        $this->lembretes->criarLembrete($request->user(), $request->validated());

        return back()->with('status', 'Lembrete criado.');
    }

    public function update(SalvarLembreteRequest $request, Lembrete $lembrete): RedirectResponse
    {
        $this->authorize('update', $lembrete);

        $this->lembretes->atualizarLembrete($lembrete, $request->validated());

        return back()->with('status', 'Lembrete atualizado.');
    }

    /** Liga/desliga o lembrete sem abrir formulário. */
    public function alternar(Lembrete $lembrete): RedirectResponse
    {
        $this->authorize('update', $lembrete);

        $ativo = $this->lembretes->alternarAtivacao($lembrete);

        return back()->with('status', $ativo ? 'Lembrete ligado.' : 'Lembrete desligado.');
    }

    /**
     * "Marcar como feito": vira um registro no diário, herdando título e tipo
     * do lembrete.
     */
    public function concluir(Lembrete $lembrete): RedirectResponse
    {
        $this->authorize('update', $lembrete);

        $this->registrosDeCuidados->registrarCuidadoDoLembrete($lembrete);

        return back()->with('status', 'Cuidado registrado no seu diário.');
    }

    public function destroy(Lembrete $lembrete): RedirectResponse
    {
        $this->authorize('delete', $lembrete);

        $this->lembretes->removerLembrete($lembrete);

        return back()->with('status', 'Lembrete removido.');
    }

    /**
     * @return list<array{valor: string, rotulo: string}>
     */
    private function tiposDisponiveis(): array
    {
        return array_map(
            fn (TipoDeLembrete $tipo): array => [
                'valor' => $tipo->value,
                'rotulo' => $tipo->rotulo(),
            ],
            TipoDeLembrete::cases(),
        );
    }
}
