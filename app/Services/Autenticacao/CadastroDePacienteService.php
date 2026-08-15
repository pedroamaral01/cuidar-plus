<?php

declare(strict_types=1);

namespace App\Services\Autenticacao;

use App\DTOs\DadosDoCadastroDePaciente;
use App\Enums\PerfilDeUsuario;
use App\Models\Dispositivo;
use App\Models\Usuario;
use App\Services\Paciente\DispositivoDoPacienteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Cadastro do paciente em 3 passos: dados pessoais, dispositivo e plano de
 * cuidados. Tudo em uma transação — um cadastro pela metade (paciente sem
 * dispositivo, ou dispositivo sem lembretes) deixaria a conta inutilizável.
 */
class CadastroDePacienteService
{
    public function __construct(
        private readonly DispositivoDoPacienteService $dispositivosDoPaciente,
    ) {}

    public function cadastrarPaciente(DadosDoCadastroDePaciente $dados): Usuario
    {
        return DB::transaction(function () use ($dados): Usuario {
            $paciente = Usuario::create([
                'nome' => $dados->nome,
                'email' => $dados->email,
                'senha' => Hash::make($dados->senha),
                'perfil' => PerfilDeUsuario::Paciente,
                'data_de_nascimento' => $dados->dataDeNascimento,
                'cpf' => $dados->cpf,
                'telefone' => $dados->telefone,
                'cuidador_nome' => $dados->cuidadorNome,
                'cuidador_telefone' => $dados->cuidadorTelefone,
                'ativo' => true,
            ]);

            $dispositivo = $dados->dispositivoId !== null
                ? Dispositivo::ativos()->find($dados->dispositivoId)
                : null;

            if ($dispositivo instanceof Dispositivo) {
                $this->dispositivosDoPaciente->definirDispositivo(
                    $paciente,
                    $dispositivo,
                    $dados->aplicarPlanoDeCuidados,
                );
            }

            return $paciente;
        });
    }
}
