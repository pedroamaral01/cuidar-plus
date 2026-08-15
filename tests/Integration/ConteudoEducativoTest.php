<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\ConteudoEducativo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConteudoEducativoTest extends TestCase
{
    use RefreshDatabase;

    public function test_paciente_favorita_e_desfavorita_um_conteudo(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $conteudo = ConteudoEducativo::factory()->create();

        $paciente->conteudosFavoritos()->attach($conteudo);
        $this->assertCount(1, $paciente->conteudosFavoritos()->get());

        $paciente->conteudosFavoritos()->detach($conteudo);
        $this->assertCount(0, $paciente->conteudosFavoritos()->get());
    }

    public function test_favorito_de_um_paciente_nao_vaza_para_outro(): void
    {
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();
        $conteudo = ConteudoEducativo::factory()->create();

        $maria->conteudosFavoritos()->attach($conteudo);

        $this->assertCount(1, $maria->conteudosFavoritos()->get());
        $this->assertCount(0, $joao->conteudosFavoritos()->get());
        $this->assertSame([$maria->id], $conteudo->favoritadoPor()->pluck('usuarios.id')->all());
    }

    public function test_conteudo_de_video_guarda_a_url_e_dispensa_corpo(): void
    {
        $video = ConteudoEducativo::factory()->video()->create();

        $this->assertSame('video', $video->tipo->value);
        $this->assertNotNull($video->url_do_video);
        $this->assertNull($video->corpo);
    }

    public function test_conteudo_despublicado_fica_fora_da_listagem(): void
    {
        ConteudoEducativo::factory()->count(2)->create();
        ConteudoEducativo::factory()->despublicado()->create();

        $this->assertCount(2, ConteudoEducativo::publicados()->get());
    }

    public function test_apagar_paciente_remove_seus_favoritos(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $conteudo = ConteudoEducativo::factory()->create();
        $paciente->conteudosFavoritos()->attach($conteudo);

        $paciente->delete();

        $this->assertDatabaseCount('usuarios_conteudos_favoritos', 0);
        $this->assertModelExists($conteudo);
    }
}
