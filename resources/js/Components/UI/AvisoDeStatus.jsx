import { usePage } from '@inertiajs/react';
import { CheckCircle2, XCircle } from 'lucide-react';

/**
 * Mostra o retorno da última ação (flash de sessão). O erro é destacado em
 * coral porque costuma indicar uma operação que não aconteceu.
 */
export default function AvisoDeStatus() {
    const { flash } = usePage().props;

    const status = flash?.status;
    const erro = flash?.erro;

    if (!status && !erro) {
        return null;
    }

    if (erro) {
        return (
            <div
                role="alert"
                className="mb-4 flex items-start gap-2 rounded-2xl bg-coral-suave px-4 py-3 text-sm text-coral"
            >
                <XCircle size={16} className="mt-0.5 shrink-0" />
                {erro}
            </div>
        );
    }

    return (
        <div
            role="status"
            className="mb-4 flex items-start gap-2 rounded-2xl bg-teal-suave px-4 py-3 text-sm text-teal-profundo"
        >
            <CheckCircle2 size={16} className="mt-0.5 shrink-0" />
            {status}
        </div>
    );
}
