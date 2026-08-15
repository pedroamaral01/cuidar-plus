<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Dispositivo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispositivoDoPacienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_vincula_paciente_a_um_dispositivo(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $colostomia = Dispositivo::factory()->create(['nome' => 'Colostomia']);

        $paciente->dispositivos()->attach($colostomia, ['ativo' => true]);

        $this->assertTrue($paciente->dispositivos()->where('dispositivos.id', $colostomia->id)->exists());
        $this->assertSame('Colostomia', $paciente->dispositivoAtual()?->nome);
    }

    public function test_dispositivo_atual_ignora_vinculo_desativado(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $antigo = Dispositivo::factory()->create(['nome' => 'Sonda vesical']);
        $atual = Dispositivo::factory()->create(['nome' => 'Cistostomia']);

        $paciente->dispositivos()->attach($antigo, ['ativo' => false]);
        $paciente->dispositivos()->attach($atual, ['ativo' => true]);

        $this->assertSame('Cistostomia', $paciente->dispositivoAtual()?->nome);
    }

    public function test_paciente_sem_vinculo_nao_tem_dispositivo_atual(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->assertNull($paciente->dispositivoAtual());
    }

    public function test_dispositivo_de_um_paciente_nao_aparece_para_outro(): void
    {
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();
        $dispositivo = Dispositivo::factory()->create();

        $maria->dispositivos()->attach($dispositivo, ['ativo' => true]);

        $this->assertCount(1, $maria->dispositivos()->get());
        $this->assertCount(0, $joao->dispositivos()->get());
        $this->assertNull($joao->dispositivoAtual());
    }

    public function test_lista_apenas_dispositivos_ativos_no_catalogo(): void
    {
        Dispositivo::factory()->count(3)->create();
        Dispositivo::factory()->inativo()->create();

        $this->assertCount(3, Dispositivo::ativos()->get());
        $this->assertCount(4, Dispositivo::all());
    }
}
