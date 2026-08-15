import CampoDeTexto from './CampoDeTexto';
import MensagemDeErro from './MensagemDeErro';

/**
 * Rótulo + campo + mensagem de erro, que é o trio que se repete em todos os
 * formulários do sistema.
 */
export default function CampoDeFormulario({
    id,
    rotulo,
    erro,
    obrigatorio = false,
    dica,
    className = '',
    ...props
}) {
    return (
        <div className={className}>
            <label
                htmlFor={id}
                className="text-xs font-semibold text-marinho"
            >
                {rotulo}
                {obrigatorio && (
                    <span className="ml-0.5 text-coral" aria-hidden="true">
                        *
                    </span>
                )}
            </label>

            <CampoDeTexto
                id={id}
                name={id}
                erro={erro}
                required={obrigatorio}
                className="mt-1"
                {...props}
            />

            {dica && !erro && (
                <p className="mt-1 text-[11px] text-cinza">{dica}</p>
            )}

            <MensagemDeErro mensagem={erro} className="mt-1" />
        </div>
    );
}
