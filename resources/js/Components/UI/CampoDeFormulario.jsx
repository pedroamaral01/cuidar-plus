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

            {/*
              Usamos aria-required, e não o `required` nativo: com o `required`
              o navegador bloqueia o envio e mostra uma bolha no idioma dele,
              escondendo as mensagens em português validadas no servidor. O
              servidor é a fonte única de verdade da validação; o aria-required
              mantém a informação para leitores de tela.
            */}
            <CampoDeTexto
                id={id}
                name={id}
                erro={erro}
                aria-required={obrigatorio ? 'true' : undefined}
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
