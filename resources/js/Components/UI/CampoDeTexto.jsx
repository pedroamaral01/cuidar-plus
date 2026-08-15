import { forwardRef, useEffect, useRef } from 'react';

const CampoDeTexto = forwardRef(function CampoDeTexto(
    { type = 'text', className = '', comFoco = false, erro, ...props },
    ref,
) {
    const referenciaLocal = useRef(null);
    const referencia = ref ?? referenciaLocal;

    useEffect(() => {
        if (comFoco) {
            referencia.current?.focus();
        }
    }, [comFoco, referencia]);

    return (
        <input
            {...props}
            type={type}
            ref={referencia}
            aria-invalid={erro ? 'true' : undefined}
            className={
                'w-full rounded-xl border px-4 py-3 text-sm text-marinho placeholder:text-cinza/70 ' +
                'focus:border-teal focus:ring-1 focus:ring-teal ' +
                `${erro ? 'border-coral' : 'border-teal-suave'} ${className}`
            }
        />
    );
});

export default CampoDeTexto;
