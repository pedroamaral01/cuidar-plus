<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administracao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administracao\SalvarOrientacaoRequest;
use App\Models\Dispositivo;
use App\Models\Orientacao;
use App\Services\Administracao\OrientacaoAdministrativaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrientacaoController extends Controller
{
    public function __construct(
        private readonly OrientacaoAdministrativaService $orientacoes,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Administracao/Orientacoes/Index', [
            'orientacoes' => Orientacao::with('dispositivo')
                ->withCount('tiposDeCuidado as tipos_de_cuidado')
                ->orderBy('titulo')
                ->get()
                ->map(fn (Orientacao $orientacao): array => [
                    'id' => $orientacao->id,
                    'titulo' => $orientacao->titulo,
                    'dispositivo' => $orientacao->dispositivo?->nome ?? '—',
                    'tiposDeCuidado' => $orientacao->tipos_de_cuidado,
                    'publicada' => $orientacao->publicada,
                    'atualizadoEm' => $orientacao->updated_at?->format('d/m/Y'),
                ])->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Administracao/Orientacoes/Formulario', [
            'orientacao' => null,
            'dispositivos' => $this->dispositivosDisponiveis(),
        ]);
    }

    public function store(SalvarOrientacaoRequest $request): RedirectResponse
    {
        $this->orientacoes->criarOrientacao($request->validated());

        return redirect()
            ->route('administracao.orientacoes.index')
            ->with('status', 'Orientação cadastrada.');
    }

    public function edit(Orientacao $orientacao): Response
    {
        $orientacao->load('tiposDeCuidado.passos');

        return Inertia::render('Administracao/Orientacoes/Formulario', [
            'orientacao' => [
                'id' => $orientacao->id,
                'dispositivo_id' => $orientacao->dispositivo_id,
                'titulo' => $orientacao->titulo,
                'subtitulo' => $orientacao->subtitulo,
                'publicada' => $orientacao->publicada,
                'abas' => $orientacao->tiposDeCuidado->map(fn ($aba): array => [
                    'nome' => $aba->nome,
                    'passos' => $aba->passos->pluck('descricao')->all(),
                ])->all(),
            ],
            'dispositivos' => $this->dispositivosDisponiveis(),
        ]);
    }

    public function update(SalvarOrientacaoRequest $request, Orientacao $orientacao): RedirectResponse
    {
        $this->orientacoes->atualizarOrientacao($orientacao, $request->validated());

        return redirect()
            ->route('administracao.orientacoes.index')
            ->with('status', 'Orientação atualizada.');
    }

    public function destroy(Orientacao $orientacao): RedirectResponse
    {
        $this->orientacoes->removerOrientacao($orientacao);

        return redirect()
            ->route('administracao.orientacoes.index')
            ->with('status', 'Orientação excluída.');
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
