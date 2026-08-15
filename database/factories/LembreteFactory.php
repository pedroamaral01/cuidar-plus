<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TipoDeLembrete;
use App\Models\Lembrete;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lembrete>
 */
class LembreteFactory extends Factory
{
    protected $model = Lembrete::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipo = fake()->randomElement(TipoDeLembrete::cases());

        return [
            'usuario_id' => Usuario::factory()->paciente(),
            'titulo' => $tipo->rotulo(),
            'tipo' => $tipo,
            'horario' => fake()->time('H:i:s'),
            'ativo' => true,
        ];
    }

    public function inativo(): static
    {
        return $this->state(fn (array $atributos): array => ['ativo' => false]);
    }

    public function noHorario(string $horario): static
    {
        return $this->state(fn (array $atributos): array => ['horario' => $horario]);
    }
}
