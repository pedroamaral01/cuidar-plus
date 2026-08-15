import LogoMarca from '@/Components/Marca/LogoMarca';
import { classesDaCor } from '@/Components/UI/paleta';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Bell, LogOut, MoreHorizontal, X } from 'lucide-react';
import { useEffect, useState } from 'react';

import {
    fixosNaBarraInferior,
    navegacaoDoPerfil,
} from './navegacao';

/**
 * Casca única da SPA. É o mesmo componente que vira sidebar no desktop e barra
 * inferior + painel "Mais" no celular — não são duas aplicações (seção 2.2).
 */
export default function LayoutDoAplicativo({ titulo, secaoAtiva, children }) {
    const { auth, naoLidas = 0 } = usePage().props;
    const perfil = auth.usuario?.perfil ?? 'paciente';

    const [maisAberto, setMaisAberto] = useState(false);

    // Só entram na navegação os itens cuja rota já existe no backend.
    const itens = navegacaoDoPerfil(perfil).filter((item) =>
        route().has(item.rota),
    );

    const idsFixos = fixosNaBarraInferior[perfil] ?? fixosNaBarraInferior.paciente;
    const itensFixos = idsFixos
        .map((id) => itens.find((item) => item.id === id))
        .filter(Boolean);
    const itensDoMais = itens.filter((item) => !idsFixos.includes(item.id));

    const rotaDeNotificacoes = itens.find((item) => item.id === 'notificacoes')?.rota;

    // Fecha o painel "Mais" ao navegar, para não ficar sobreposto à tela nova.
    useEffect(() => router.on('navigate', () => setMaisAberto(false)), []);

    const sair = (evento) => {
        evento.preventDefault();
        router.post(route('logout'));
    };

    return (
        <div className="flex h-screen flex-col bg-creme md:flex-row">
            <Head title={titulo} />

            {/* ---------- Sidebar (desktop) ---------- */}
            <aside className="hidden shrink-0 bg-teal-profundo md:flex md:w-64 md:flex-col">
                <div className="flex items-center gap-2 px-6 py-6">
                    <LogoMarca tamanho={30} />
                    <span className="font-display text-lg font-bold text-white">
                        Cuidar+
                    </span>
                </div>

                <nav
                    aria-label="Navegação principal"
                    className="flex flex-1 flex-col gap-1 overflow-y-auto px-3"
                >
                    {itens.map(({ id, rotulo, rota, Icone }) => {
                        const ativo = secaoAtiva === id;

                        return (
                            <Link
                                key={id}
                                href={route(rota)}
                                aria-current={ativo ? 'page' : undefined}
                                className={`flex items-center gap-3 rounded-xl px-3 py-2.5 text-left transition ${
                                    ativo ? 'bg-white/15' : 'hover:bg-white/8'
                                }`}
                            >
                                <Icone
                                    size={18}
                                    className={ativo ? 'text-white' : 'text-teal-suave'}
                                />
                                <span
                                    className={`flex-1 text-sm font-medium ${
                                        ativo ? 'text-white' : 'text-teal-suave'
                                    }`}
                                >
                                    {rotulo}
                                </span>
                                {id === 'notificacoes' && naoLidas > 0 && (
                                    <span className="flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-coral px-1 text-[10px] font-bold text-white">
                                        {naoLidas}
                                    </span>
                                )}
                            </Link>
                        );
                    })}
                </nav>

                <div className="px-3 pt-2 pb-5">
                    <button
                        type="button"
                        onClick={sair}
                        className="flex w-full items-center gap-3 rounded-xl bg-white/10 px-3 py-2.5 transition hover:bg-white/15"
                    >
                        <LogOut size={18} className="text-teal-suave" />
                        <span className="text-sm font-medium text-teal-suave">
                            Sair
                        </span>
                    </button>
                    <p className="mt-3 px-3 text-[11px] text-white/50">
                        {perfil === 'administrador'
                            ? 'Área administrativa'
                            : 'Área do paciente'}
                    </p>
                </div>
            </aside>

            {/* ---------- Cabeçalho (celular) ---------- */}
            <header className="flex shrink-0 items-center justify-between border-b border-teal-suave bg-white px-4 py-3 md:hidden">
                <div className="flex items-center gap-2">
                    <LogoMarca tamanho={24} />
                    <span className="font-display text-base font-bold text-marinho">
                        {titulo ?? 'Cuidar+'}
                    </span>
                </div>

                {rotaDeNotificacoes && (
                    <Link
                        href={route(rotaDeNotificacoes)}
                        aria-label={`Notificações${naoLidas > 0 ? `, ${naoLidas} não lidas` : ''}`}
                        className="relative rounded-full bg-teal-suave p-2"
                    >
                        <Bell size={16} className="text-teal-profundo" />
                        {naoLidas > 0 && (
                            <span className="absolute top-0.5 right-0.5 h-2 w-2 rounded-full bg-coral" />
                        )}
                    </Link>
                )}
            </header>

            <div className="flex flex-1 flex-col overflow-hidden">
                {/* ---------- Barra superior (desktop) ---------- */}
                <div className="hidden shrink-0 items-center justify-between border-b border-teal-suave px-8 py-5 md:flex">
                    <h1 className="font-display text-xl font-bold text-marinho">
                        {titulo}
                    </h1>

                    <div className="flex items-center gap-4">
                        {rotaDeNotificacoes && (
                            <Link
                                href={route(rotaDeNotificacoes)}
                                aria-label={`Notificações${naoLidas > 0 ? `, ${naoLidas} não lidas` : ''}`}
                                className="relative rounded-full bg-teal-suave p-2"
                            >
                                <Bell size={17} className="text-teal-profundo" />
                                {naoLidas > 0 && (
                                    <span className="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-coral px-1 text-[9px] font-bold text-white">
                                        {naoLidas}
                                    </span>
                                )}
                            </Link>
                        )}

                        <span className="flex h-9 w-9 items-center justify-center rounded-full bg-teal text-sm font-semibold text-white">
                            {auth.usuario?.iniciais ?? '?'}
                        </span>
                    </div>
                </div>

                {/*
                  scroll-region avisa o Inertia de que a rolagem da página
                  acontece aqui, e não na janela. Sem isso, a rolagem não volta
                  ao topo depois de uma ação e o paciente não vê a confirmação,
                  que fica no início da tela.
                */}
                <main
                    scroll-region="true"
                    className="flex-1 overflow-y-auto px-4 py-5 pb-24 md:px-8 md:pb-8"
                >
                    <div className="mx-auto w-full max-w-5xl">{children}</div>
                </main>
            </div>

            {/* ---------- Barra inferior (celular) ---------- */}
            <nav
                aria-label="Navegação principal"
                className="fixed inset-x-0 bottom-0 flex h-16 items-center justify-between border-t border-teal-suave bg-white px-1 md:hidden"
            >
                {itensFixos.map(({ id, rotulo, rota, Icone }) => {
                    const ativo = secaoAtiva === id;

                    return (
                        <Link
                            key={id}
                            href={route(rota)}
                            aria-current={ativo ? 'page' : undefined}
                            className="relative flex h-full flex-1 flex-col items-center justify-center gap-1"
                        >
                            <Icone
                                size={19}
                                strokeWidth={ativo ? 2.6 : 2}
                                className={ativo ? 'text-teal-profundo' : 'text-cinza'}
                            />
                            <span
                                className={`text-[10px] font-medium ${
                                    ativo ? 'text-teal-profundo' : 'text-cinza'
                                }`}
                            >
                                {rotulo}
                            </span>
                            {id === 'notificacoes' && naoLidas > 0 && (
                                <span className="absolute top-1 right-[30%] h-2 w-2 rounded-full bg-coral" />
                            )}
                        </Link>
                    );
                })}

                {itensDoMais.length > 0 && (
                    <button
                        type="button"
                        onClick={() => setMaisAberto(true)}
                        aria-expanded={maisAberto}
                        className="relative flex h-full flex-1 flex-col items-center justify-center gap-1"
                    >
                        <MoreHorizontal
                            size={19}
                            strokeWidth={
                                itensDoMais.some((item) => item.id === secaoAtiva)
                                    ? 2.6
                                    : 2
                            }
                            className={
                                itensDoMais.some((item) => item.id === secaoAtiva)
                                    ? 'text-teal-profundo'
                                    : 'text-cinza'
                            }
                        />
                        <span className="text-[10px] font-medium text-cinza">
                            Mais
                        </span>
                    </button>
                )}
            </nav>

            {/* ---------- Painel "Mais" (celular) ---------- */}
            {maisAberto && (
                <div className="fixed inset-0 z-50 flex flex-col justify-end bg-marinho/40 md:hidden">
                    <button
                        type="button"
                        aria-label="Fechar"
                        className="flex-1"
                        onClick={() => setMaisAberto(false)}
                    />

                    <div className="rounded-t-3xl bg-creme p-5 pb-8">
                        <div className="mb-4 flex items-center justify-between">
                            <h2 className="font-display text-lg font-bold text-marinho">
                                Mais
                            </h2>
                            <button
                                type="button"
                                onClick={() => setMaisAberto(false)}
                                aria-label="Fechar"
                                className="rounded-full bg-teal-suave p-1.5"
                            >
                                <X size={16} className="text-teal-profundo" />
                            </button>
                        </div>

                        <div className="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            {itensDoMais.map(({ id, rotulo, rota, Icone, cor }) => {
                                const cores = classesDaCor(cor);

                                return (
                                    <Link
                                        key={id}
                                        href={route(rota)}
                                        className="flex w-full flex-col items-start gap-3 rounded-2xl bg-white p-4 text-left shadow-cartao"
                                    >
                                        <span
                                            className={`flex h-9 w-9 items-center justify-center rounded-full ${cores.fundo}`}
                                        >
                                            <Icone size={18} className={cores.texto} />
                                        </span>
                                        <span className="text-sm font-semibold text-marinho">
                                            {rotulo}
                                        </span>
                                    </Link>
                                );
                            })}
                        </div>

                        <button
                            type="button"
                            onClick={sair}
                            className="flex w-full items-center justify-center gap-2 rounded-2xl bg-coral-suave py-3 text-sm font-semibold text-coral"
                        >
                            <LogOut size={16} /> Sair da conta
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
