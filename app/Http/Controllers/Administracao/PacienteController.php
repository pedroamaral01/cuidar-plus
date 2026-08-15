<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administracao;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Visualização básica dos pacientes. Não há edição nem exclusão aqui: o
 * escopo do MVP prevê apenas consulta (seção 2.3).
 */
class PacienteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Administracao/Pacientes/Index', [
            'pacientes' => Usuario::pacientes()
                ->with(['dispositivos' => fn ($consulta) => $consulta->wherePivot('ativo', true)])
                ->withCount('registrosDeCuidados as registros')
                ->orderBy('nome')
                ->get()
                ->map(fn (Usuario $paciente): array => [
                    'id' => $paciente->id,
                    'nome' => $paciente->nome,
                    'email' => $paciente->email,
                    'dispositivo' => $paciente->dispositivos->first()?->nome ?? '—',
                    'cadastroEm' => $paciente->created_at?->format('d/m/Y'),
                    'registros' => $paciente->registros,
                    'ativo' => $paciente->ativo,
                ])->all(),
        ]);
    }
}
