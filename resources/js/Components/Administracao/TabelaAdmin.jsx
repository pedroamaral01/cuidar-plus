import { Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

import ConfirmacaoDeExclusao from './ConfirmacaoDeExclusao';

/**
 * Listagem em tabela usada por todas as telas administrativas.
 *
 * `colunas` = [{ chave, rotulo, formatar? }]
 * `linhas`  = registros, cada um com `id`
 */
export default function TabelaAdmin({
    titulo,
    subtitulo,
    colunas,
    linhas,
    rotaDeCriacao,
    rotuloDoBotao = 'Novo',
    rotaDeEdicao,
    rotaDeExclusao,
    descreverLinha = (linha) => linha.nome ?? linha.titulo ?? `#${linha.id}`,
    vazio = 'Nada cadastrado ainda.',
}) {
    const [emExclusao, setEmExclusao] = useState(null);

    const excluir = () => {
        router.delete(rotaDeExclusao(emExclusao), {
            preserveScroll: true,
            onFinish: () => setEmExclusao(null),
        });
    };

    return (
        <div>
            <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 className="font-display text-xl font-bold text-marinho">
                        {titulo}
                    </h2>
                    {subtitulo && (
                        <p className="text-sm text-cinza">{subtitulo}</p>
                    )}
                </div>

                {rotaDeCriacao && (
                    <Link
                        href={rotaDeCriacao}
                        className="flex items-center gap-2 rounded-xl bg-teal-profundo px-4 py-2 text-sm font-semibold text-white"
                    >
                        <Plus size={16} /> {rotuloDoBotao}
                    </Link>
                )}
            </div>

            <div className="overflow-x-auto rounded-2xl bg-white shadow-cartao">
                <table className="w-full min-w-[480px] text-sm">
                    <thead>
                        <tr className="bg-teal-suave">
                            {colunas.map((coluna) => (
                                <th
                                    key={coluna.chave}
                                    scope="col"
                                    className="px-4 py-3 text-left font-semibold whitespace-nowrap text-teal-profundo"
                                >
                                    {coluna.rotulo}
                                </th>
                            ))}
                            {(rotaDeEdicao || rotaDeExclusao) && (
                                <th scope="col" className="px-4 py-3">
                                    <span className="sr-only">Ações</span>
                                </th>
                            )}
                        </tr>
                    </thead>

                    <tbody>
                        {linhas.length === 0 && (
                            <tr>
                                <td
                                    colSpan={colunas.length + 1}
                                    className="px-4 py-8 text-center text-sm text-cinza"
                                >
                                    {vazio}
                                </td>
                            </tr>
                        )}

                        {linhas.map((linha) => (
                            <tr
                                key={linha.id}
                                className="border-t border-teal-suave"
                            >
                                {colunas.map((coluna) => (
                                    <td
                                        key={coluna.chave}
                                        className="px-4 py-3 text-marinho"
                                    >
                                        {coluna.formatar
                                            ? coluna.formatar(linha)
                                            : linha[coluna.chave]}
                                    </td>
                                ))}

                                {(rotaDeEdicao || rotaDeExclusao) && (
                                    <td className="px-4 py-3">
                                        <div className="flex items-center justify-end gap-2">
                                            {rotaDeEdicao && (
                                                <Link
                                                    href={rotaDeEdicao(linha)}
                                                    aria-label={`Editar ${descreverLinha(linha)}`}
                                                    className="rounded-lg bg-teal-suave p-1.5"
                                                >
                                                    <Pencil
                                                        size={14}
                                                        className="text-teal-profundo"
                                                    />
                                                </Link>
                                            )}
                                            {rotaDeExclusao && (
                                                <button
                                                    type="button"
                                                    onClick={() => setEmExclusao(linha)}
                                                    aria-label={`Excluir ${descreverLinha(linha)}`}
                                                    className="rounded-lg bg-coral-suave p-1.5"
                                                >
                                                    <Trash2
                                                        size={14}
                                                        className="text-coral"
                                                    />
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                )}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {emExclusao && (
                <ConfirmacaoDeExclusao
                    descricao={descreverLinha(emExclusao)}
                    aoConfirmar={excluir}
                    aoCancelar={() => setEmExclusao(null)}
                />
            )}
        </div>
    );
}
