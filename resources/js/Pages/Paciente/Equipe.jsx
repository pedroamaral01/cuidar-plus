import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { Link } from '@inertiajs/react';
import { AlertTriangle, MessageCircle } from 'lucide-react';

/**
 * Ponto de entrada de "Falar com a equipe".
 *
 * O canal real de contato ainda não foi definido pelo time (chat próprio,
 * e-mail, ticket ou WhatsApp Business). Esta tela existe para manter o ponto
 * de entrada visível — sem prometer ao paciente um envio que ainda não existe.
 */
export default function Equipe() {
    return (
        <LayoutDoAplicativo titulo="Falar com a equipe" secaoAtiva="equipe">
            <div className="flex flex-col gap-4">
                <div className="flex flex-col items-center gap-4 rounded-3xl bg-white p-8 text-center shadow-cartao">
                    <span className="flex h-16 w-16 items-center justify-center rounded-full bg-lavanda-suave">
                        <MessageCircle size={28} className="text-lavanda" />
                    </span>

                    <div>
                        <h2 className="font-display text-xl font-bold text-marinho">
                            Em breve
                        </h2>
                        <p className="mt-2 max-w-md text-sm leading-relaxed text-cinza">
                            O canal de contato direto com a equipe de saúde
                            ainda está sendo preparado. Assim que estiver
                            disponível, você poderá tirar dúvidas por aqui.
                        </p>
                    </div>
                </div>

                <div className="flex items-start gap-3 rounded-2xl bg-coral-suave p-4">
                    <AlertTriangle
                        size={18}
                        className="mt-0.5 shrink-0 text-coral"
                    />
                    <p className="text-xs leading-relaxed text-teal-profundo">
                        <strong>Enquanto isso:</strong> em caso de urgência ou
                        sinal de alerta, procure atendimento profissional
                        imediatamente ou entre em contato pelos canais que a
                        equipe passou na sua alta. Não aguarde resposta por esta
                        tela.
                    </p>
                </div>

                <div className="rounded-2xl bg-white p-5 shadow-cartao">
                    <p className="mb-3 text-xs font-semibold text-cinza">
                        ENQUANTO ISSO, PODE AJUDAR
                    </p>

                    <div className="flex flex-col gap-2">
                        <Link
                            href={route('paciente.alertas')}
                            className="rounded-xl bg-creme px-4 py-3 text-sm font-medium text-marinho"
                        >
                            Ver os sinais de alerta e o que fazer
                        </Link>
                        <Link
                            href={route('paciente.orientacoes.index')}
                            className="rounded-xl bg-creme px-4 py-3 text-sm font-medium text-marinho"
                        >
                            Rever o passo a passo dos cuidados
                        </Link>
                    </div>
                </div>
            </div>
        </LayoutDoAplicativo>
    );
}
