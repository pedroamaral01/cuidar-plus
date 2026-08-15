export default function MensagemDeErro({ mensagem, className = '' }) {
    if (!mensagem) {
        return null;
    }

    return (
        <p className={`text-xs font-medium text-coral ${className}`} role="alert">
            {mensagem}
        </p>
    );
}
