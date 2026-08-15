<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PerfilDeUsuario;
use App\Models\ConteudoEducativo;
use App\Models\Dispositivo;
use App\Models\Lembrete;
use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Contas e dados de exemplo para a demonstração do sistema.
 *
 * Cria dois pacientes de propósito: a regra crítica de isolamento (paciente A
 * nunca vê dado do paciente B) fica visível já na demonstração.
 */
class UsuarioDeDemonstracaoSeeder extends Seeder
{
    public function run(): void
    {
        $this->criarAdministrador();

        $maria = $this->criarPaciente(
            nome: 'Maria Aparecida',
            email: 'maria@cuidarplus.local',
            dispositivo: 'Colostomia',
            cuidador: ['nome' => 'Joana Aparecida', 'telefone' => '(31) 98888-1010'],
        );

        $this->criarPaciente(
            nome: 'João Pereira',
            email: 'joao@cuidarplus.local',
            dispositivo: 'Sonda vesical',
            cuidador: null,
        );

        $this->favoritarConteudos($maria);
    }

    private function criarAdministrador(): Usuario
    {
        return Usuario::updateOrCreate(
            ['email' => 'admin@cuidarplus.local'],
            [
                'nome' => 'Equipe Cuidar+',
                'senha' => Hash::make('senha1234'),
                'perfil' => PerfilDeUsuario::Administrador,
                'email_verified_at' => now(),
                'ativo' => true,
            ],
        );
    }

    /**
     * @param  array{nome: string, telefone: string}|null  $cuidador
     */
    private function criarPaciente(string $nome, string $email, string $dispositivo, ?array $cuidador): Usuario
    {
        $paciente = Usuario::updateOrCreate(
            ['email' => $email],
            [
                'nome' => $nome,
                'senha' => Hash::make('senha1234'),
                'perfil' => PerfilDeUsuario::Paciente,
                'email_verified_at' => now(),
                'data_de_nascimento' => '1958-04-12',
                'telefone' => '(31) 99999-0000',
                'cuidador_nome' => $cuidador['nome'] ?? null,
                'cuidador_telefone' => $cuidador['telefone'] ?? null,
                'ativo' => true,
            ],
        );

        $this->vincularDispositivo($paciente, $dispositivo);
        $this->criarLembretesDoPlano($paciente, $dispositivo);
        $this->criarHistoricoDeCuidados($paciente);

        return $paciente;
    }

    private function vincularDispositivo(Usuario $paciente, string $nomeDoDispositivo): void
    {
        $dispositivo = Dispositivo::where('nome', $nomeDoDispositivo)->first();

        if ($dispositivo === null) {
            return;
        }

        $paciente->dispositivos()->syncWithoutDetaching([
            $dispositivo->id => ['ativo' => true, 'data_de_inicio' => now()->subDays(20)->toDateString()],
        ]);
    }

    /** Os lembretes iniciais do paciente vêm do plano de cuidados do dispositivo. */
    private function criarLembretesDoPlano(Usuario $paciente, string $nomeDoDispositivo): void
    {
        $dispositivo = Dispositivo::with('planosDeCuidados.itens')
            ->where('nome', $nomeDoDispositivo)
            ->first();

        $plano = $dispositivo?->planosDeCuidados->firstWhere('ativo', true);

        if ($plano === null) {
            return;
        }

        foreach ($plano->itens as $item) {
            Lembrete::updateOrCreate(
                ['usuario_id' => $paciente->id, 'titulo' => $item->titulo],
                [
                    'tipo' => $item->tipo,
                    'horario' => $item->horario,
                    'ativo' => true,
                ],
            );
        }
    }

    private function criarHistoricoDeCuidados(Usuario $paciente): void
    {
        if ($paciente->registrosDeCuidados()->exists()) {
            return;
        }

        $historico = [
            ['titulo' => 'Troca de bolsa realizada', 'tipo' => 'troca', 'realizado_em' => now()->setTime(8, 10)],
            ['titulo' => 'Higienização do local', 'tipo' => 'higiene', 'realizado_em' => now()->setTime(12, 5)],
            ['titulo' => 'Esvaziamento registrado', 'tipo' => 'esvaziamento', 'realizado_em' => now()->subDay()->setTime(16, 0)],
            ['titulo' => 'Medicamento administrado', 'tipo' => 'medicamento', 'realizado_em' => now()->subDay()->setTime(20, 30)],
            ['titulo' => 'Proteção da pele aplicada', 'tipo' => 'protecao', 'realizado_em' => now()->subDays(2)->setTime(20, 15)],
        ];

        foreach ($historico as $registro) {
            RegistroDeCuidado::create([
                'usuario_id' => $paciente->id,
                'titulo' => $registro['titulo'],
                'tipo' => $registro['tipo'],
                'realizado_em' => $registro['realizado_em'],
            ]);
        }
    }

    private function favoritarConteudos(Usuario $paciente): void
    {
        $favoritos = ConteudoEducativo::whereIn('titulo', [
            'Vídeo: troca da bolsa passo a passo',
            'Quando procurar a equipe de saúde',
        ])->pluck('id');

        $paciente->conteudosFavoritos()->syncWithoutDetaching($favoritos);
    }
}
