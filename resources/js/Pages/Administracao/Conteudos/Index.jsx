import TabelaAdmin from '@/Components/Administracao/TabelaAdmin';
import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import Selo from '@/Components/UI/Selo';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';

export default function Index({ conteudos }) {
    const colunas = [
        { chave: 'titulo', rotulo: 'Título' },
        {
            chave: 'tipo',
            rotulo: 'Tipo',
            formatar: (linha) => (
                <Selo cor={linha.tipo === 'video' ? 'lavender' : 'teal'}>
                    {linha.tipoRotulo}
                </Selo>
            ),
        },
        { chave: 'dispositivo', rotulo: 'Dispositivo' },
        { chave: 'favoritos', rotulo: 'Favoritado por' },
        {
            chave: 'publicado',
            rotulo: 'Publicado',
            formatar: (linha) => (
                <Selo cor={linha.publicado ? 'teal' : 'coral'}>
                    {linha.publicado ? 'Sim' : 'Não'}
                </Selo>
            ),
        },
    ];

    return (
        <LayoutDoAplicativo titulo="Conteúdos educativos" secaoAtiva="conteudos">
            <AvisoDeStatus />

            <TabelaAdmin
                titulo="Conteúdos educativos"
                subtitulo="Textos e vídeos disponibilizados aos pacientes."
                colunas={colunas}
                linhas={conteudos}
                rotaDeCriacao={route('administracao.conteudos.create')}
                rotuloDoBotao="Novo conteúdo"
                rotaDeEdicao={(linha) =>
                    route('administracao.conteudos.edit', linha.id)
                }
                rotaDeExclusao={(linha) =>
                    route('administracao.conteudos.destroy', linha.id)
                }
                descreverLinha={(linha) => linha.titulo}
                vazio="Nenhum conteúdo cadastrado."
            />
        </LayoutDoAplicativo>
    );
}
