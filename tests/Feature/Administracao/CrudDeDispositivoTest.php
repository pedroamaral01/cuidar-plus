<?php

declare(strict_types=1);

namespace Tests\Feature\Administracao;

use App\Models\Dispositivo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CrudDeDispositivoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::factory()->administrador()->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosValidos(array $sobrescrever = []): array
    {
        return [
            'nome' => 'Gastrostomia',
            'descricao' => 'Sonda posicionada diretamente no estômago.',
            'icone' => 'Droplets',
            'cor' => 'teal',
            'ativo' => true,
            ...$sobrescrever,
        ];
    }

    public function test_administrador_lista_dispositivos_com_a_contagem_de_pacientes(): void
    {
        $dispositivo = Dispositivo::factory()->create(['nome' => 'Colostomia']);
        $paciente = Usuario::factory()->paciente()->create();
        $paciente->dispositivos()->attach($dispositivo, ['ativo' => true]);

        $this->actingAs($this->admin)
            ->get(route('administracao.dispositivos.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Administracao/Dispositivos/Index')
                ->has('dispositivos', 1)
                ->where('dispositivos.0.pacientesVinculados', 1),
            );
    }

    public function test_administrador_cadastra_dispositivo(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.dispositivos.store'), $this->dadosValidos())
            ->assertRedirect(route('administracao.dispositivos.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('dispositivos', ['nome' => 'Gastrostomia', 'ativo' => 1]);
    }

    public function test_cadastro_recusa_nome_repetido(): void
    {
        Dispositivo::factory()->create(['nome' => 'Gastrostomia']);

        $this->actingAs($this->admin)
            ->post(route('administracao.dispositivos.store'), $this->dadosValidos())
            ->assertSessionHasErrors('nome');
    }

    public function test_cadastro_recusa_cor_fora_da_paleta(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.dispositivos.store'), $this->dadosValidos(['cor' => 'rosa']))
            ->assertSessionHasErrors('cor');
    }

    public function test_administrador_edita_dispositivo(): void
    {
        $dispositivo = Dispositivo::factory()->create(['nome' => 'Antigo']);

        $this->actingAs($this->admin)
            ->put(
                route('administracao.dispositivos.update', $dispositivo),
                $this->dadosValidos(['nome' => 'Nome novo']),
            )
            ->assertRedirect(route('administracao.dispositivos.index'));

        $this->assertSame('Nome novo', $dispositivo->refresh()->nome);
    }

    public function test_edicao_permite_manter_o_proprio_nome(): void
    {
        $dispositivo = Dispositivo::factory()->create(['nome' => 'Colostomia']);

        $this->actingAs($this->admin)
            ->put(
                route('administracao.dispositivos.update', $dispositivo),
                $this->dadosValidos(['nome' => 'Colostomia', 'descricao' => 'Nova descrição']),
            )
            ->assertSessionHasNoErrors();
    }

    public function test_administrador_desativa_dispositivo(): void
    {
        $dispositivo = Dispositivo::factory()->create();

        $this->actingAs($this->admin)
            ->put(
                route('administracao.dispositivos.update', $dispositivo),
                $this->dadosValidos(['nome' => $dispositivo->nome, 'ativo' => false]),
            );

        $this->assertFalse($dispositivo->refresh()->ativo);
    }

    public function test_dispositivo_sem_paciente_pode_ser_excluido(): void
    {
        $dispositivo = Dispositivo::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('administracao.dispositivos.destroy', $dispositivo))
            ->assertRedirect(route('administracao.dispositivos.index'));

        $this->assertModelMissing($dispositivo);
    }

    public function test_dispositivo_com_paciente_vinculado_nao_e_excluido(): void
    {
        $dispositivo = Dispositivo::factory()->create();
        $paciente = Usuario::factory()->paciente()->create();
        $paciente->dispositivos()->attach($dispositivo, ['ativo' => true]);

        $this->actingAs($this->admin)
            ->delete(route('administracao.dispositivos.destroy', $dispositivo))
            ->assertSessionHas('erro');

        $this->assertModelExists($dispositivo);
    }

    public function test_paciente_nao_acessa_o_crud_de_dispositivos(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->get(route('administracao.dispositivos.index'))
            ->assertForbidden();

        $this->actingAs($paciente)
            ->post(route('administracao.dispositivos.store'), $this->dadosValidos())
            ->assertForbidden();

        $this->assertDatabaseCount('dispositivos', 0);
    }
}
