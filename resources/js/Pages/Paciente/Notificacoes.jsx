import useNotificacoesEmTempoReal from '@/Components/Notificacoes/useNotificacoesEmTempoReal';
import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { router } from '@inertiajs/react';

export default function Notificacoes({ notificacoes }) {
    // Estando nesta tela, uma notificação nova precisa entrar na lista na hora.
    useNotificacoesEmTempoReal(['notificacoes', 'naoLidas']);

    const temNaoLidas = notificacoes.some((notificacao) => !notificacao.lida);

    const abrir = (notificacao) => {
        router.patch(
            route('paciente.notificacoes.lida', notificacao.id),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    if (notificacao.url) {
                        router.visit(notificacao.url);
                    }
                },
            },
        );
    };

    const marcarTodas = () =>
        router.patch(
            route('paciente.notificacoes.todas-lidas'),
            {},
            { preserveScroll: true },
        );

    return (
        <LayoutDoAplicativo titulo="Notificações" secaoAtiva="notificacoes">
            <div className="flex flex-col gap-2">
                <AvisoDeStatus />

                {temNaoLidas && (
                    <button
                        type="button"
                        onClick={marcarTodas}
                        className="mb-2 self-end text-xs font-semibold text-teal-profundo underline"
                    >
                        Marcar todas como lidas
                    </button>
                )}

                {notificacoes.length === 0 && (
                    <p className="rounded-2xl bg-white p-6 text-center text-sm text-cinza shadow-cartao">
                        Você ainda não tem notificações. Avisos sobre lembretes e
                        novos sinais de alerta aparecem aqui.
                    </p>
                )}

                {notificacoes.map((notificacao) => {
                    const cores = classesDaCor(notificacao.cor);

                    return (
                        <button
                            key={notificacao.id}
                            type="button"
                            onClick={() => abrir(notificacao)}
                            aria-label={`${notificacao.titulo}${notificacao.lida ? '' : ', não lida'}`}
                            className={`relative flex items-start gap-3 rounded-2xl bg-white p-4 text-left shadow-cartao ${
                                notificacao.lida ? 'opacity-70' : ''
                            }`}
                        >
                            <span
                                className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-full ${cores.fundo}`}
                            >
                                <IconeDinamico
                                    nome={notificacao.icone}
                                    size={16}
                                    className={cores.texto}
                                />
                            </span>

                            <span className="flex-1">
                                <span className="block text-sm font-semibold text-marinho">
                                    {notificacao.titulo}
                                </span>
                                <span className="mt-0.5 block text-xs text-cinza">
                                    {notificacao.descricao}
                                </span>
                                <span className="mt-1 block text-[11px] text-cinza">
                                    {notificacao.quando}
                                </span>
                            </span>

                            {!notificacao.lida && (
                                <span
                                    className="mt-1 h-2 w-2 shrink-0 rounded-full bg-coral"
                                    aria-hidden="true"
                                />
                            )}
                        </button>
                    );
                })}

                <p className="mt-2 text-center text-[11px] text-cinza">
                    As notificações chegam em tempo real assim que a equipe
                    publica algo novo.
                </p>
            </div>
        </LayoutDoAplicativo>
    );
}
