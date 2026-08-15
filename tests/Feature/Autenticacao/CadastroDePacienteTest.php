<?php

declare(strict_types=1);

namespace Tests\Feature\Autenticacao;

use App\Models\Dispositivo;
use App\Models\ItemDoPlanoDeCuidados;
use App\Models\PlanoDeCuidados;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CadastroDePacienteTest extends TestCase
{
    use RefreshDatabase;

    private function dispositivoComPlano(): Dispositivo
    {
        $dispositivo = Dispositivo::factory()->create(['nome' => 'Colostomia']);
        $plano = PlanoDeCuidados::factory()->create([
            'dispositivo_id' => $dispositivo->id,
            'ativo' => true,
        ]);

        ItemDoPlanoDeCuidados::create([
            'plano_de_cuidados_id' => $plano->id,
            'titulo' => 'Trocar bolsa',
            'tipo' => 'troca',
            'horario' => '08:00',
            'ordem' => 0,
        ]);

        return $dispositivo;
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosValidos(Dispositivo $dispositivo): array
    {
        return [
            'nome' => 'Maria Aparecida',
            'email' => 'maria@exemplo.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
            'telefone' => '(31) 99999-0000',
            'dispositivo_id' => $dispositivo->id,
            'aplicar_plano_de_cuidados' => true,
        ];
    }

    public function test_formulario_recebe_dispositivos_e_planos_do_banco(): void
    {
        $this->dispositivoComPlano();
        Dispositivo::factory()->inativo()->create(['nome' => 'Descontinuado']);

        $this->get(route('cadastro.criar'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Autenticacao/Cadastro')
                // Dispositivo inativo não pode ser oferecido no cadastro.
                ->has('dispositivos', 1)
                ->has('planosPorDispositivo'),
            );
    }

    public function test_cadastro_completo_cria_conta_autentica_e_leva_ao_inicio(): void
    {
        $dispositivo = $this->dispositivoComPlano();

        $resposta = $this->post(route('cadastro.salvar'), $this->dadosValidos($dispositivo));

        $resposta->assertRedirect(route('paciente.inicio'));
        $this->assertAuthenticated();

        $paciente = Usuario::where('email', 'maria@exemplo.com')->first();
        $this->assertNotNull($paciente);
        $this->assertTrue($paciente->ehPaciente());
        $this->assertSame($dispositivo->id, $paciente->dispositivoAtual()?->id);
        $this->assertCount(1, $paciente->lembretes()->get());
    }

    public function test_cadastro_exige_dispositivo(): void
    {
        $dispositivo = $this->dispositivoComPlano();
        $dados = $this->dadosValidos($dispositivo);
        unset($dados['dispositivo_id']);

        $this->post(route('cadastro.salvar'), $dados)
            ->assertSessionHasErrors('dispositivo_id');

        $this->assertGuest();
        $this->assertDatabaseCount('usuarios', 0);
    }

    public function test_cadastro_recusa_dispositivo_inativo(): void
    {
        $inativo = Dispositivo::factory()->inativo()->create();
        $dados = $this->dadosValidos($inativo);

        $this->post(route('cadastro.salvar'), $dados)
            ->assertSessionHasErrors('dispositivo_id');

        $this->assertDatabaseCount('usuarios', 0);
    }

    public function test_cadastro_recusa_email_ja_usado(): void
    {
        $dispositivo = $this->dispositivoComPlano();
        Usuario::factory()->create(['email' => 'maria@exemplo.com']);

        $this->post(route('cadastro.salvar'), $this->dadosValidos($dispositivo))
            ->assertSessionHasErrors('email');
    }

    public function test_cadastro_recusa_confirmacao_de_senha_diferente(): void
    {
        $dispositivo = $this->dispositivoComPlano();
        $dados = $this->dadosValidos($dispositivo);
        $dados['password_confirmation'] = 'outra-senha';

        $this->post(route('cadastro.salvar'), $dados)
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('usuarios', 0);
    }

    public function test_cadastro_recusa_data_de_nascimento_no_futuro(): void
    {
        $dispositivo = $this->dispositivoComPlano();
        $dados = $this->dadosValidos($dispositivo);
        $dados['data_de_nascimento'] = now()->addYear()->toDateString();

        $this->post(route('cadastro.salvar'), $dados)
            ->assertSessionHasErrors('data_de_nascimento');
    }

    public function test_paciente_pode_recusar_o_plano_sugerido(): void
    {
        $dispositivo = $this->dispositivoComPlano();
        $dados = $this->dadosValidos($dispositivo);
        $dados['aplicar_plano_de_cuidados'] = false;

        $this->post(route('cadastro.salvar'), $dados);

        $paciente = Usuario::where('email', 'maria@exemplo.com')->first();
        $this->assertCount(0, $paciente->lembretes()->get());
    }

    public function test_cadastro_guarda_o_cuidador_informado(): void
    {
        $dispositivo = $this->dispositivoComPlano();
        $dados = $this->dadosValidos($dispositivo);
        $dados['cuidador_nome'] = 'Joana Aparecida';
        $dados['cuidador_telefone'] = '(31) 98888-1010';

        $this->post(route('cadastro.salvar'), $dados);

        $paciente = Usuario::where('email', 'maria@exemplo.com')->first();
        $this->assertSame('Joana Aparecida', $paciente->cuidador_nome);
        $this->assertSame('(31) 98888-1010', $paciente->cuidador_telefone);
    }

    public function test_usuario_autenticado_nao_acessa_o_cadastro(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->get(route('cadastro.criar'))
            ->assertRedirect();
    }
}
