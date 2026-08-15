import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { Link, router } from '@inertiajs/react';
import { Star } from 'lucide-react';
import { useState } from 'react';

const FILTROS = [
    { id: 'todos', rotulo: 'Todos' },
    { id: 'video', rotulo: 'Vídeos' },
    { id: 'favoritos', rotulo: 'Favoritos' },
];

export default function Index({ conteudos }) {
    // Filtro é preferência de tela e vive só no componente — nada de
    // localStorage, conforme a regra de segurança do projeto.
    const [filtro, setFiltro] = useState('todos');

    const filtrados = conteudos.filter((conteudo) => {
        if (filtro === 'todos') return true;
        if (filtro === 'favoritos') return conteudo.favorito;

        return conteudo.tipo === filtro;
    });

    const alternarFavorito = (conteudo) =>
        router.post(
            route('paciente.conteudos.favorito', conteudo.id),
            {},
            { preserveScroll: true },
        );

    return (
        <LayoutDoAplicativo titulo="Conteúdos" secaoAtiva="conteudos">
            <div className="flex flex-col gap-4">
                <AvisoDeStatus />

                <div className="flex flex-wrap gap-2" role="tablist" aria-label="Filtro">
                    {FILTROS.map((opcao) => {
                        const ativo = filtro === opcao.id;

                        return (
                            <button
                                key={opcao.id}
                                type="button"
                                role="tab"
                                aria-selected={ativo}
                                onClick={() => setFiltro(opcao.id)}
                                className={`rounded-xl px-4 py-2 text-xs font-semibold transition ${
                                    ativo
                                        ? 'bg-teal-profundo text-white'
                                        : 'bg-teal-suave text-teal-profundo'
                                }`}
                            >
                                {opcao.rotulo}
                            </button>
                        );
                    })}
                </div>

                {filtrados.length === 0 && (
                    <p className="rounded-2xl bg-white p-6 text-center text-sm text-cinza shadow-cartao">
                        {filtro === 'favoritos'
                            ? 'Você ainda não favoritou nenhum conteúdo. Toque na estrela para salvar.'
                            : 'Nenhum conteúdo disponível por aqui ainda.'}
                    </p>
                )}

                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    {filtrados.map((conteudo) => {
                        const cores = classesDaCor(conteudo.cor);

                        return (
                            <article
                                key={conteudo.id}
                                className="overflow-hidden rounded-2xl bg-white shadow-cartao"
                            >
                                <Link
                                    href={route('paciente.conteudos.show', conteudo.id)}
                                    className={`flex h-24 items-center justify-center ${cores.fundo}`}
                                    aria-label={`Abrir ${conteudo.titulo}`}
                                >
                                    <IconeDinamico
                                        nome={conteudo.icone}
                                        size={30}
                                        className={cores.texto}
                                    />
                                </Link>

                                <div className="flex items-start justify-between gap-2 p-4">
                                    <Link
                                        href={route(
                                            'paciente.conteudos.show',
                                            conteudo.id,
                                        )}
                                        className="min-w-0"
                                    >
                                        <p className="text-sm font-semibold text-marinho">
                                            {conteudo.titulo}
                                        </p>
                                        <p className="mt-1 text-xs text-cinza">
                                            {conteudo.tipoRotulo}
                                        </p>
                                    </Link>

                                    <button
                                        type="button"
                                        onClick={() => alternarFavorito(conteudo)}
                                        aria-pressed={conteudo.favorito}
                                        aria-label={
                                            conteudo.favorito
                                                ? `Remover ${conteudo.titulo} dos favoritos`
                                                : `Favoritar ${conteudo.titulo}`
                                        }
                                        className="shrink-0 rounded-lg p-1"
                                    >
                                        <Star
                                            size={17}
                                            className="text-ambar"
                                            fill={
                                                conteudo.favorito
                                                    ? 'currentColor'
                                                    : 'none'
                                            }
                                        />
                                    </button>
                                </div>
                            </article>
                        );
                    })}
                </div>
            </div>
        </LayoutDoAplicativo>
    );
}
