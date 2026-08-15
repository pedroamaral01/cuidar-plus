import MensagemDeErro from './MensagemDeErro';

export default function CampoDeTextoLongo({
    id,
    rotulo,
    erro,
    obrigatorio = false,
    dica,
    linhas = 5,
    className = '',
    ...props
}) {
    return (
        <div className={className}>
            <label htmlFor={id} className="text-xs font-semibold text-marinho">
                {rotulo}
                {obrigatorio && (
                    <span className="ml-0.5 text-coral" aria-hidden="true">
                        *
                    </span>
                )}
            </label>

            <textarea
                id={id}
                name={id}
                rows={linhas}
                aria-invalid={erro ? 'true' : undefined}
                aria-required={obrigatorio ? 'true' : undefined}
                className={`mt-1 w-full rounded-xl border px-4 py-3 text-sm text-marinho placeholder:text-cinza/70 focus:border-teal focus:ring-1 focus:ring-teal ${
                    erro ? 'border-coral' : 'border-teal-suave'
                }`}
                {...props}
            />

            {dica && !erro && <p className="mt-1 text-[11px] text-cinza">{dica}</p>}

            <MensagemDeErro mensagem={erro} className="mt-1" />
        </div>
    );
}
