/**
 * Paleta de marca do Cuidar+.
 *
 * As cores vivem em resources/css/app.css (@theme do Tailwind 4). Aqui ficam
 * apenas os mapeamentos de classe usados quando a cor vem do backend — os
 * Enums do domínio devolvem 'teal' | 'amber' | 'coral' | 'lavender', e o
 * componente precisa traduzir isso em classes.
 *
 * As classes são escritas por extenso de propósito: o Tailwind faz varredura
 * estática do código-fonte e não enxergaria algo como `bg-${cor}-suave`.
 */
export const fundoPorCor = {
    teal: 'bg-teal-suave',
    amber: 'bg-ambar-suave',
    coral: 'bg-coral-suave',
    lavender: 'bg-lavanda-suave',
};

export const textoPorCor = {
    teal: 'text-teal-profundo',
    amber: 'text-ambar',
    coral: 'text-coral',
    lavender: 'text-lavanda',
};

export const pontoPorCor = {
    teal: 'bg-teal',
    amber: 'bg-ambar',
    coral: 'bg-coral',
    lavender: 'bg-lavanda',
};

export function classesDaCor(cor = 'teal') {
    return {
        fundo: fundoPorCor[cor] ?? fundoPorCor.teal,
        texto: textoPorCor[cor] ?? textoPorCor.teal,
        ponto: pontoPorCor[cor] ?? pontoPorCor.teal,
    };
}
