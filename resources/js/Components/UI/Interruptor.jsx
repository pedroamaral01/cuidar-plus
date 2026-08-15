/**
 * Liga/desliga usado nos lembretes e nos formulários administrativos.
 */
export default function Interruptor({ ativo, aoAlternar, rotulo, disabled = false }) {
    return (
        <button
            type="button"
            role="switch"
            aria-checked={ativo}
            aria-label={rotulo}
            disabled={disabled}
            onClick={aoAlternar}
            className={`relative h-[22px] w-10 shrink-0 rounded-full transition ${
                ativo ? 'bg-teal' : 'bg-cinza/40'
            } ${disabled ? 'cursor-not-allowed opacity-60' : ''}`}
        >
            <span
                className="absolute top-0.5 h-[18px] w-[18px] rounded-full bg-white transition-[left]"
                style={{ left: ativo ? 20 : 2 }}
            />
        </button>
    );
}
