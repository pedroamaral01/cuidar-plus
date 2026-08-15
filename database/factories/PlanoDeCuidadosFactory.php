<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Dispositivo;
use App\Models\PlanoDeCuidados;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlanoDeCuidados>
 */
class PlanoDeCuidadosFactory extends Factory
{
    protected $model = PlanoDeCuidados::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dispositivo_id' => Dispositivo::factory(),
            'nome' => 'Plano de cuidados '.fake()->word(),
            'descricao' => fake()->sentence(8),
            'ativo' => true,
        ];
    }
}
