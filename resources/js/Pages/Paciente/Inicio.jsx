import AnelDeVitalidade from '@/Components/UI/AnelDeVitalidade';
import CartaoAcao from '@/Components/UI/CartaoAcao';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { navegacaoDoPaciente } from '@/Layouts/navegacao';
import { Link } from '@inertiajs/react';
import { ChevronRight, MessageCircle } from 'lucide-react';

// Atalhos do painel: os mesmos itens da navegação, sem os que já estão em
// destaque na própria tela.
const ATALHOS = ['dispositivo', 'orientacoes', 'alertas', 'lembretes', 'conteudos', 'notificacoes'];

export default function Inicio({ painel }) {
    const atalhos = ATALHOS.map((id) =>
        navegacaoDoPaciente.find((item) => item.id === id),
    ).filter((item) => item && route().has(item.rota));

    const { resumoDaSemana } = painel;

    return (
        <LayoutDoAplicativo titulo="Início" secaoAtiva="inicio">
            <div className="flex flex-col gap-5">
                <div>
                    <h1 className="font-display text-2xl font-bold text-marinho">
                        Olá, {painel.primeiroNome} 👋
                    </h1>
                    <p className="text-sm text-cinza">
                        Como podemos ajudar você hoje?
                    </p>
                </div>

                <section className="flex flex-col items-center justify-between gap-4 rounded-3xl bg-teal-profundo p-6 sm:flex-row">
                    <div>
                        <p className="mb-1 text-xs font-medium text-teal-suave">
                            Cuidados realizados esta semana
                        </p>
                        <p className="text-sm text-white">
                            {resumoDaSemana.realizados} de{' '}
                            {resumoDaSemana.previstos} concluídos
                        </p>

                        {route().has('paciente.diario') && (
                            <Link
                                href={route('paciente.diario')}
                                className="mt-3 flex items-center gap-1 text-xs font-semibold text-white underline-offset-2 hover:underline"
                            >
                                Ver diário completo <ChevronRight size={13} />
                            </Link>
                        )}
                    </div>

                    <AnelDeVitalidade percentual={resumoDaSemana.percentual} />
                </section>

                {painel.dispositivo === null && (
                    <p className="rounded-2xl bg-ambar-suave p-4 text-xs leading-relaxed text-teal-profundo">
                        Você ainda não indicou qual dispositivo utiliza. As
                        orientações e os lembretes são personalizados a partir
                        dele.
                    </p>
                )}

                <div className="grid grid-cols-2 gap-3 md:grid-cols-3">
                    {atalhos.map((item) => (
                        <CartaoAcao
                            key={item.id}
                            rotulo={item.rotulo}
                            href={route(item.rota)}
                            Icone={item.Icone}
                            cor={item.cor}
                            badge={item.id === 'notificacoes' ? painel.naoLidas : 0}
                        />
                    ))}
                </div>

                {route().has('paciente.equipe') && (
                    <Link
                        href={route('paciente.equipe')}
                        className="flex w-full items-center justify-between rounded-2xl bg-white p-4 shadow-cartao"
                    >
                        <span className="flex items-center gap-3">
                            <span className="flex h-10 w-10 items-center justify-center rounded-full bg-lavanda-suave">
                                <MessageCircle size={18} className="text-lavanda" />
                            </span>
                            <span className="text-sm font-semibold text-marinho">
                                Falar com a equipe
                            </span>
                        </span>
                        <ChevronRight size={16} className="text-cinza" />
                    </Link>
                )}
            </div>
        </LayoutDoAplicativo>
    );
}
