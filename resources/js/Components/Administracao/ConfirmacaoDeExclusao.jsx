import { AlertTriangle } from 'lucide-react';

/**
 * Exclusão é irreversível e apaga conteúdo que o paciente enxerga, então
 * sempre passa por confirmação explícita.
 */
export default function ConfirmacaoDeExclusao({
    descricao,
    aoConfirmar,
    aoCancelar,
}) {
    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-marinho/40 p-6"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-da-exclusao"
        >
            <div className="w-full max-w-sm rounded-3xl bg-white p-6 shadow-cartao">
                <div className="mb-3 flex items-center gap-3">
                    <span className="flex h-10 w-10 items-center justify-center rounded-full bg-coral-suave">
                        <AlertTriangle size={18} className="text-coral" />
                    </span>
                    <h2
                        id="titulo-da-exclusao"
                        className="font-display text-lg font-bold text-marinho"
                    >
                        Confirmar exclusão
                    </h2>
                </div>

                <p className="mb-6 text-sm text-cinza">
                    Tem certeza que deseja excluir <strong>{descricao}</strong>?
                    Essa ação não pode ser desfeita.
                </p>

                <div className="flex gap-3">
                    <button
                        type="button"
                        onClick={aoCancelar}
                        className="flex-1 rounded-2xl border-[1.5px] border-teal-profundo py-2.5 text-sm font-semibold text-teal-profundo"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        onClick={aoConfirmar}
                        className="flex-1 rounded-2xl bg-coral py-2.5 text-sm font-semibold text-white"
                    >
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    );
}
