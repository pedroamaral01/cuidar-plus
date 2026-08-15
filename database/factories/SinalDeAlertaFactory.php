<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GravidadeDoAlerta;
use App\Models\Dispositivo;
use App\Models\SinalDeAlerta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SinalDeAlerta>
 */
class SinalDeAlertaFactory extends Factory
{
    protected $model = SinalDeAlerta::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dispositivo_id' => Dispositivo::factory(),
            'nome' => fake()->words(2, true),
            'orientacao' => fake()->sentence(12),
            'gravidade' => fake()->randomElement(GravidadeDoAlerta::cases()),
            'publicado' => true,
        ];
    }

    /** Sinal geral, exibido para pacientes de qualquer dispositivo. */
    public function geral(): static
    {
        return $this->state(fn (array $atributos): array => ['dispositivo_id' => null]);
    }

    public function comGravidade(GravidadeDoAlerta $gravidade): static
    {
        return $this->state(fn (array $atributos): array => ['gravidade' => $gravidade]);
    }
}
