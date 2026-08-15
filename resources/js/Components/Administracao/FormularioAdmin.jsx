import Botao from '@/Components/UI/Botao';
import { Link } from '@inertiajs/react';
import { ChevronLeft } from 'lucide-react';

/**
 * Casca dos formulários administrativos: título, voltar, corpo e ações.
 */
export default function FormularioAdmin({
    titulo,
    subtitulo,
    rotaDeVolta,
    aoEnviar,
    processando,
    rotuloDeEnvio = 'Salvar',
    children,
}) {
    return (
        <div>
            <div className="mb-6 flex items-center gap-2">
                <Link
                    href={rotaDeVolta}
                    aria-label="Voltar para a listagem"
                    className="flex h-8 w-8 items-center justify-center rounded-full bg-teal-suave"
                >
                    <ChevronLeft size={18} className="text-teal-profundo" />
                </Link>
                <div>
                    <h2 className="font-display text-xl font-bold text-marinho">
                        {titulo}
                    </h2>
                    {subtitulo && (
                        <p className="text-sm text-cinza">{subtitulo}</p>
                    )}
                </div>
            </div>

            <form
                onSubmit={aoEnviar}
                className="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-cartao"
            >
                {children}

                <div className="mt-2 flex flex-col gap-3 sm:flex-row-reverse">
                    <Botao
                        type="submit"
                        disabled={processando}
                        className="sm:w-auto sm:px-8"
                    >
                        {rotuloDeEnvio}
                    </Botao>

                    <Link
                        href={rotaDeVolta}
                        className="flex items-center justify-center rounded-2xl border-[1.5px] border-teal-profundo px-8 py-3 text-sm font-semibold text-teal-profundo"
                    >
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    );
}
