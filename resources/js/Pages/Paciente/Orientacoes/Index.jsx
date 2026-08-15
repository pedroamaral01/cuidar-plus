import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

export default function Index({ orientacoes, dispositivo }) {
    return (
        <LayoutDoAplicativo titulo="Orientações" secaoAtiva="orientacoes">
            <div className="flex flex-col gap-3">
                <p className="text-xs text-cinza">
                    {dispositivo
                        ? `Orientações disponíveis para ${dispositivo}:`
                        : 'Orientações disponíveis para o seu dispositivo:'}
                </p>

                {orientacoes.length === 0 && (
                    <p className="rounded-2xl bg-white p-6 text-center text-sm text-cinza shadow-cartao">
                        {dispositivo
                            ? 'Ainda não há orientações publicadas para o seu dispositivo.'
                            : 'Escolha o seu dispositivo em "Meu dispositivo" para ver as orientações.'}
                    </p>
                )}

                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    {orientacoes.map((orientacao) => (
                        <Link
                            key={orientacao.id}
                            href={route('paciente.orientacoes.show', orientacao.id)}
                            className="flex items-center justify-between rounded-2xl bg-white p-4 text-left shadow-cartao transition hover:shadow-md"
                        >
                            <span>
                                <span className="block text-sm font-semibold text-marinho">
                                    {orientacao.titulo}
                                </span>
                                {orientacao.subtitulo && (
                                    <span className="mt-0.5 block text-xs text-cinza">
                                        {orientacao.subtitulo}
                                    </span>
                                )}
                            </span>
                            <ChevronRight size={16} className="shrink-0 text-cinza" />
                        </Link>
                    ))}
                </div>
            </div>
        </LayoutDoAplicativo>
    );
}
