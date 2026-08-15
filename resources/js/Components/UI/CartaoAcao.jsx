import { classesDaCor } from '@/Components/UI/paleta';
import { Link } from '@inertiajs/react';

/**
 * Cartão de atalho usado no Início do paciente e no painel administrativo.
 */
export default function CartaoAcao({ rotulo, href, Icone, cor = 'teal', badge = 0 }) {
    const cores = classesDaCor(cor);

    return (
        <Link
            href={href}
            className="relative flex w-full flex-col items-start gap-3 rounded-2xl bg-white p-4 text-left shadow-cartao transition hover:shadow-md"
        >
            <span
                className={`flex h-10 w-10 items-center justify-center rounded-full ${cores.fundo}`}
            >
                <Icone size={18} className={cores.texto} />
            </span>

            <span className="text-sm font-semibold text-marinho">{rotulo}</span>

            {badge > 0 && (
                <span className="absolute top-3 right-3 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-coral px-1 text-[10px] font-bold text-white">
                    {badge}
                </span>
            )}
        </Link>
    );
}
