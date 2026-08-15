import FormularioAdmin from '@/Components/Administracao/FormularioAdmin';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import CampoDeSelecao from '@/Components/UI/CampoDeSelecao';
import CampoDeTexto from '@/Components/UI/CampoDeTexto';
import Interruptor from '@/Components/UI/Interruptor';
import MensagemDeErro from '@/Components/UI/MensagemDeErro';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { useForm } from '@inertiajs/react';
import { ArrowDown, ArrowUp, Plus, Trash2 } from 'lucide-react';

const ABA_VAZIA = { nome: '', passos: [''] };

/** Move um item de posição dentro do array, sem mutar o original. */
function mover(lista, de, para) {
    if (para < 0 || para >= lista.length) {
        return lista;
    }

    const copia = [...lista];
    [copia[de], copia[para]] = [copia[para], copia[de]];

    return copia;
}

export default function Formulario({ orientacao, dispositivos }) {
    const editando = orientacao !== null;

    const { data, setData, post, put, processing, errors } = useForm({
        dispositivo_id: orientacao?.dispositivo_id ?? '',
        titulo: orientacao?.titulo ?? '',
        subtitulo: orientacao?.subtitulo ?? '',
        publicada: orientacao?.publicada ?? true,
        abas: orientacao?.abas?.length ? orientacao.abas : [{ ...ABA_VAZIA }],
    });

    const atualizarAba = (indice, alteracoes) => {
        setData(
            'abas',
            data.abas.map((aba, i) => (i === indice ? { ...aba, ...alteracoes } : aba)),
        );
    };

    const adicionarAba = () =>
        setData('abas', [...data.abas, { nome: '', passos: [''] }]);

    const removerAba = (indice) =>
        setData(
            'abas',
            data.abas.filter((_, i) => i !== indice),
        );

    const atualizarPasso = (indiceDaAba, indiceDoPasso, texto) => {
        const passos = data.abas[indiceDaAba].passos.map((passo, i) =>
            i === indiceDoPasso ? texto : passo,
        );
        atualizarAba(indiceDaAba, { passos });
    };

    const adicionarPasso = (indiceDaAba) =>
        atualizarAba(indiceDaAba, { passos: [...data.abas[indiceDaAba].passos, ''] });

    const removerPasso = (indiceDaAba, indiceDoPasso) =>
        atualizarAba(indiceDaAba, {
            passos: data.abas[indiceDaAba].passos.filter((_, i) => i !== indiceDoPasso),
        });

    const moverPasso = (indiceDaAba, de, para) =>
        atualizarAba(indiceDaAba, {
            passos: mover(data.abas[indiceDaAba].passos, de, para),
        });

    const enviar = (evento) => {
        evento.preventDefault();

        if (editando) {
            put(route('administracao.orientacoes.update', orientacao.id));
        } else {
            post(route('administracao.orientacoes.store'));
        }
    };

    return (
        <LayoutDoAplicativo
            titulo={editando ? 'Editar orientação' : 'Nova orientação'}
            secaoAtiva="orientacoes"
        >
            <FormularioAdmin
                titulo={editando ? 'Editar orientação' : 'Nova orientação'}
                subtitulo="Cada tipo de cuidado vira uma aba na tela do paciente, com o passo a passo numerado."
                rotaDeVolta={route('administracao.orientacoes.index')}
                aoEnviar={enviar}
                processando={processing}
            >
                <CampoDeSelecao
                    id="dispositivo_id"
                    rotulo="Dispositivo"
                    placeholder="Selecione o dispositivo"
                    opcoes={dispositivos}
                    value={data.dispositivo_id ?? ''}
                    erro={errors.dispositivo_id}
                    obrigatorio
                    onChange={(e) => setData('dispositivo_id', e.target.value)}
                />

                <CampoDeFormulario
                    id="titulo"
                    rotulo="Título"
                    placeholder="Colostomia"
                    value={data.titulo}
                    erro={errors.titulo}
                    obrigatorio
                    onChange={(e) => setData('titulo', e.target.value)}
                />

                <CampoDeFormulario
                    id="subtitulo"
                    rotulo="Subtítulo"
                    placeholder="Aprenda como realizar os cuidados corretamente."
                    value={data.subtitulo}
                    erro={errors.subtitulo}
                    onChange={(e) => setData('subtitulo', e.target.value)}
                />

                {/* ---------- Abas e passos ---------- */}
                <div className="mt-2">
                    <div className="mb-2 flex items-center justify-between">
                        <span className="text-xs font-semibold text-marinho">
                            Tipos de cuidado
                            <span className="ml-0.5 text-coral" aria-hidden="true">
                                *
                            </span>
                        </span>
                        <button
                            type="button"
                            onClick={adicionarAba}
                            className="flex items-center gap-1 rounded-lg bg-teal-suave px-3 py-1.5 text-xs font-semibold text-teal-profundo"
                        >
                            <Plus size={13} /> Adicionar tipo de cuidado
                        </button>
                    </div>

                    <MensagemDeErro mensagem={errors.abas} className="mb-2" />

                    <div className="flex flex-col gap-4">
                        {data.abas.map((aba, indiceDaAba) => (
                            <fieldset
                                key={indiceDaAba}
                                className="rounded-2xl border border-teal-suave p-4"
                            >
                                <legend className="px-1 text-xs font-semibold text-cinza">
                                    Aba {indiceDaAba + 1}
                                </legend>

                                <div className="mb-3 flex items-end gap-2">
                                    <div className="flex-1">
                                        <label
                                            htmlFor={`aba-${indiceDaAba}-nome`}
                                            className="text-xs font-semibold text-marinho"
                                        >
                                            Nome do tipo de cuidado
                                        </label>
                                        <CampoDeTexto
                                            id={`aba-${indiceDaAba}-nome`}
                                            className="mt-1"
                                            placeholder="Troca da bolsa"
                                            value={aba.nome}
                                            erro={errors[`abas.${indiceDaAba}.nome`]}
                                            onChange={(e) =>
                                                atualizarAba(indiceDaAba, {
                                                    nome: e.target.value,
                                                })
                                            }
                                        />
                                    </div>

                                    {data.abas.length > 1 && (
                                        <button
                                            type="button"
                                            onClick={() => removerAba(indiceDaAba)}
                                            aria-label={`Remover aba ${indiceDaAba + 1}`}
                                            className="mb-1 rounded-lg bg-coral-suave p-2.5"
                                        >
                                            <Trash2 size={14} className="text-coral" />
                                        </button>
                                    )}
                                </div>

                                <MensagemDeErro
                                    mensagem={errors[`abas.${indiceDaAba}.nome`]}
                                    className="mb-2"
                                />

                                <p className="mb-2 text-xs font-semibold text-cinza">
                                    PASSO A PASSO
                                </p>

                                <div className="flex flex-col gap-2">
                                    {aba.passos.map((passo, indiceDoPasso) => (
                                        <div
                                            key={indiceDoPasso}
                                            className="flex items-center gap-2"
                                        >
                                            <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-suave text-xs font-bold text-teal-profundo">
                                                {indiceDoPasso + 1}
                                            </span>

                                            <CampoDeTexto
                                                aria-label={`Passo ${indiceDoPasso + 1} da aba ${indiceDaAba + 1}`}
                                                placeholder="Descreva o passo"
                                                value={passo}
                                                erro={
                                                    errors[
                                                        `abas.${indiceDaAba}.passos.${indiceDoPasso}`
                                                    ]
                                                }
                                                onChange={(e) =>
                                                    atualizarPasso(
                                                        indiceDaAba,
                                                        indiceDoPasso,
                                                        e.target.value,
                                                    )
                                                }
                                            />

                                            <button
                                                type="button"
                                                onClick={() =>
                                                    moverPasso(
                                                        indiceDaAba,
                                                        indiceDoPasso,
                                                        indiceDoPasso - 1,
                                                    )
                                                }
                                                disabled={indiceDoPasso === 0}
                                                aria-label={`Mover passo ${indiceDoPasso + 1} para cima`}
                                                className="rounded-lg bg-teal-suave p-2 disabled:opacity-40"
                                            >
                                                <ArrowUp
                                                    size={13}
                                                    className="text-teal-profundo"
                                                />
                                            </button>

                                            <button
                                                type="button"
                                                onClick={() =>
                                                    moverPasso(
                                                        indiceDaAba,
                                                        indiceDoPasso,
                                                        indiceDoPasso + 1,
                                                    )
                                                }
                                                disabled={
                                                    indiceDoPasso ===
                                                    aba.passos.length - 1
                                                }
                                                aria-label={`Mover passo ${indiceDoPasso + 1} para baixo`}
                                                className="rounded-lg bg-teal-suave p-2 disabled:opacity-40"
                                            >
                                                <ArrowDown
                                                    size={13}
                                                    className="text-teal-profundo"
                                                />
                                            </button>

                                            {aba.passos.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removerPasso(
                                                            indiceDaAba,
                                                            indiceDoPasso,
                                                        )
                                                    }
                                                    aria-label={`Remover passo ${indiceDoPasso + 1}`}
                                                    className="rounded-lg bg-coral-suave p-2"
                                                >
                                                    <Trash2
                                                        size={13}
                                                        className="text-coral"
                                                    />
                                                </button>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                <MensagemDeErro
                                    mensagem={errors[`abas.${indiceDaAba}.passos`]}
                                    className="mt-2"
                                />

                                <button
                                    type="button"
                                    onClick={() => adicionarPasso(indiceDaAba)}
                                    className="mt-3 flex items-center gap-1 text-xs font-semibold text-teal-profundo"
                                >
                                    <Plus size={13} /> Adicionar passo
                                </button>
                            </fieldset>
                        ))}
                    </div>
                </div>

                <div className="flex items-center justify-between rounded-2xl bg-creme p-4">
                    <span>
                        <span className="block text-sm font-semibold text-marinho">
                            Publicada
                        </span>
                        <span className="block text-xs text-cinza">
                            Orientações publicadas aparecem para o paciente.
                        </span>
                    </span>
                    <Interruptor
                        ativo={data.publicada}
                        rotulo="Publicada"
                        aoAlternar={() => setData('publicada', !data.publicada)}
                    />
                </div>
            </FormularioAdmin>
        </LayoutDoAplicativo>
    );
}
