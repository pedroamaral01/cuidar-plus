<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\ConteudoEducativo;
use App\Services\Paciente\ConteudoEducativoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConteudoEducativoController extends Controller
{
    public function __construct(
        private readonly ConteudoEducativoService $conteudos,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Paciente/Conteudos/Index', [
            'conteudos' => $this->conteudos->listarParaPaciente($request->user())
                ->map(fn (ConteudoEducativo $conteudo): array => [
                    'id' => $conteudo->id,
                    'titulo' => $conteudo->titulo,
                    'resumo' => $conteudo->resumo,
                    'tipo' => $conteudo->tipo->value,
                    'tipoRotulo' => $conteudo->tipo->rotulo(),
                    'icone' => $conteudo->tipo->icone(),
                    'cor' => $conteudo->tipo->cor(),
                    'favorito' => (bool) $conteudo->favorito,
                ])->values()->all(),
        ]);
    }

    /**
     * Detalhe de um conteúdo. Passa pelo mesmo filtro de visibilidade da
     * listagem — id na URL não dá acesso a conteúdo de outro dispositivo.
     */
    public function show(Request $request, int $conteudo): Response
    {
        $paciente = $request->user();

        $encontrado = $this->conteudos->listarParaPaciente($paciente)
            ->firstWhere('id', $conteudo);

        if (! $encontrado instanceof ConteudoEducativo) {
            throw new NotFoundHttpException('Conteúdo indisponível.');
        }

        return Inertia::render('Paciente/Conteudos/Detalhe', [
            'conteudo' => [
                'id' => $encontrado->id,
                'titulo' => $encontrado->titulo,
                'resumo' => $encontrado->resumo,
                'corpo' => $encontrado->corpo,
                'tipo' => $encontrado->tipo->value,
                'tipoRotulo' => $encontrado->tipo->rotulo(),
                'urlDoVideo' => $encontrado->url_do_video,
                'favorito' => (bool) $encontrado->favorito,
            ],
        ]);
    }

    public function alternarFavorito(Request $request, int $conteudo): RedirectResponse
    {
        $favoritado = $this->conteudos->alternarFavorito($request->user(), $conteudo);

        return back()->with(
            'status',
            $favoritado ? 'Conteúdo salvo nos favoritos.' : 'Conteúdo removido dos favoritos.',
        );
    }
}
