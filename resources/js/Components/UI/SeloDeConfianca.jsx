import { HeartHandshake, Lock, ShieldCheck, Users2 } from 'lucide-react';

const selos = [
    { Icone: Lock, rotulo: 'Seguro' },
    { Icone: ShieldCheck, rotulo: 'Confidencial' },
    { Icone: HeartHandshake, rotulo: 'Acolhedor' },
    { Icone: Users2, rotulo: 'Conectado' },
];

/**
 * Selos da tela de acesso + o aviso de que o sistema não substitui
 * atendimento profissional (exigência da seção 11 da especificação).
 */
export default function SeloDeConfianca() {
    return (
        <>
            <div className="flex items-center justify-center gap-4 pt-2">
                {selos.map(({ Icone, rotulo }) => (
                    <div key={rotulo} className="flex flex-col items-center gap-1">
                        <Icone size={16} className="text-teal" />
                        <span className="text-[10px] text-cinza">{rotulo}</span>
                    </div>
                ))}
            </div>

            <p className="max-w-[300px] text-center text-[11px] leading-snug text-cinza">
                Este aplicativo não substitui o atendimento profissional. Em caso
                de dúvidas ou sinais de alerta, procure a equipe de saúde.
            </p>
        </>
    );
}
