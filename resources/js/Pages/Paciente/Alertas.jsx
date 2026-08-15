import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { AlertTriangle, ChevronRight } from 'lucide-react';
import { useState } from 'react';

export default function Alertas({ sinais }) {
    const [aberto, setAberto] = useState(null);

    return (
        <LayoutDoAplicativo titulo="Sinais de alerta" secaoAtiva="alertas">
            <div className="flex flex-col gap-3">
                <p className="text-xs text-cinza">
                    O que você está sentindo ou observando?
                </p>

                {sinais.length === 0 && (
                    <p className="rounded-2xl bg-white p-6 text-center text-sm text-cinza shadow-cartao">
                        Nenhum sinal de alerta cadastrado para o seu dispositivo
                        ainda.
                    </p>
                )}

                <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    {sinais.map((sinal) => {
                        const cores = classesDaCor(sinal.cor);
                        const expandido = aberto === sinal.id;

                        return (
                            <div
                                key={sinal.id}
                                className="overflow-hidden rounded-2xl bg-white shadow-cartao"
                            >
                                <button
                                    type="button"
                                    aria-expanded={expandido}
                                    aria-controls={`sinal-${sinal.id}`}
                                    onClick={() =>
                                        setAberto(expandido ? null : sinal.id)
                                    }
                                    className="flex w-full items-center justify-between p-4"
                                >
                                    <span className="flex items-center gap-3">
                                        <span
                                            className={`h-2 w-2 shrink-0 rounded-full ${cores.ponto}`}
                                        />
                                        <span className="text-left text-sm font-medium text-marinho">
                                            {sinal.nome}
                                        </span>
                                        <span className="sr-only">
                                            Gravidade {sinal.gravidadeRotulo}
                                        </span>
                                    </span>

                                    <ChevronRight
                                        size={16}
                                        className={`shrink-0 text-cinza transition-transform ${
                                            expandido ? 'rotate-90' : ''
                                        }`}
                                    />
                                </button>

                                {expandido && (
                                    <p
                                        id={`sinal-${sinal.id}`}
                                        className="px-4 pb-4 text-xs leading-relaxed text-cinza"
                                    >
                                        {sinal.orientacao}
                                    </p>
                                )}
                            </div>
                        );
                    })}
                </div>

                <div className="mt-2 flex items-start gap-3 rounded-2xl bg-coral-suave p-4">
                    <AlertTriangle
                        size={18}
                        className="mt-0.5 shrink-0 text-coral"
                    />
                    <p className="text-xs leading-relaxed text-teal-profundo">
                        Em caso de urgência, procure atendimento profissional
                        imediatamente. O Cuidar+ não substitui a avaliação da
                        equipe de saúde.
                    </p>
                </div>
            </div>
        </LayoutDoAplicativo>
    );
}
