import MensagemDeErro from './MensagemDeErro';

/**
 * Select padronizado. `opcoes` = [{ valor, rotulo }]
 */
export default function CampoDeSelecao({
    id,
    rotulo,
    opcoes,
    erro,
    obrigatorio = false,
    placeholder,
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

            <select
                id={id}
                name={id}
                aria-invalid={erro ? 'true' : undefined}
                aria-required={obrigatorio ? 'true' : undefined}
                className={`mt-1 w-full rounded-xl border px-4 py-3 text-sm text-marinho focus:border-teal focus:ring-1 focus:ring-teal ${
                    erro ? 'border-coral' : 'border-teal-suave'
                }`}
                {...props}
            >
                {placeholder && <option value="">{placeholder}</option>}
                {opcoes.map((opcao) => (
                    <option key={opcao.valor} value={opcao.valor}>
                        {opcao.rotulo}
                    </option>
                ))}
            </select>

            <MensagemDeErro mensagem={erro} className="mt-1" />
        </div>
    );
}
