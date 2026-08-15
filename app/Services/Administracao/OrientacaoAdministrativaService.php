<?php

declare(strict_types=1);

namespace App\Services\Administracao;

use App\Models\Orientacao;
use Illuminate\Support\Facades\DB;

/**
 * Salvar uma orientação envolve três tabelas (orientação, abas e passos) e a
 * reordenação de tudo — por isso a regra vive aqui, e não no Controller.
 */
class OrientacaoAdministrativaService
{
    /**
     * @param  array<string, mixed>  $dados
     */
    public function criarOrientacao(array $dados): Orientacao
    {
        return DB::transaction(function () use ($dados): Orientacao {
            $orientacao = Orientacao::create($this->camposDaOrientacao($dados));

            $this->substituirAbasEPassos($orientacao, $dados['abas']);

            return $orientacao;
        });
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function atualizarOrientacao(Orientacao $orientacao, array $dados): Orientacao
    {
        return DB::transaction(function () use ($orientacao, $dados): Orientacao {
            $orientacao->update($this->camposDaOrientacao($dados));

            $this->substituirAbasEPassos($orientacao, $dados['abas']);

            return $orientacao->refresh();
        });
    }

    public function removerOrientacao(Orientacao $orientacao): void
    {
        // Abas e passos saem em cascata, pela definição das migrations.
        $orientacao->delete();
    }

    /**
     * As abas são recriadas a cada gravação. É mais simples e seguro do que
     * casar item a item, e o volume é pequeno (poucas abas por orientação).
     *
     * @param  list<array{nome: string, passos: list<string>}>  $abas
     */
    private function substituirAbasEPassos(Orientacao $orientacao, array $abas): void
    {
        $orientacao->tiposDeCuidado()->delete();

        foreach (array_values($abas) as $ordemDaAba => $aba) {
            $tipoDeCuidado = $orientacao->tiposDeCuidado()->create([
                'nome' => $aba['nome'],
                'ordem' => $ordemDaAba,
            ]);

            foreach (array_values($aba['passos']) as $ordemDoPasso => $descricao) {
                $tipoDeCuidado->passos()->create([
                    'descricao' => $descricao,
                    'ordem' => $ordemDoPasso,
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $dados
     * @return array<string, mixed>
     */
    private function camposDaOrientacao(array $dados): array
    {
        return [
            'dispositivo_id' => $dados['dispositivo_id'],
            'titulo' => $dados['titulo'],
            'subtitulo' => $dados['subtitulo'] ?? null,
            'publicada' => $dados['publicada'] ?? true,
        ];
    }
}
