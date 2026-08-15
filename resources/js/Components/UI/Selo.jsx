import { classesDaCor } from './paleta';

/** Etiqueta curta de estado (Ativo/Inativo, Publicado, gravidade...). */
export default function Selo({ children, cor = 'teal' }) {
    const cores = classesDaCor(cor);

    return (
        <span
            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${cores.fundo} ${cores.texto}`}
        >
            {children}
        </span>
    );
}
