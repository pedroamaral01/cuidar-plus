<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administracao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administracao\SalvarDispositivoRequest;
use App\Models\Dispositivo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DispositivoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Administracao/Dispositivos/Index', [
            'dispositivos' => Dispositivo::withCount([
                'pacientes as pacientes_vinculados' => fn ($consulta) => $consulta->where('usuarios_dispositivos.ativo', true),
            ])
                ->orderBy('nome')
                ->get()
                ->map(fn (Dispositivo $dispositivo): array => [
                    'id' => $dispositivo->id,
                    'nome' => $dispositivo->nome,
                    'descricao' => $dispositivo->descricao,
                    'icone' => $dispositivo->icone,
                    'cor' => $dispositivo->cor,
                    'ativo' => $dispositivo->ativo,
                    'pacientesVinculados' => $dispositivo->pacientes_vinculados,
                ])->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Administracao/Dispositivos/Formulario', [
            'dispositivo' => null,
        ]);
    }

    public function store(SalvarDispositivoRequest $request): RedirectResponse
    {
        Dispositivo::create($request->validated());

        return redirect()
            ->route('administracao.dispositivos.index')
            ->with('status', 'Dispositivo cadastrado.');
    }

    public function edit(Dispositivo $dispositivo): Response
    {
        return Inertia::render('Administracao/Dispositivos/Formulario', [
            'dispositivo' => [
                'id' => $dispositivo->id,
                'nome' => $dispositivo->nome,
                'descricao' => $dispositivo->descricao,
                'icone' => $dispositivo->icone,
                'cor' => $dispositivo->cor,
                'ativo' => $dispositivo->ativo,
            ],
        ]);
    }

    public function update(SalvarDispositivoRequest $request, Dispositivo $dispositivo): RedirectResponse
    {
        $dispositivo->update($request->validated());

        return redirect()
            ->route('administracao.dispositivos.index')
            ->with('status', 'Dispositivo atualizado.');
    }

    /**
     * Dispositivo com paciente vinculado não é apagado — apagar levaria junto
     * o vínculo, as orientações e o histórico de quem depende dele. Nesse caso
     * a via correta é desativar.
     */
    public function destroy(Dispositivo $dispositivo): RedirectResponse
    {
        $vinculados = $dispositivo->pacientes()->count();

        if ($vinculados > 0) {
            return back()->with('erro', "Este dispositivo tem {$vinculados} paciente(s) vinculado(s). Desative-o em vez de excluir.");
        }

        $dispositivo->delete();

        return redirect()
            ->route('administracao.dispositivos.index')
            ->with('status', 'Dispositivo excluído.');
    }
}
