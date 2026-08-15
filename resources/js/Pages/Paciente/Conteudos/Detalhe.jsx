import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { Link, router } from '@inertiajs/react';
import { ChevronLeft, ExternalLink, Star } from 'lucide-react';

export default function Detalhe({ conteudo }) {
    const alternarFavorito = () =>
        router.post(
            route('paciente.conteudos.favorito', conteudo.id),
            {},
            { preserveScroll: true },
        );

    return (
        <LayoutDoAplicativo titulo={conteudo.titulo} secaoAtiva="conteudos">
            <div className="flex flex-col gap-4">
                <Link
                    href={route('paciente.conteudos')}
                    className="flex w-fit items-center gap-1 text-sm font-medium text-teal-profundo"
                >
                    <ChevronLeft size={16} /> Conteúdos
                </Link>

                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h2 className="font-display text-xl font-bold text-marinho">
                            {conteudo.titulo}
                        </h2>
                        {conteudo.resumo && (
                            <p className="mt-1 text-sm text-cinza">
                                {conteudo.resumo}
                            </p>
                        )}
                    </div>

                    <button
                        type="button"
                        onClick={alternarFavorito}
                        aria-pressed={conteudo.favorito}
                        aria-label={
                            conteudo.favorito
                                ? 'Remover dos favoritos'
                                : 'Favoritar conteúdo'
                        }
                        className="shrink-0 rounded-full bg-ambar-suave p-2.5"
                    >
                        <Star
                            size={18}
                            className="text-ambar"
                            fill={conteudo.favorito ? 'currentColor' : 'none'}
                        />
                    </button>
                </div>

                <div className="rounded-2xl bg-white p-5 shadow-cartao">
                    {conteudo.tipo === 'video' ? (
                        <a
                            href={conteudo.urlDoVideo}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center justify-center gap-2 rounded-2xl bg-lavanda-suave px-4 py-6 text-sm font-semibold text-lavanda"
                        >
                            <ExternalLink size={16} /> Assistir ao vídeo
                        </a>
                    ) : (
                        <div className="flex flex-col gap-3">
                            {(conteudo.corpo ?? '')
                                .split('\n')
                                .filter((paragrafo) => paragrafo.trim() !== '')
                                .map((paragrafo, indice) => (
                                    <p
                                        key={indice}
                                        className="text-sm leading-relaxed text-marinho"
                                    >
                                        {paragrafo}
                                    </p>
                                ))}
                        </div>
                    )}
                </div>

                <p className="rounded-2xl bg-teal-suave p-4 text-xs leading-relaxed text-teal-profundo">
                    Este conteúdo é informativo e não substitui a orientação da
                    equipe de saúde que acompanha você.
                </p>
            </div>
        </LayoutDoAplicativo>
    );
}
