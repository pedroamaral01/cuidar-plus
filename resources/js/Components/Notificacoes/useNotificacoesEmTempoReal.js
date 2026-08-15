import { router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

/**
 * Escuta o canal privado do usuário logado e reage a cada notificação nova.
 *
 * O canal é `App.Models.Usuario.{id}`, o mesmo nome que o trait Notifiable
 * monta no backend. A autorização acontece no servidor, em routes/channels.php,
 * usando a sessão — por isso um paciente não consegue escutar o canal de
 * outro nem trocando o id aqui.
 *
 * Ao receber um aviso, o hook recarrega apenas as props que mudaram
 * (`naoLidas` e, quando o paciente está na central, `notificacoes`), em vez de
 * recarregar a página inteira.
 */
export default function useNotificacoesEmTempoReal(propsParaRecarregar = ['naoLidas']) {
    const { auth } = usePage().props;
    const usuarioId = auth?.usuario?.id;

    useEffect(() => {
        if (!usuarioId || !window.Echo) {
            return undefined;
        }

        const canal = window.Echo.private(`App.Models.Usuario.${usuarioId}`);

        canal.notification(() => {
            router.reload({ only: propsParaRecarregar });
        });

        return () => {
            window.Echo.leave(`App.Models.Usuario.${usuarioId}`);
        };
        // propsParaRecarregar é um array literal nas chamadas; serializar
        // evita recriar a inscrição a cada render.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [usuarioId, JSON.stringify(propsParaRecarregar)]);
}
