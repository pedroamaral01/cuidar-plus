<?php

declare(strict_types=1);

namespace Tests\Feature\Paciente;

use App\Models\ConteudoEducativo;
use App\Models\Dispositivo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ConteudosPerfilEEquipeTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $maria;

    private Usuario $joao;

    private Dispositivo $colostomia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->colostomia = Dispositivo::factory()->create(['nome' => 'Colostomia']);
        $this->maria = Usuario::factory()->paciente()->create(['nome' => 'Maria Aparecida']);
        $this->maria->dispositivos()->attach($this->colostomia, ['ativo' => true]);

        $this->joao = Usuario::factory()->paciente()->create(['nome' => 'João Pereira']);
    }

    // ------------------------------------------------------------------
    // Conteúdos educativos
    // ------------------------------------------------------------------

    public function test_listagem_traz_conteudos_visiveis_com_o_estado_de_favorito(): void
    {
        $geral = ConteudoEducativo::factory()->create(['titulo' => 'A geral']);
        ConteudoEducativo::factory()->create([
            'titulo' => 'B da colostomia',
            'dispositivo_id' => $this->colostomia->id,
        ]);
        $outro = Dispositivo::factory()->create();
        ConteudoEducativo::factory()->create([
            'titulo' => 'C de outro dispositivo',
            'dispositivo_id' => $outro->id,
        ]);

        $this->maria->conteudosFavoritos()->attach($geral);

        $this->actingAs($this->maria)
            ->get(route('paciente.conteudos'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Conteudos/Index')
                ->has('conteudos', 2)
                ->where('conteudos.0.titulo', 'A geral')
                ->where('conteudos.0.favorito', true)
                ->where('conteudos.1.favorito', false),
            );
    }

    public function test_paciente_favorita_e_desfavorita_conteudo(): void
    {
        $conteudo = ConteudoEducativo::factory()->create();

        $this->actingAs($this->maria)
            ->post(route('paciente.conteudos.favorito', $conteudo))
            ->assertSessionHas('status', 'Conteúdo salvo nos favoritos.');

        $this->assertCount(1, $this->maria->conteudosFavoritos()->get());

        $this->actingAs($this->maria)
            ->post(route('paciente.conteudos.favorito', $conteudo))
            ->assertSessionHas('status', 'Conteúdo removido dos favoritos.');

        $this->assertCount(0, $this->maria->conteudosFavoritos()->get());
    }

    public function test_favorito_de_um_paciente_nao_afeta_o_outro(): void
    {
        $conteudo = ConteudoEducativo::factory()->create();

        $this->actingAs($this->maria)->post(route('paciente.conteudos.favorito', $conteudo));

        $this->actingAs($this->joao)
            ->get(route('paciente.conteudos'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->where('conteudos.0.favorito', false),
            );
    }

    public function test_paciente_nao_favorita_conteudo_despublicado(): void
    {
        $despublicado = ConteudoEducativo::factory()->despublicado()->create();

        $this->actingAs($this->maria)
            ->post(route('paciente.conteudos.favorito', $despublicado))
            ->assertNotFound();

        $this->assertCount(0, $this->maria->conteudosFavoritos()->get());
    }

    public function test_paciente_nao_favorita_conteudo_de_outro_dispositivo(): void
    {
        $outro = Dispositivo::factory()->create();
        $conteudoDeOutro = ConteudoEducativo::factory()->create(['dispositivo_id' => $outro->id]);

        $this->actingAs($this->maria)
            ->post(route('paciente.conteudos.favorito', $conteudoDeOutro))
            ->assertNotFound();
    }

    public function test_detalhe_do_conteudo_de_texto_traz_o_corpo(): void
    {
        $conteudo = ConteudoEducativo::factory()->create([
            'titulo' => 'Alimentação após a alta',
            'corpo' => 'Reintroduza os alimentos aos poucos.',
        ]);

        $this->actingAs($this->maria)
            ->get(route('paciente.conteudos.show', $conteudo))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Conteudos/Detalhe')
                ->where('conteudo.titulo', 'Alimentação após a alta')
                ->where('conteudo.corpo', 'Reintroduza os alimentos aos poucos.'),
            );
    }

    public function test_paciente_nao_abre_conteudo_de_outro_dispositivo(): void
    {
        $outro = Dispositivo::factory()->create();
        $conteudoDeOutro = ConteudoEducativo::factory()->create(['dispositivo_id' => $outro->id]);

        $this->actingAs($this->maria)
            ->get(route('paciente.conteudos.show', $conteudoDeOutro))
            ->assertNotFound();
    }

    // ------------------------------------------------------------------
    // Perfil
    // ------------------------------------------------------------------

    public function test_perfil_mostra_dados_do_paciente_e_dispositivo_atual(): void
    {
        $this->actingAs($this->maria)
            ->get(route('paciente.perfil'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Perfil')
                ->where('paciente.nome', 'Maria Aparecida')
                ->where('paciente.iniciais', 'M')
                ->where('dispositivo.nome', 'Colostomia')
                ->missing('paciente.senha'),
            );
    }

    public function test_paciente_atualiza_o_proprio_perfil(): void
    {
        $this->actingAs($this->maria)
            ->put(route('paciente.perfil.atualizar'), [
                'nome' => 'Maria Aparecida da Silva',
                'email' => $this->maria->email,
                'telefone' => '(31) 91234-5678',
                'cuidador_nome' => 'Joana',
            ])
            ->assertSessionHasNoErrors();

        $this->maria->refresh();
        $this->assertSame('Maria Aparecida da Silva', $this->maria->nome);
        $this->assertSame('Joana', $this->maria->cuidador_nome);
    }

    public function test_perfil_recusa_email_ja_usado_por_outro_paciente(): void
    {
        $this->actingAs($this->maria)
            ->put(route('paciente.perfil.atualizar'), [
                'nome' => 'Maria Aparecida',
                'email' => $this->joao->email,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_trocar_de_email_invalida_a_verificacao_anterior(): void
    {
        $this->assertNotNull($this->maria->email_verified_at);

        $this->actingAs($this->maria)
            ->put(route('paciente.perfil.atualizar'), [
                'nome' => 'Maria Aparecida',
                'email' => 'novo@exemplo.com',
            ]);

        $this->assertNull($this->maria->refresh()->email_verified_at);
    }

    // ------------------------------------------------------------------
    // Falar com a equipe (somente a tela — seção 5.4)
    // ------------------------------------------------------------------

    public function test_tela_de_falar_com_a_equipe_abre_sem_mecanismo_de_envio(): void
    {
        $this->actingAs($this->maria)
            ->get(route('paciente.equipe'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina->component('Paciente/Equipe'));
    }

    public function test_nao_existe_rota_de_envio_de_mensagem_para_a_equipe(): void
    {
        // A decisão sobre o canal de contato está em aberto: enquanto não
        // houver definição, não deve existir endpoint de envio.
        $this->assertFalse(
            app('router')->has('paciente.equipe.enviar'),
            'Não deve existir rota de envio enquanto o canal de contato não for definido.',
        );
    }
}
