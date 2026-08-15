<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Enums\PerfilDeUsuario;
use App\Models\Dispositivo;
use App\Models\Orientacao;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Garante que a base de demonstração sobe íntegra — é ela que sustenta a
 * apresentação do MVP.
 */
class SeedersDeDemonstracaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_cria_catalogo_pacientes_e_conteudo_consistentes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertCount(6, Dispositivo::ativos()->get());
        $this->assertGreaterThanOrEqual(4, Orientacao::publicadas()->count());

        $maria = Usuario::where('email', 'maria@cuidarplus.local')->first();
        $this->assertNotNull($maria);
        $this->assertSame(PerfilDeUsuario::Paciente, $maria->perfil);
        $this->assertSame('Colostomia', $maria->dispositivoAtual()?->nome);

        // Os lembretes iniciais vêm do plano de cuidados do dispositivo.
        $this->assertCount(5, $maria->lembretes()->get());
        $this->assertCount(2, $maria->conteudosFavoritos()->get());
    }

    public function test_seed_cria_administrador_separado_dos_pacientes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Usuario::where('email', 'admin@cuidarplus.local')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->ehAdministrador());
        $this->assertCount(2, Usuario::pacientes()->get());
        $this->assertCount(1, Usuario::administradores()->get());
    }

    public function test_seed_pode_rodar_duas_vezes_sem_duplicar_dados(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertCount(6, Dispositivo::all());
        $this->assertCount(3, Usuario::all());
    }
}
