<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Orientacao;
use App\Services\Paciente\OrientacaoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrientacaoController extends Controller
{
    public function __construct(
        private readonly OrientacaoService $orientacoes,
    ) {}

    public function index(Request $request): Response
    {
        $paciente = $request->user();

        return Inertia::render('Paciente/Orientacoes/Index', [
            'dispositivo' => $paciente->dispositivoAtual()?->nome,
            'orientacoes' => $this->orientacoes->listarParaPaciente($paciente)
                ->map(fn (Orientacao $orientacao): array => [
                    'id' => $orientacao->id,
                    'titulo' => $orientacao->titulo,
                    'subtitulo' => $orientacao->subtitulo,
                ])->values()->all(),
        ]);
    }

    /**
     * O Service garante que a orientação pertence ao dispositivo do paciente —
     * trocar o id na URL não dá acesso a orientação de outro dispositivo.
     */
    public function show(Request $request, int $orientacao): Response
    {
        $encontrada = $this->orientacoes->detalharParaPaciente($request->user(), $orientacao);

        return Inertia::render('Paciente/Orientacoes/Detalhe', [
            'orientacao' => [
                'id' => $encontrada->id,
                'titulo' => $encontrada->titulo,
                'subtitulo' => $encontrada->subtitulo,
                'abas' => $encontrada->tiposDeCuidado->map(fn ($aba): array => [
                    'id' => $aba->id,
                    'nome' => $aba->nome,
                    'passos' => $aba->passos->pluck('descricao')->all(),
                ])->values()->all(),
            ],
        ]);
    }
}
