<?php

declare(strict_types=1);

namespace Tests\Feature\Administracao;

use App\Models\ConteudoEducativo;
use App\Models\Dispositivo;
use App\Models\SinalDeAlerta;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CrudDeAlertaEConteudoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::factory()->administrador()->create();
    }

    // ------------------------------------------------------------------
    // Sinais de alerta
    // ------------------------------------------------------------------

    public function test_administrador_cadastra_sinal_de_alerta_geral(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.alertas.store'), [
                'nome' => 'Febre',
                'orientacao' => 'Meça a temperatura e informe a equipe.',
                'gravidade' => 'alta',
                'dispositivo_id' => null,
                'publicado' => true,
            ])
            ->assertRedirect(route('administracao.alertas.index'));

        $this->assertDatabaseHas('sinais_de_alerta', [
            'nome' => 'Febre',
            'gravidade' => 'alta',
            'dispositivo_id' => null,
        ]);
    }

    public function test_sinal_de_alerta_exige_nome_orientacao_e_gravidade(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.alertas.store'), [])
            ->assertSessionHasErrors(['nome', 'orientacao', 'gravidade']);
    }

    public function test_sinal_de_alerta_recusa_gravidade_invalida(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.alertas.store'), [
                'nome' => 'Febre',
                'orientacao' => 'Informe a equipe.',
                'gravidade' => 'urgentissima',
            ])
            ->assertSessionHasErrors('gravidade');
    }

    public function test_listagem_de_alertas_ordena_pela_gravidade(): void
    {
        SinalDeAlerta::factory()->geral()->create(['nome' => 'Coceira', 'gravidade' => 'baixa']);
        SinalDeAlerta::factory()->geral()->create(['nome' => 'Sangramento', 'gravidade' => 'alta']);
        SinalDeAlerta::factory()->geral()->create(['nome' => 'Vermelhidão', 'gravidade' => 'media']);

        $this->actingAs($this->admin)
            ->get(route('administracao.alertas.index'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->where('sinais.0.nome', 'Sangramento')
                ->where('sinais.1.nome', 'Vermelhidão')
                ->where('sinais.2.nome', 'Coceira'),
            );
    }

    public function test_administrador_edita_e_exclui_sinal_de_alerta(): void
    {
        $sinal = SinalDeAlerta::factory()->geral()->create(['nome' => 'Antigo']);

        $this->actingAs($this->admin)
            ->put(route('administracao.alertas.update', $sinal), [
                'nome' => 'Nome novo',
                'orientacao' => 'Nova orientação para o paciente.',
                'gravidade' => 'media',
                'publicado' => false,
            ])
            ->assertRedirect(route('administracao.alertas.index'));

        $sinal->refresh();
        $this->assertSame('Nome novo', $sinal->nome);
        $this->assertFalse($sinal->publicado);

        $this->actingAs($this->admin)
            ->delete(route('administracao.alertas.destroy', $sinal));

        $this->assertModelMissing($sinal);
    }

    // ------------------------------------------------------------------
    // Conteúdos educativos
    // ------------------------------------------------------------------

    public function test_conteudo_de_texto_exige_corpo(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.conteudos.store'), [
                'titulo' => 'Alimentação após a alta',
                'tipo' => 'texto',
                'publicado' => true,
            ])
            ->assertSessionHasErrors('corpo');
    }

    public function test_conteudo_de_video_exige_url(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.conteudos.store'), [
                'titulo' => 'Vídeo da troca',
                'tipo' => 'video',
                'publicado' => true,
            ])
            ->assertSessionHasErrors('url_do_video');
    }

    public function test_conteudo_de_video_recusa_url_invalida(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.conteudos.store'), [
                'titulo' => 'Vídeo da troca',
                'tipo' => 'video',
                'url_do_video' => 'não é uma url',
                'publicado' => true,
            ])
            ->assertSessionHasErrors('url_do_video');
    }

    public function test_administrador_cadastra_conteudo_de_texto(): void
    {
        $dispositivo = Dispositivo::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('administracao.conteudos.store'), [
                'titulo' => 'Alimentação após a alta',
                'resumo' => 'O que ajuda e o que evitar.',
                'tipo' => 'texto',
                'corpo' => 'Reintroduza os alimentos aos poucos.',
                'dispositivo_id' => $dispositivo->id,
                'publicado' => true,
            ])
            ->assertRedirect(route('administracao.conteudos.index'));

        $this->assertDatabaseHas('conteudos_educativos', [
            'titulo' => 'Alimentação após a alta',
            'tipo' => 'texto',
            'dispositivo_id' => $dispositivo->id,
        ]);
    }

    public function test_listagem_mostra_quantos_pacientes_favoritaram(): void
    {
        $conteudo = ConteudoEducativo::factory()->create();
        $paciente = Usuario::factory()->paciente()->create();
        $paciente->conteudosFavoritos()->attach($conteudo);

        $this->actingAs($this->admin)
            ->get(route('administracao.conteudos.index'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->where('conteudos.0.favoritos', 1),
            );
    }

    public function test_paciente_nao_acessa_o_crud_de_alertas_nem_de_conteudos(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)->get(route('administracao.alertas.index'))->assertForbidden();
        $this->actingAs($paciente)->get(route('administracao.conteudos.index'))->assertForbidden();
    }

    // ------------------------------------------------------------------
    // Pacientes e auditoria
    // ------------------------------------------------------------------

    public function test_administrador_ve_a_lista_de_pacientes_sem_o_diario_deles(): void
    {
        $dispositivo = Dispositivo::factory()->create(['nome' => 'Colostomia']);
        $paciente = Usuario::factory()->paciente()->create(['nome' => 'Maria Aparecida']);
        $paciente->dispositivos()->attach($dispositivo, ['ativo' => true]);

        $this->actingAs($this->admin)
            ->get(route('administracao.pacientes.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Administracao/Pacientes/Index')
                ->has('pacientes', 1)
                ->where('pacientes.0.nome', 'Maria Aparecida')
                ->where('pacientes.0.dispositivo', 'Colostomia')
                // A listagem traz a contagem, nunca o conteúdo dos registros.
                ->has('pacientes.0.registros')
                ->missing('pacientes.0.senha'),
            );
    }

    public function test_auditoria_de_notificacoes_abre_mesmo_sem_notificacoes(): void
    {
        $this->actingAs($this->admin)
            ->get(route('administracao.notificacoes'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Administracao/Notificacoes/Index')
                ->has('notificacoes', 0),
            );
    }
}
