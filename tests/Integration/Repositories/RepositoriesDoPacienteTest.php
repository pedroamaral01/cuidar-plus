<?php

declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Enums\GravidadeDoAlerta;
use App\Enums\TipoDeLembrete;
use App\Models\ConteudoEducativo;
use App\Models\Dispositivo;
use App\Models\Lembrete;
use App\Models\Orientacao;
use App\Models\PassoDeOrientacao;
use App\Models\RegistroDeCuidado;
use App\Models\SinalDeAlerta;
use App\Models\TipoDeCuidado;
use App\Models\Usuario;
use App\Repositories\Contracts\ConteudoEducativoRepositoryInterface;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use App\Repositories\Contracts\OrientacaoRepositoryInterface;
use App\Repositories\Contracts\RegistroDeCuidadoRepositoryInterface;
use App\Repositories\Contracts\SinalDeAlertaRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoriesDoPacienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_container_resolve_cada_interface_para_a_implementacao_eloquent(): void
    {
        $contratos = [
            LembreteRepositoryInterface::class,
            RegistroDeCuidadoRepositoryInterface::class,
            OrientacaoRepositoryInterface::class,
            SinalDeAlertaRepositoryInterface::class,
            ConteudoEducativoRepositoryInterface::class,
        ];

        foreach ($contratos as $contrato) {
            $this->assertInstanceOf($contrato, app($contrato));
        }
    }

    public function test_lembretes_do_paciente_vem_ordenados_por_horario(): void
    {
        $repositorio = app(LembreteRepositoryInterface::class);
        $paciente = Usuario::factory()->paciente()->create();

        Lembrete::factory()->noHorario('20:00:00')->create(['usuario_id' => $paciente->id, 'titulo' => 'Noite']);
        Lembrete::factory()->noHorario('08:00:00')->create(['usuario_id' => $paciente->id, 'titulo' => 'Manhã']);
        Lembrete::factory()->noHorario('12:00:00')->create(['usuario_id' => $paciente->id, 'titulo' => 'Meio-dia']);

        $titulos = $repositorio->buscarPorPaciente($paciente)->pluck('titulo')->all();

        $this->assertSame(['Manhã', 'Meio-dia', 'Noite'], $titulos);
    }

    public function test_busca_por_paciente_nao_traz_lembrete_de_outro(): void
    {
        $repositorio = app(LembreteRepositoryInterface::class);
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        Lembrete::factory()->count(2)->create(['usuario_id' => $maria->id]);
        Lembrete::factory()->create(['usuario_id' => $joao->id]);

        $this->assertCount(2, $repositorio->buscarPorPaciente($maria));
        $this->assertCount(1, $repositorio->buscarPorPaciente($joao));
    }

    public function test_janela_de_horario_ignora_quem_ja_foi_notificado_hoje(): void
    {
        $repositorio = app(LembreteRepositoryInterface::class);
        $paciente = Usuario::factory()->paciente()->create();

        $pendente = Lembrete::factory()->noHorario('08:00:00')->create(['usuario_id' => $paciente->id]);
        Lembrete::factory()->noHorario('08:05:00')->create([
            'usuario_id' => $paciente->id,
            'notificado_em' => now(),
        ]);
        Lembrete::factory()->noHorario('08:10:00')->inativo()->create(['usuario_id' => $paciente->id]);
        Lembrete::factory()->noHorario('15:00:00')->create(['usuario_id' => $paciente->id]);

        $encontrados = $repositorio->buscarAtivosNaJanelaDeHorario('07:55:00', '08:15:00');

        $this->assertCount(1, $encontrados);
        $this->assertSame($pendente->id, $encontrados->first()->id);
    }

    public function test_lembrete_notificado_ontem_volta_a_ser_elegivel_hoje(): void
    {
        $repositorio = app(LembreteRepositoryInterface::class);
        $paciente = Usuario::factory()->paciente()->create();

        Lembrete::factory()->noHorario('08:00:00')->create([
            'usuario_id' => $paciente->id,
            'notificado_em' => now()->subDay(),
        ]);

        $this->assertCount(1, $repositorio->buscarAtivosNaJanelaDeHorario('07:00:00', '09:00:00'));
    }

    public function test_contagem_por_tipo_no_periodo_alimenta_o_resumo_semanal(): void
    {
        $repositorio = app(RegistroDeCuidadoRepositoryInterface::class);
        $paciente = Usuario::factory()->paciente()->create();

        RegistroDeCuidado::factory()->count(3)->create([
            'usuario_id' => $paciente->id,
            'tipo' => TipoDeLembrete::Troca,
            'realizado_em' => now()->subDay(),
        ]);
        RegistroDeCuidado::factory()->create([
            'usuario_id' => $paciente->id,
            'tipo' => TipoDeLembrete::Higiene,
            'realizado_em' => now()->subDay(),
        ]);
        // Fora do período — não deve entrar na conta.
        RegistroDeCuidado::factory()->create([
            'usuario_id' => $paciente->id,
            'tipo' => TipoDeLembrete::Troca,
            'realizado_em' => now()->subMonth(),
        ]);

        $contagem = $repositorio->contarPorTipoNoPeriodo($paciente, now()->subWeek(), now());

        $this->assertSame(3, $contagem['troca']);
        $this->assertSame(1, $contagem['higiene']);
    }

    public function test_orientacao_de_outro_dispositivo_nao_e_encontrada(): void
    {
        $repositorio = app(OrientacaoRepositoryInterface::class);
        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        $orientacaoDaSonda = Orientacao::factory()->create(['dispositivo_id' => $sonda->id]);

        $this->assertNull($repositorio->buscarPublicadaComPassos($orientacaoDaSonda->id, $colostomia->id));
        $this->assertNotNull($repositorio->buscarPublicadaComPassos($orientacaoDaSonda->id, $sonda->id));
    }

    public function test_detalhe_da_orientacao_ja_traz_abas_e_passos_carregados(): void
    {
        $repositorio = app(OrientacaoRepositoryInterface::class);
        $dispositivo = Dispositivo::factory()->create();
        $orientacao = Orientacao::factory()->create(['dispositivo_id' => $dispositivo->id]);

        $aba = TipoDeCuidado::create(['orientacao_id' => $orientacao->id, 'nome' => 'Troca', 'ordem' => 0]);
        PassoDeOrientacao::create(['tipo_de_cuidado_id' => $aba->id, 'descricao' => 'Lave as mãos.', 'ordem' => 0]);

        $encontrada = $repositorio->buscarPublicadaComPassos($orientacao->id, $dispositivo->id);

        $this->assertTrue($encontrada->relationLoaded('tiposDeCuidado'));
        $this->assertTrue($encontrada->tiposDeCuidado->first()->relationLoaded('passos'));
        $this->assertSame('Lave as mãos.', $encontrada->tiposDeCuidado->first()->passos->first()->descricao);
    }

    public function test_sinais_de_alerta_vem_com_os_mais_graves_primeiro(): void
    {
        $repositorio = app(SinalDeAlertaRepositoryInterface::class);

        SinalDeAlerta::factory()->geral()->comGravidade(GravidadeDoAlerta::Baixa)->create(['nome' => 'Coceira leve']);
        SinalDeAlerta::factory()->geral()->comGravidade(GravidadeDoAlerta::Alta)->create(['nome' => 'Sangramento']);
        SinalDeAlerta::factory()->geral()->comGravidade(GravidadeDoAlerta::Media)->create(['nome' => 'Vermelhidão']);

        $nomes = $repositorio->buscarPublicadosParaDispositivo(null)->pluck('nome')->all();

        $this->assertSame(['Sangramento', 'Vermelhidão', 'Coceira leve'], $nomes);
    }

    public function test_pacientes_afetados_por_sinal_de_dispositivo_sao_so_os_que_o_usam(): void
    {
        $repositorio = app(SinalDeAlertaRepositoryInterface::class);
        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();
        Usuario::factory()->administrador()->create();

        $maria->dispositivos()->attach($colostomia, ['ativo' => true]);
        $joao->dispositivos()->attach($sonda, ['ativo' => true]);

        $afetados = $repositorio->buscarPacientesAfetados($colostomia->id);

        $this->assertCount(1, $afetados);
        $this->assertSame($maria->id, $afetados->first()->id);
    }

    public function test_sinal_geral_afeta_todos_os_pacientes_mas_nenhum_administrador(): void
    {
        $repositorio = app(SinalDeAlertaRepositoryInterface::class);

        Usuario::factory()->count(3)->paciente()->create();
        Usuario::factory()->administrador()->create();

        $this->assertCount(3, $repositorio->buscarPacientesAfetados(null));
    }

    public function test_listagem_de_conteudos_ja_diz_quais_sao_favoritos(): void
    {
        $repositorio = app(ConteudoEducativoRepositoryInterface::class);
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        $favoritado = ConteudoEducativo::factory()->create(['titulo' => 'A favoritado']);
        ConteudoEducativo::factory()->create(['titulo' => 'B comum']);

        $maria->conteudosFavoritos()->attach($favoritado);

        $daMaria = $repositorio->buscarPublicadosParaPaciente($maria, null);
        $doJoao = $repositorio->buscarPublicadosParaPaciente($joao, null);

        $this->assertTrue((bool) $daMaria->firstWhere('titulo', 'A favoritado')->favorito);
        $this->assertFalse((bool) $daMaria->firstWhere('titulo', 'B comum')->favorito);
        // O favorito da Maria não pode aparecer marcado para o João.
        $this->assertFalse((bool) $doJoao->firstWhere('titulo', 'A favoritado')->favorito);
    }

    public function test_alternar_favorito_liga_e_desliga(): void
    {
        $repositorio = app(ConteudoEducativoRepositoryInterface::class);
        $paciente = Usuario::factory()->paciente()->create();
        $conteudo = ConteudoEducativo::factory()->create();

        $this->assertTrue($repositorio->alternarFavorito($paciente, $conteudo));
        $this->assertFalse($repositorio->alternarFavorito($paciente, $conteudo));
        $this->assertCount(0, $paciente->conteudosFavoritos()->get());
    }
}
