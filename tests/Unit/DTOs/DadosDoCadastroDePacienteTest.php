<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\DadosDoCadastroDePaciente;
use PHPUnit\Framework\TestCase;

class DadosDoCadastroDePacienteTest extends TestCase
{
    public function test_monta_os_tres_passos_a_partir_do_formulario(): void
    {
        $dados = DadosDoCadastroDePaciente::apartirDoFormulario([
            'nome' => 'Maria Aparecida',
            'email' => 'maria@exemplo.com',
            'password' => 'senha1234',
            'data_de_nascimento' => '1958-04-12',
            'telefone' => '(31) 99999-0000',
            'cuidador_nome' => 'Joana',
            'cuidador_telefone' => '(31) 98888-1010',
            'dispositivo_id' => '3',
        ]);

        $this->assertSame('Maria Aparecida', $dados->nome);
        $this->assertSame('senha1234', $dados->senha);
        $this->assertSame('Joana', $dados->cuidadorNome);
        $this->assertSame(3, $dados->dispositivoId, 'O id do dispositivo deve chegar como inteiro.');
        $this->assertTrue($dados->aplicarPlanoDeCuidados);
    }

    public function test_campos_opcionais_ficam_nulos_quando_nao_enviados(): void
    {
        $dados = DadosDoCadastroDePaciente::apartirDoFormulario([
            'nome' => 'João Pereira',
            'email' => 'joao@exemplo.com',
            'password' => 'senha1234',
        ]);

        $this->assertNull($dados->cpf);
        $this->assertNull($dados->telefone);
        $this->assertNull($dados->cuidadorNome);
        $this->assertNull($dados->dispositivoId);
    }

    public function test_paciente_pode_recusar_o_plano_de_cuidados_sugerido(): void
    {
        $dados = DadosDoCadastroDePaciente::apartirDoFormulario([
            'nome' => 'João Pereira',
            'email' => 'joao@exemplo.com',
            'password' => 'senha1234',
            'aplicar_plano_de_cuidados' => false,
        ]);

        $this->assertFalse($dados->aplicarPlanoDeCuidados);
    }
}
