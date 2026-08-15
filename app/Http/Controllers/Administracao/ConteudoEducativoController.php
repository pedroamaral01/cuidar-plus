<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administracao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administracao\SalvarConteudoEducativoRequest;
use App\Models\ConteudoEducativo;
use App\Models\Dispositivo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConteudoEducativoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Administracao/Conteudos/Index', [
            'conteudos' => ConteudoEducativo::with('dispositivo')
                ->withCount('favoritadoPor as favoritos')
                ->orderBy('titulo')
                ->get()
                ->map(fn (ConteudoEducativo $conteudo): array => [
                    'id' => $conteudo->id,
                    'titulo' => $conteudo->titulo,
                    'tipo' => $conteudo->tipo->value,
                    'tipoRotulo' => $conteudo->tipo->rotulo(),
                    'dispositivo' => $conteudo->dispositivo?->nome ?? 'Todos',
                    'publicado' => $conteudo->publicado,
                    'favoritos' => $conteudo->favoritos,
                ])->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Administracao/Conteudos/Formulario', [
            'conteudo' => null,
            'dispositivos' => $this->dispositivosDisponiveis(),
        ]);
    }

    public function store(SalvarConteudoEducativoRequest $request): RedirectResponse
    {
        ConteudoEducativo::create($request->validated());

        return redirect()
            ->route('administracao.conteudos.index')
            ->with('status', 'Conteúdo cadastrado.');
    }

    public function edit(ConteudoEducativo $conteudo): Response
    {
        return Inertia::render('Administracao/Conteudos/Formulario', [
            'conteudo' => [
                'id' => $conteudo->id,
                'titulo' => $conteudo->titulo,
                'resumo' => $conteudo->resumo,
                'corpo' => $conteudo->corpo,
                'tipo' => $conteudo->tipo->value,
                'url_do_video' => $conteudo->url_do_video,
                'dispositivo_id' => $conteudo->dispositivo_id,
                'publicado' => $conteudo->publicado,
            ],
            'dispositivos' => $this->dispositivosDisponiveis(),
        ]);
    }

    public function update(SalvarConteudoEducativoRequest $request, ConteudoEducativo $conteudo): RedirectResponse
    {
        $conteudo->update($request->validated());

        return redirect()
            ->route('administracao.conteudos.index')
            ->with('status', 'Conteúdo atualizado.');
    }

    public function destroy(ConteudoEducativo $conteudo): RedirectResponse
    {
        $conteudo->delete();

        return redirect()
            ->route('administracao.conteudos.index')
            ->with('status', 'Conteúdo excluído.');
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
