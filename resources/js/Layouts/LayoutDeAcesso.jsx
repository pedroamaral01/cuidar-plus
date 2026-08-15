import LogoMarca from '@/Components/Marca/LogoMarca';
import { Head } from '@inertiajs/react';

/**
 * Casca das telas públicas (acesso, cadastro, recuperação de senha).
 * No desktop mostra o painel da marca à esquerda; no celular, só o logo.
 */
export default function LayoutDeAcesso({ titulo, children, painelLateral = true }) {
    return (
        <>
            <Head title={titulo} />

            <div className="flex min-h-screen items-center justify-center bg-creme px-6 py-10">
                <div className="flex w-full max-w-[880px] flex-col items-center gap-10 md:flex-row">
                    {painelLateral && (
                        <div className="hidden min-h-[420px] flex-1 flex-col items-center justify-center rounded-3xl bg-teal-profundo p-10 text-center md:flex">
                            <LogoMarca tamanho={72} />
                            <h2 className="mt-4 font-display text-2xl font-bold text-white">
                                Seu cuidado continua em casa.
                            </h2>
                            <p className="mt-3 max-w-xs text-sm text-teal-suave">
                                Orientações, lembretes e suporte para quem usa
                                dispositivos de saúde após a alta hospitalar —
                                no celular ou no computador.
                            </p>
                        </div>
                    )}

                    <div className="flex w-full flex-1 flex-col items-center gap-4">
                        <div className="mb-2 flex flex-col items-center gap-2 md:hidden">
                            <LogoMarca tamanho={56} />
                            <h1 className="font-display text-2xl font-bold text-teal-profundo">
                                Cuidar+
                            </h1>
                        </div>

                        {children}
                    </div>
                </div>
            </div>
        </>
    );
}
