<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Lembrete;
use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use App\Policies\LembretePolicy;
use App\Policies\RegistroDeCuidadoPolicy;
use PHPUnit\Framework\TestCase;

/**
 * A regra crítica de isolamento verificada sem banco: só o dono acessa.
 */
class LembretePolicyTest extends TestCase
{
    public function test_dono_pode_ver_alterar_e_apagar_o_proprio_lembrete(): void
    {
        $policy = new LembretePolicy;
        $maria = $this->usuarioComId(1);
        $lembrete = $this->lembreteDoUsuario(1);

        $this->assertTrue($policy->view($maria, $lembrete));
        $this->assertTrue($policy->update($maria, $lembrete));
        $this->assertTrue($policy->delete($maria, $lembrete));
    }

    public function test_paciente_nao_alcanca_o_lembrete_de_outro_paciente(): void
    {
        $policy = new LembretePolicy;
        $joao = $this->usuarioComId(2);
        $lembreteDaMaria = $this->lembreteDoUsuario(1);

        $this->assertFalse($policy->view($joao, $lembreteDaMaria));
        $this->assertFalse($policy->update($joao, $lembreteDaMaria));
        $this->assertFalse($policy->delete($joao, $lembreteDaMaria));
    }

    public function test_registro_de_cuidado_segue_a_mesma_regra(): void
    {
        $policy = new RegistroDeCuidadoPolicy;
        $maria = $this->usuarioComId(1);
        $joao = $this->usuarioComId(2);

        $registro = new RegistroDeCuidado;
        $registro->usuario_id = 1;

        $this->assertTrue($policy->view($maria, $registro));
        $this->assertFalse($policy->view($joao, $registro));
        $this->assertFalse($policy->delete($joao, $registro));
    }

    private function usuarioComId(int $id): Usuario
    {
        $usuario = new Usuario;
        $usuario->id = $id;

        return $usuario;
    }

    private function lembreteDoUsuario(int $usuarioId): Lembrete
    {
        $lembrete = new Lembrete;
        $lembrete->usuario_id = $usuarioId;

        return $lembrete;
    }
}
