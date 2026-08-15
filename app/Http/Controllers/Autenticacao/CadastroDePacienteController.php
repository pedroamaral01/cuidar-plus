<?php

declare(strict_types=1);

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Autenticacao\CadastroDePacienteRequest;
use App\Models\Dispositivo;
use App\Services\Autenticacao\CadastroDePacienteService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CadastroDePacienteController extends Controller
{
    public function __construct(
        private readonly CadastroDePacienteService $cadastro,
    ) {}

    /**
     * Passo 2 e 3 do formulário precisam da lista de dispositivos ativos e do
     * plano sugerido de cada um — tudo vem do banco, nunca fixo no React.
     */
    public function mostrarFormulario(): Response
    {
        $dispositivos = Dispositivo::ativos()
            ->with(['planosDeCuidados' => fn ($consulta) => $consulta->where('ativo', true)->with('itens')])
            ->orderBy('id')
            ->get();

        return Inertia::render('Autenticacao/Cadastro', [
            'dispositivos' => $dispositivos->map(fn (Dispositivo $dispositivo): array => [
                'id' => $dispositivo->id,
                'nome' => $dispositivo->nome,
                'icone' => $dispositivo->icone,
                'cor' => $dispositivo->cor,
            ])->all(),

            'planosPorDispositivo' => $dispositivos->mapWithKeys(fn (Dispositivo $dispositivo): array => [
                $dispositivo->id => $dispositivo->planosDeCuidados
                    ->first()?->itens
                    ->map(fn ($item): array => [
                        'titulo' => $item->titulo,
                        'horario' => substr((string) $item->horario, 0, 5),
                    ])->all() ?? [],
            ])->all(),
        ]);
    }

    public function salvar(CadastroDePacienteRequest $request): RedirectResponse
    {
        $paciente = $this->cadastro->cadastrarPaciente($request->paraDto());

        event(new Registered($paciente));

        Auth::login($paciente);
        $request->session()->regenerate();

        return redirect()->route('paciente.inicio');
    }
}
