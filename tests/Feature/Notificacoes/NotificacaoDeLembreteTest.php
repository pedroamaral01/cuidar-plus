<?php

declare(strict_types=1);

namespace Tests\Feature\Notificacoes;

use App\Events\LembreteProximoDoHorario;
use App\Listeners\NotificarPacienteSobreLembrete;
use App\Models\Lembrete;
use App\Models\Usuario;
use App\Notifications\NotificarLembrete;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificacaoDeLembreteTest extends TestCase
{
    use RefreshDatabase;

    public function test_comando_dispara_evento_para_lembrete_na_janela_do_horario(): void
    {
        Event::fake();

        $paciente = Usuario::factory()->paciente()->create();
        Lembrete::factory()->noHorario(now()->subMinute()->format('H:i:s'))->create([
            'usuario_id' => $paciente->id,
        ]);
        // Fora da janela.
        Lembrete::factory()->noHorario(now()->addHours(5)->format('H:i:s'))->create([
            'usuario_id' => $paciente->id,
        ]);

        $this->artisan('cuidar:avisar-lembretes')->assertSuccessful();

        Event::assertDispatched(LembreteProximoDoHorario::class, 1);
    }

    public function test_comando_nao_dispara_para_lembrete_desligado(): void
    {
        Event::fake();

        $paciente = Usuario::factory()->paciente()->create();
        Lembrete::factory()->inativo()->noHorario(now()->format('H:i:s'))->create([
            'usuario_id' => $paciente->id,
        ]);

        $this->artisan('cuidar:avisar-lembretes')->assertSuccessful();

        Event::assertNotDispatched(LembreteProximoDoHorario::class);
    }

    public function test_listener_notifica_o_dono_do_lembrete(): void
    {
        Notification::fake();

        $paciente = Usuario::factory()->paciente()->create();
        $lembrete = Lembrete::factory()->create(['usuario_id' => $paciente->id]);

        app(NotificarPacienteSobreLembrete::class)
            ->handle(new LembreteProximoDoHorario($lembrete));

        Notification::assertSentTo($paciente, NotificarLembrete::class);
    }

    public function test_paciente_nao_recebe_notificacao_de_lembrete_de_outro(): void
    {
        Notification::fake();

        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();
        $lembreteDaMaria = Lembrete::factory()->create(['usuario_id' => $maria->id]);

        app(NotificarPacienteSobreLembrete::class)
            ->handle(new LembreteProximoDoHorario($lembreteDaMaria));

        Notification::assertSentTo($maria, NotificarLembrete::class);
        Notification::assertNotSentTo($joao, NotificarLembrete::class);
    }

    public function test_listener_marca_o_lembrete_como_notificado(): void
    {
        Notification::fake();

        $paciente = Usuario::factory()->paciente()->create();
        $lembrete = Lembrete::factory()->create([
            'usuario_id' => $paciente->id,
            'notificado_em' => null,
        ]);

        app(NotificarPacienteSobreLembrete::class)
            ->handle(new LembreteProximoDoHorario($lembrete));

        $this->assertNotNull($lembrete->refresh()->notificado_em);
    }

    public function test_lembrete_ja_notificado_hoje_nao_entra_na_proxima_varredura(): void
    {
        Event::fake();

        $paciente = Usuario::factory()->paciente()->create();
        Lembrete::factory()->noHorario(now()->format('H:i:s'))->create([
            'usuario_id' => $paciente->id,
            'notificado_em' => now(),
        ]);

        $this->artisan('cuidar:avisar-lembretes')->assertSuccessful();

        Event::assertNotDispatched(LembreteProximoDoHorario::class);
    }

    public function test_notificacao_de_lembrete_grava_no_banco_com_os_dois_canais(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $lembrete = Lembrete::factory()->noHorario('08:00:00')->create([
            'usuario_id' => $paciente->id,
            'titulo' => 'Trocar bolsa',
        ]);

        $notificacao = new NotificarLembrete($lembrete);

        $this->assertSame(['database', 'broadcast'], $notificacao->via($paciente));
        $this->assertInstanceOf(ShouldQueue::class, $notificacao);

        $paciente->notify($notificacao);

        $gravada = $paciente->notifications()->first();
        $this->assertNotNull($gravada);
        $this->assertSame('lembrete', $gravada->data['tipo']);
        $this->assertSame('Hora de: Trocar bolsa', $gravada->data['titulo']);
        $this->assertStringContainsString('08:00', $gravada->data['descricao']);
    }
}
