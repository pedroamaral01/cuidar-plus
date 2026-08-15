<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Carrega os dados dos 3 passos do cadastro entre o Form Request e o Service.
 *
 * Existe porque o cadastro chega em um formato (campos achatados do
 * formulário) e é consumido em outro (paciente + dispositivo + plano), e
 * porque o Service precisa ser testável sem uma requisição HTTP.
 */
final readonly class DadosDoCadastroDePaciente
{
    public function __construct(
        // Passo 1 — dados pessoais
        public string $nome,
        public string $email,
        public string $senha,
        public ?string $dataDeNascimento = null,
        public ?string $cpf = null,
        public ?string $telefone = null,
        public ?string $cuidadorNome = null,
        public ?string $cuidadorTelefone = null,
        // Passo 2 — dispositivo
        public ?int $dispositivoId = null,
        // Passo 3 — plano de cuidados
        public bool $aplicarPlanoDeCuidados = true,
    ) {}

    /**
     * @param  array<string, mixed>  $dados
     */
    public static function apartirDoFormulario(array $dados): self
    {
        return new self(
            nome: $dados['nome'],
            email: $dados['email'],
            senha: $dados['password'],
            dataDeNascimento: $dados['data_de_nascimento'] ?? null,
            cpf: $dados['cpf'] ?? null,
            telefone: $dados['telefone'] ?? null,
            cuidadorNome: $dados['cuidador_nome'] ?? null,
            cuidadorTelefone: $dados['cuidador_telefone'] ?? null,
            dispositivoId: isset($dados['dispositivo_id']) ? (int) $dados['dispositivo_id'] : null,
            aplicarPlanoDeCuidados: (bool) ($dados['aplicar_plano_de_cuidados'] ?? true),
        );
    }
}
