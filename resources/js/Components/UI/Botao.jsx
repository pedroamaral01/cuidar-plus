const variantes = {
    primario: 'bg-teal-profundo text-white hover:bg-teal-profundo/90',
    secundario:
        'border-[1.5px] border-teal-profundo text-teal-profundo hover:bg-teal-suave',
    perigo: 'bg-coral-suave text-coral hover:bg-coral-suave/70',
};

export default function Botao({
    variante = 'primario',
    className = '',
    disabled,
    children,
    ...props
}) {
    return (
        <button
            {...props}
            disabled={disabled}
            className={
                'flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold transition ' +
                'focus:ring-2 focus:ring-teal focus:ring-offset-2 focus:outline-none ' +
                `${variantes[variante]} ${disabled ? 'cursor-not-allowed opacity-60' : ''} ${className}`
            }
        >
            {children}
        </button>
    );
}
