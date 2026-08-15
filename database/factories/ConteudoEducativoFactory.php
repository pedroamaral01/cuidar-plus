<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TipoDeConteudo;
use App\Models\ConteudoEducativo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConteudoEducativo>
 */
class ConteudoEducativoFactory extends Factory
{
    protected $model = ConteudoEducativo::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dispositivo_id' => null,
            'titulo' => fake()->sentence(5),
            'resumo' => fake()->sentence(10),
            'corpo' => fake()->paragraphs(3, true),
            'tipo' => TipoDeConteudo::Texto,
            'url_do_video' => null,
            'publicado' => true,
        ];
    }

    public function video(): static
    {
        return $this->state(fn (array $atributos): array => [
            'tipo' => TipoDeConteudo::Video,
            'url_do_video' => 'https://www.youtube.com/watch?v='.fake()->lexify('???????????'),
            'corpo' => null,
        ]);
    }

    public function despublicado(): static
    {
        return $this->state(fn (array $atributos): array => ['publicado' => false]);
    }
}
