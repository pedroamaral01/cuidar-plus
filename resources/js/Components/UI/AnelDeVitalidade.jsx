/**
 * Anel de progresso do resumo semanal. O valor também é exposto em texto,
 * para quem usa leitor de tela.
 */
export default function AnelDeVitalidade({ percentual, tamanho = 104 }) {
    const raio = (tamanho / 2) * 0.8;
    const centro = tamanho / 2;
    const circunferencia = 2 * Math.PI * raio;
    const deslocamento = circunferencia - (percentual / 100) * circunferencia;

    return (
        <div
            className="anel-vitalidade relative flex items-center justify-center"
            style={{ width: tamanho, height: tamanho }}
            role="img"
            aria-label={`${percentual}% dos cuidados da semana realizados`}
        >
            <svg width={tamanho} height={tamanho} viewBox={`0 0 ${tamanho} ${tamanho}`}>
                <circle
                    cx={centro}
                    cy={centro}
                    r={raio}
                    fill="none"
                    stroke="#DCEEEA"
                    strokeWidth="9"
                />
                <circle
                    cx={centro}
                    cy={centro}
                    r={raio}
                    fill="none"
                    stroke="#2F9C8F"
                    strokeWidth="9"
                    strokeLinecap="round"
                    strokeDasharray={circunferencia}
                    strokeDashoffset={deslocamento}
                    transform={`rotate(-90 ${centro} ${centro})`}
                />
            </svg>

            <span
                aria-hidden="true"
                className="absolute font-display text-lg font-bold text-teal-profundo"
            >
                {percentual}%
            </span>
        </div>
    );
}
