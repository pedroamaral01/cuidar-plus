<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use App\Services\Paciente\DispositivoDoPacienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DispositivoController extends Controller
{
    public function __construct(
        private readonly DispositivoDoPacienteService $dispositivosDoPaciente,
    ) {}

    public function mostrar(Request $request): Response
    {
        return Inertia::render('Paciente/Dispositivo', [
            'dispositivoAtualId' => $request->user()->dispositivoAtual()?->id,
            'dispositivos' => Dispositivo::ativos()
                ->orderBy('id')
                ->get()
                ->map(fn (Dispositivo $dispositivo): array => [
                    'id' => $dispositivo->id,
                    'nome' => $dispositivo->nome,
                    'descricao' => $dispositivo->descricao,
                    'icone' => $dispositivo->icone,
                    'cor' => $dispositivo->cor,
                ])->all(),
        ]);
    }

    public function definir(Request $request): RedirectResponse
    {
        $validado = $request->validate(
            [
                'dispositivo_id' => [
                    'required',
                    Rule::exists('dispositivos', 'id')->where('ativo', true),
                ],
                'aplicar_plano_de_cuidados' => ['boolean'],
            ],
            [
                'dispositivo_id.required' => 'Selecione o dispositivo que você utiliza.',
                'dispositivo_id.exists' => 'Dispositivo indisponível. Escolha outro.',
            ],
        );

        $dispositivo = Dispositivo::ativos()->findOrFail($validado['dispositivo_id']);

        $this->dispositivosDoPaciente->definirDispositivo(
            $request->user(),
            $dispositivo,
            (bool) ($validado['aplicar_plano_de_cuidados'] ?? true),
        );

        return back()->with('status', "Dispositivo atualizado para {$dispositivo->nome}.");
    }
}
