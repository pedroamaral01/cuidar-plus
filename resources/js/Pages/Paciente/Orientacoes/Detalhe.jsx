import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { Link } from '@inertiajs/react';
import { AlertTriangle, ChevronLeft } from 'lucide-react';
import { useState } from 'react';

export default function Detalhe({ orientacao }) {
    const [abaAtiva, setAbaAtiva] = useState(orientacao.abas[0]?.id ?? null);

    const aba = orientacao.abas.find((item) => item.id === abaAtiva) ?? orientacao.abas[0];

    return (
        <LayoutDoAplicativo titulo={orientacao.titulo} secaoAtiva="orientacoes">
            <div className="flex flex-col gap-4">
                <Link
                    href={route('paciente.orientacoes.index')}
                    className="flex w-fit items-center gap-1 text-sm font-medium text-teal-profundo"
                >
                    <ChevronLeft size={16} /> Orientações
                </Link>

                <div>
                    <h2 className="font-display text-xl font-bold text-marinho">
                        {orientacao.titulo}
                    </h2>
                    {orientacao.subtitulo && (
                        <p className="text-sm text-cinza">{orientacao.subtitulo}</p>
                    )}
                </div>

                {/* Abas por tipo de cuidado */}
                <div
                    role="tablist"
                    aria-label="Tipos de cuidado"
                    className="flex flex-wrap gap-2"
                >
                    {orientacao.abas.map((item) => {
                        const selecionada = item.id === aba?.id;

                        return (
                            <button
                                key={item.id}
                                type="button"
                                role="tab"
                                id={`aba-${item.id}`}
                                aria-selected={selecionada}
                                aria-controls={`painel-${item.id}`}
                                onClick={() => setAbaAtiva(item.id)}
                                className={`rounded-xl px-4 py-2 text-xs font-semibold transition ${
                                    selecionada
                                        ? 'bg-teal-profundo text-white'
                                        : 'bg-teal-suave text-teal-profundo'
                                }`}
                            >
                                {item.nome}
                            </button>
                        );
                    })}
                </div>

                {aba && (
                    <div
                        role="tabpanel"
                        id={`painel-${aba.id}`}
                        aria-labelledby={`aba-${aba.id}`}
                        className="rounded-2xl bg-white p-5 shadow-cartao"
                    >
                        <p className="mb-3 text-xs font-semibold text-cinza">
                            PASSO A PASSO
                        </p>

                        <ol className="flex flex-col gap-3">
                            {aba.passos.map((passo, indice) => (
                                <li
                                    key={indice}
                                    className="flex items-start gap-3"
                                >
                                    <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-suave text-xs font-bold text-teal-profundo">
                                        {indice + 1}
                                    </span>
                                    <span className="pt-0.5 text-sm text-marinho">
                                        {passo}
                                    </span>
                                </li>
                            ))}
                        </ol>
                    </div>
                )}

                <div className="flex items-start gap-3 rounded-2xl bg-coral-suave p-4">
                    <AlertTriangle
                        size={18}
                        className="mt-0.5 shrink-0 text-coral"
                    />
                    <p className="text-xs leading-relaxed text-teal-profundo">
                        Em caso de dúvida durante o procedimento ou sinais de
                        alerta, procure atendimento imediato.
                    </p>
                </div>
            </div>
        </LayoutDoAplicativo>
    );
}
