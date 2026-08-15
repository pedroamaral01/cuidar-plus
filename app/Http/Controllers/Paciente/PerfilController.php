<?php

declare(strict_types=1);

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Paciente\AtualizarPerfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PerfilController extends Controller
{
    public function mostrar(Request $request): Response
    {
        $paciente = $request->user();
        $dispositivo = $paciente->dispositivoAtual();

        return Inertia::render('Paciente/Perfil', [
            'paciente' => [
                'nome' => $paciente->nome,
                'email' => $paciente->email,
                'telefone' => $paciente->telefone,
                'data_de_nascimento' => $paciente->data_de_nascimento?->toDateString(),
                'cuidador_nome' => $paciente->cuidador_nome,
                'cuidador_telefone' => $paciente->cuidador_telefone,
                'iniciais' => mb_strtoupper(mb_substr($paciente->nome, 0, 1)),
            ],
            'dispositivo' => $dispositivo === null ? null : [
                'nome' => $dispositivo->nome,
                'icone' => $dispositivo->icone,
                'cor' => $dispositivo->cor,
            ],
        ]);
    }

    public function atualizar(AtualizarPerfilRequest $request): RedirectResponse
    {
        $paciente = $request->user();
        $validado = $request->validated();

        $paciente->fill($validado);

        // Trocar de e-mail invalida a verificação anterior.
        if ($paciente->isDirty('email')) {
            $paciente->email_verified_at = null;
        }

        $paciente->save();

        return back()->with('status', 'Perfil atualizado.');
    }
}
