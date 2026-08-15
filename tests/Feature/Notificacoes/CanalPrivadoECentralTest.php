<?php

declare(strict_types=1);

namespace Tests\Feature\Notificacoes;

use App\Models\Lembrete;
use App\Models\Usuario;
use App\Notifications\NotificarLembrete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * Isolamento no tempo real e na central de notificações.
 */
class CanalPrivadoECentralTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A suíte roda com BROADCAST_CONNECTION=null, e o broadcaster `null`
     * autoriza qualquer canal — sem esta troca, os testes de autorização
     * passariam sem exercitar nada.
     *
     * A troca precisa de dois passos: `routes/channels.php` é carregado no
     * boot da aplicação, quando o driver ainda é o `null`, e os canais ficam
     * registrados naquele driver. Ao trocar o driver, é preciso registrar os
     * canais de novo — senão o novo broadcaster não conhece canal nenhum e
     * nega tudo, inclusive o dono legítimo.
     *
     * Nenhuma conexão de rede acontece: a autorização é resolvida no servidor.
     */
    private function usarBroadcasterReal(): void
    {
        config(['broadcasting.default' => 'reverb']);

        require base_path('routes/channels.php');
    }

    private function notificar(Usuario $paciente): void
    {
        $lembrete = Lembrete::factory()->create(['usuario_id' => $paciente->id]);
        $paciente->notify(new NotificarLembrete($lembrete));
    }

    // ------------------------------------------------------------------
    // Canal privado do Reverb
    // ------------------------------------------------------------------

    public function test_paciente_autoriza_o_proprio_canal(): void
    {
        $this->usarBroadcasterReal();

        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->post('/broadcasting/auth', [
                'channel_name' => "private-App.Models.Usuario.{$paciente->id}",
                'socket_id' => '1234.5678',
            ])
            ->assertOk();
    }

    public function test_paciente_nao_autoriza_o_canal_de_outro_paciente(): void
    {
        $this->usarBroadcasterReal();

        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        $this->actingAs($joao)
            ->post('/broadcasting/auth', [
                'channel_name' => "private-App.Models.Usuario.{$maria->id}",
                'socket_id' => '1234.5678',
            ])
            ->assertForbidden();
    }

    public function test_visitante_nao_autoriza_canal_nenhum(): void
    {
        $this->usarBroadcasterReal();

        $paciente = Usuario::factory()->paciente()->create();

        $this->post('/broadcasting/auth', [
            'channel_name' => "private-App.Models.Usuario.{$paciente->id}",
            'socket_id' => '1234.5678',
        ])->assertStatus(403);
    }

    // ------------------------------------------------------------------
    // Central de notificações
    // ------------------------------------------------------------------

    public function test_central_lista_apenas_as_notificacoes_do_proprio_paciente(): void
    {
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        $this->notificar($maria);
        $this->notificar($maria);
        $this->notificar($joao);

        $this->actingAs($maria)
            ->get(route('paciente.notificacoes'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Notificacoes')
                ->has('notificacoes', 2),
            );

        $this->actingAs($joao)
            ->get(route('paciente.notificacoes'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina->has('notificacoes', 1));
    }

    public function test_badge_de_nao_lidas_e_compartilhado_com_todas_as_telas(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $this->notificar($paciente);
        $this->notificar($paciente);

        $this->actingAs($paciente)
            ->get(route('paciente.inicio'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina->where('naoLidas', 2));
    }

    public function test_paciente_marca_a_propria_notificacao_como_lida(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $this->notificar($paciente);

        $notificacao = $paciente->notifications()->first();

        $this->actingAs($paciente)
            ->patch(route('paciente.notificacoes.lida', $notificacao->id));

        $this->assertNotNull($notificacao->refresh()->read_at);
        $this->assertSame(0, $paciente->unreadNotifications()->count());
    }

    public function test_paciente_nao_marca_notificacao_de_outro_como_lida(): void
    {
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        $this->notificar($maria);
        $notificacaoDaMaria = $maria->notifications()->first();

        $this->actingAs($joao)
            ->patch(route('paciente.notificacoes.lida', $notificacaoDaMaria->id))
            ->assertNotFound();

        $this->assertNull($notificacaoDaMaria->refresh()->read_at);
    }

    public function test_paciente_marca_todas_como_lidas(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $this->notificar($paciente);
        $this->notificar($paciente);

        $this->actingAs($paciente)
            ->patch(route('paciente.notificacoes.todas-lidas'))
            ->assertSessionHas('status');

        $this->assertSame(0, $paciente->unreadNotifications()->count());
    }

    public function test_marcar_todas_como_lidas_nao_toca_nas_de_outro_paciente(): void
    {
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        $this->notificar($maria);
        $this->notificar($joao);

        $this->actingAs($maria)->patch(route('paciente.notificacoes.todas-lidas'));

        $this->assertSame(0, $maria->unreadNotifications()->count());
        $this->assertSame(1, $joao->unreadNotifications()->count());
    }

    public function test_auditoria_administrativa_mostra_as_notificacoes_enviadas(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $paciente = Usuario::factory()->paciente()->create(['nome' => 'Maria Aparecida']);

        $this->notificar($paciente);

        $this->actingAs($admin)
            ->get(route('administracao.notificacoes'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->has('notificacoes', 1)
                ->where('notificacoes.0.destinatario', 'Maria Aparecida')
                ->where('notificacoes.0.tipo', 'lembrete')
                ->where('notificacoes.0.canal', 'database + broadcast'),
            );
    }
}
