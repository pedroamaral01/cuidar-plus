<?php

declare(strict_types=1);

namespace Tests\Integration\Services;

use App\DTOs\DadosDoCadastroDePaciente;
use App\Models\Dispositivo;
use App\Models\ItemDoPlanoDeCuidados;
use App\Models\PlanoDeCuidados;
use App\Models\Usuario;
use App\Services\Autenticacao\CadastroDePacienteService;
use App\Services\Paciente\DispositivoDoPacienteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CadastroECadastroDeDispositivoTest extends TestCase
{
    use RefreshDatabase;

    private function criarDispositivoComPlano(string $nome = 'Colostomia'): Dispositivo
    {
        $dispositivo = Dispositivo::factory()->create(['nome' => $nome]);
        $plano = PlanoDeCuidados::factory()->create(['dispositivo_id' => $dispositivo->id, 'ativo' => true]);

        foreach ([['Trocar bolsa', 'troca', '08:00'], ['Higienizar', 'higiene', '12:00']] as $ordem => [$titulo, $tipo, $horario]) {
            ItemDoPlanoDeCuidados::create([
                'plano_de_cuidados_id' => $plano->id,
                'titulo' => $titulo,
                'tipo' => $tipo,
                'horario' => $horario,
                'ordem' => $ordem,
            ]);
        }

        return $dispositivo;
    }

    public function test_cadastro_em_tres_passos_cria_paciente_dispositivo_e_lembretes(): void
    {
        $dispositivo = $this->criarDispositivoComPlano();

        $paciente = app(CadastroDePacienteService::class)->cadastrarPaciente(
            DadosDoCadastroDePaciente::apartirDoFormulario([
                'nome' => 'Maria Aparecida',
                'email' => 'maria@exemplo.com',
                'password' => 'senha1234',
                'telefone' => '(31) 99999-0000',
                'cuidador_nome' => 'Joana',
                'dispositivo_id' => $dispositivo->id,
            ]),
        );

        $this->assertTrue($paciente->ehPaciente());
        $this->assertTrue(Hash::check('senha1234', $paciente->senha), 'A senha precisa ser gravada com hash.');
        $this->assertSame('Joana', $paciente->cuidador_nome);
        $this->assertSame($dispositivo->id, $paciente->dispositivoAtual()?->id);
        $this->assertCount(2, $paciente->lembretes()->get());
    }

    public function test_paciente_pode_concluir_o_cadastro_sem_o_plano_sugerido(): void
    {
        $dispositivo = $this->criarDispositivoComPlano();

        $paciente = app(CadastroDePacienteService::class)->cadastrarPaciente(
            DadosDoCadastroDePaciente::apartirDoFormulario([
                'nome' => 'João Pereira',
                'email' => 'joao@exemplo.com',
                'password' => 'senha1234',
                'dispositivo_id' => $dispositivo->id,
                'aplicar_plano_de_cuidados' => false,
            ]),
        );

        $this->assertSame($dispositivo->id, $paciente->dispositivoAtual()?->id);
        $this->assertCount(0, $paciente->lembretes()->get());
    }

    public function test_cadastro_ignora_dispositivo_inativo_sem_quebrar(): void
    {
        $inativo = Dispositivo::factory()->inativo()->create();

        $paciente = app(CadastroDePacienteService::class)->cadastrarPaciente(
            DadosDoCadastroDePaciente::apartirDoFormulario([
                'nome' => 'João Pereira',
                'email' => 'joao@exemplo.com',
                'password' => 'senha1234',
                'dispositivo_id' => $inativo->id,
            ]),
        );

        $this->assertNull($paciente->dispositivoAtual());
    }

    public function test_trocar_de_dispositivo_desativa_o_vinculo_anterior(): void
    {
        $servico = app(DispositivoDoPacienteService::class);
        $paciente = Usuario::factory()->paciente()->create();

        $colostomia = $this->criarDispositivoComPlano('Colostomia');
        $sonda = $this->criarDispositivoComPlano('Sonda vesical');

        $servico->definirDispositivo($paciente, $colostomia);
        $servico->definirDispositivo($paciente, $sonda);

        $this->assertSame('Sonda vesical', $paciente->dispositivoAtual()?->nome);
        // O vínculo antigo continua no histórico, apenas desativado.
        $this->assertCount(2, $paciente->dispositivos()->get());
    }

    public function test_aplicar_plano_duas_vezes_nao_duplica_lembretes(): void
    {
        $servico = app(DispositivoDoPacienteService::class);
        $paciente = Usuario::factory()->paciente()->create();
        $dispositivo = $this->criarDispositivoComPlano();

        $primeiro = $servico->aplicarPlanoDeCuidados($paciente, $dispositivo);
        $segundo = $servico->aplicarPlanoDeCuidados($paciente, $dispositivo);

        $this->assertSame(2, $primeiro);
        $this->assertSame(0, $segundo, 'Reaplicar o plano não pode duplicar a rotina do paciente.');
        $this->assertCount(2, $paciente->lembretes()->get());
    }

    public function test_dispositivo_sem_plano_ativo_nao_cria_lembretes(): void
    {
        $servico = app(DispositivoDoPacienteService::class);
        $paciente = Usuario::factory()->paciente()->create();
        $dispositivo = Dispositivo::factory()->create();

        $criados = $servico->aplicarPlanoDeCuidados($paciente, $dispositivo);

        $this->assertSame(0, $criados);
        $this->assertCount(0, $paciente->lembretes()->get());
    }
}
