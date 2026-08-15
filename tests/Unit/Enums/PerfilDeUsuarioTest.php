<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\PerfilDeUsuario;
use PHPUnit\Framework\TestCase;

class PerfilDeUsuarioTest extends TestCase
{
    public function test_separa_paciente_de_administrador(): void
    {
        $this->assertTrue(PerfilDeUsuario::Paciente->ehPaciente());
        $this->assertFalse(PerfilDeUsuario::Paciente->ehAdministrador());

        $this->assertTrue(PerfilDeUsuario::Administrador->ehAdministrador());
        $this->assertFalse(PerfilDeUsuario::Administrador->ehPaciente());
    }

    public function test_guarda_o_valor_persistido_no_banco(): void
    {
        $this->assertSame('paciente', PerfilDeUsuario::Paciente->value);
        $this->assertSame('administrador', PerfilDeUsuario::Administrador->value);
    }

    public function test_expoe_rotulo_legivel_para_a_interface(): void
    {
        $this->assertSame('Paciente', PerfilDeUsuario::Paciente->rotulo());
        $this->assertSame('Administrador', PerfilDeUsuario::Administrador->rotulo());
    }
}
