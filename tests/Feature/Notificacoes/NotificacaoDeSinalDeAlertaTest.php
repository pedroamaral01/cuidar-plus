<?php

declare(strict_types=1);

namespace Tests\Feature\Notificacoes;

use App\Events\SinalDeAlertaCadastrado;
use App\Listeners\NotificarPacientesSobreSinalDeAlerta;
use App\Models\Dispositivo;
use App\Models\SinalDeAlerta;
use App\Models\Usuario;
use App\Notifications\NotificarNovoSinalDeAlerta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificacaoDeSinalDeAlertaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastrar_sinal_de_alerta_dispara_o_evento(): void
    {
        Event::fake();

        $admin = Usuario::factory()->administrador()->create();

        $this->actingAs($admin)->post(route('administracao.alertas.store'), [
            'nome' => 'Febre',
            'orientacao' => 'Meça a temperatura e informe a equipe.',
            'gravidade' => 'alta',
            'publicado' => true,
        ]);

        Event::assertDispatched(SinalDeAlertaCadastrado::class);
    }

    public function test_sinal_geral_alcanca_todos_os_pacientes_e_nenhum_administrador(): void
    {
        Notification::fake();

        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();
        $admin = Usuario::factory()->administrador()->create();

        $sinal = SinalDeAlerta::factory()->geral()->create();

        app(NotificarPacientesSobreSinalDeAlerta::class)
            ->handle(new SinalDeAlertaCadastrado($sinal));

        Notification::assertSentTo($maria, NotificarNovoSinalDeAlerta::class);
        Notification::assertSentTo($joao, NotificarNovoSinalDeAlerta::class);
        Notification::assertNotSentTo($admin, NotificarNovoSinalDeAlerta::class);
    }

    public function test_sinal_de_dispositivo_alcanca_so_quem_usa_aquele_dispositivo(): void
    {
        Notification::fake();

        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        $maria = Usuario::factory()->paciente()->create();
        $maria->dispositivos()->attach($colostomia, ['ativo' => true]);

        $joao = Usuario::factory()->paciente()->create();
        $joao->dispositivos()->attach($sonda, ['ativo' => true]);

        $sinal = SinalDeAlerta::factory()->create(['dispositivo_id' => $colostomia->id]);

        app(NotificarPacientesSobreSinalDeAlerta::class)
            ->handle(new SinalDeAlertaCadastrado($sinal));

        Notification::assertSentTo($maria, NotificarNovoSinalDeAlerta::class);
        Notification::assertNotSentTo($joao, NotificarNovoSinalDeAlerta::class);
    }

    public function test_sinal_despublicado_nao_notifica_ninguem(): void
    {
        Notification::fake();

        $paciente = Usuario::factory()->paciente()->create();
        $sinal = SinalDeAlerta::factory()->geral()->create(['publicado' => false]);

        app(NotificarPacientesSobreSinalDeAlerta::class)
            ->handle(new SinalDeAlertaCadastrado($sinal));

        Notification::assertNothingSent();
    }

    public function test_paciente_com_vinculo_desativado_nao_recebe(): void
    {
        Notification::fake();

        $colostomia = Dispositivo::factory()->create();
        $exPaciente = Usuario::factory()->paciente()->create();
        $exPaciente->dispositivos()->attach($colostomia, ['ativo' => false]);

        $sinal = SinalDeAlerta::factory()->create(['dispositivo_id' => $colostomia->id]);

        app(NotificarPacientesSobreSinalDeAlerta::class)
            ->handle(new SinalDeAlertaCadastrado($sinal));

        Notification::assertNotSentTo($exPaciente, NotificarNovoSinalDeAlerta::class);
    }
}
