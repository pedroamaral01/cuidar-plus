<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TipoDeConteudo;
use App\Models\ConteudoEducativo;
use Illuminate\Database\Seeder;

class ConteudoEducativoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->conteudos() as $dados) {
            ConteudoEducativo::updateOrCreate(
                ['titulo' => $dados['titulo']],
                [
                    'dispositivo_id' => null,
                    'resumo' => $dados['resumo'],
                    'corpo' => $dados['corpo'],
                    'tipo' => $dados['tipo'],
                    'url_do_video' => $dados['url_do_video'],
                    'publicado' => true,
                ],
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function conteudos(): array
    {
        return [
            [
                'titulo' => 'Como identificar sinais de infecção',
                'resumo' => 'Vermelhidão, calor, dor e secreção são os sinais que mais pedem atenção.',
                'corpo' => "A pele ao redor do dispositivo deve estar íntegra e com a mesma cor da pele vizinha.\n\n".
                    "Procure a equipe se notar vermelhidão que aumenta, calor no local, dor crescente, secreção com cheiro forte ou febre.\n\n".
                    'Registre no diário quando o sinal começou — isso ajuda muito a equipe a avaliar.',
                'tipo' => TipoDeConteudo::Texto,
                'url_do_video' => null,
            ],
            [
                'titulo' => 'Vídeo: troca da bolsa passo a passo',
                'resumo' => 'Demonstração completa da troca, do preparo do material à fixação.',
                'corpo' => null,
                'tipo' => TipoDeConteudo::Video,
                'url_do_video' => 'https://www.youtube.com/watch?v=exemplo-troca',
            ],
            [
                'titulo' => 'Alimentação após a alta hospitalar',
                'resumo' => 'O que ajuda, o que evitar e como reintroduzir alimentos com segurança.',
                'corpo' => "Reintroduza os alimentos aos poucos, um de cada vez, observando como o seu corpo responde.\n\n".
                    "Mastigue bem e mantenha os horários das refeições. Beba líquidos ao longo do dia.\n\n".
                    'Nenhum alimento é proibido por regra: o que importa é observar e conversar com a equipe.',
                'tipo' => TipoDeConteudo::Texto,
                'url_do_video' => null,
            ],
            [
                'titulo' => 'Vídeo: higienização correta do estoma',
                'resumo' => 'Como limpar a região sem agredir a pele.',
                'corpo' => null,
                'tipo' => TipoDeConteudo::Video,
                'url_do_video' => 'https://www.youtube.com/watch?v=exemplo-higiene',
            ],
            [
                'titulo' => 'Quando procurar a equipe de saúde',
                'resumo' => 'Um guia curto para não ficar na dúvida.',
                'corpo' => "Procure atendimento imediato em caso de sangramento intenso, dor forte que não passa, febre alta ou ausência de eliminação por muitas horas.\n\n".
                    "Entre em contato com a equipe, sem urgência, para vazamentos frequentes, irritação na pele e dúvidas sobre o material.\n\n".
                    'Na dúvida, pergunte. O Cuidar+ é um apoio e não substitui a avaliação profissional.',
                'tipo' => TipoDeConteudo::Texto,
                'url_do_video' => null,
            ],
        ];
    }
}
