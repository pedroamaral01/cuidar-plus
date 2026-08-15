import AnelDeVitalidade from '@/Components/UI/AnelDeVitalidade';
import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import Botao from '@/Components/UI/Botao';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import CampoDeSelecao from '@/Components/UI/CampoDeSelecao';
import CampoDeTextoLongo from '@/Components/UI/CampoDeTextoLongo';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { useForm } from '@inertiajs/react';
import { Plus, X } from 'lucide-react';
import { useState } from 'react';

export default function Diario({ primeiroNome, resumoDaSemana, historico, tipos }) {
    const [registrando, setRegistrando] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        titulo: '',
        tipo: 'troca',
        observacao: '',
    });

    const registrar = (evento) => {
        evento.preventDefault();

        // Sem preserveScroll de propósito: o aviso de confirmação fica no topo
        // da tela, e o formulário fica no fim. Mantendo a rolagem, o paciente
        // não veria a confirmação do que acabou de fazer.
        post(route('paciente.diario.registrar'), {
            onSuccess: () => {
                reset();
                setRegistrando(false);
            },
        });
    };

    const indicadores = [
        ['Trocas realizadas', resumoDaSemana.trocas],
        ['Esvaziamentos', resumoDaSemana.esvaziamentos],
        ['Registros no diário', resumoDaSemana.registros],
    ];

    return (
        <LayoutDoAplicativo titulo="Diário" secaoAtiva="diario">
            <div className="flex flex-col gap-4">
                <AvisoDeStatus />

                <section className="flex flex-col items-center justify-between gap-4 rounded-3xl bg-teal-profundo p-6 sm:flex-row">
                    <div>
                        <p className="font-display text-sm font-semibold text-white">
                            Muito bem, {primeiroNome}!
                        </p>
                        <p className="text-xs text-teal-suave">
                            Você está cuidando da sua saúde.
                        </p>
                    </div>

                    <AnelDeVitalidade percentual={resumoDaSemana.percentual} />
                </section>

                <div className="grid grid-cols-3 gap-3">
                    {indicadores.map(([rotulo, valor]) => (
                        <div
                            key={rotulo}
                            className="rounded-2xl bg-white p-4 text-center shadow-cartao"
                        >
                            <p className="font-display text-xl font-bold text-teal-profundo">
                                {valor}
                            </p>
                            <p className="mt-1 text-[11px] text-cinza">{rotulo}</p>
                        </div>
                    ))}
                </div>

                {registrando ? (
                    <form
                        onSubmit={registrar}
                        className="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-cartao"
                    >
                        <div className="flex items-center justify-between">
                            <h2 className="font-display text-base font-bold text-marinho">
                                Registrar cuidado
                            </h2>
                            <button
                                type="button"
                                onClick={() => setRegistrando(false)}
                                aria-label="Cancelar"
                                className="rounded-full bg-teal-suave p-1.5"
                            >
                                <X size={14} className="text-teal-profundo" />
                            </button>
                        </div>

                        <CampoDeFormulario
                            id="titulo"
                            rotulo="O que você fez"
                            placeholder="Troca de bolsa realizada"
                            value={data.titulo}
                            erro={errors.titulo}
                            comFoco
                            obrigatorio
                            onChange={(e) => setData('titulo', e.target.value)}
                        />

                        <CampoDeSelecao
                            id="tipo"
                            rotulo="Tipo de cuidado"
                            opcoes={tipos}
                            value={data.tipo}
                            erro={errors.tipo}
                            obrigatorio
                            onChange={(e) => setData('tipo', e.target.value)}
                        />

                        <CampoDeTextoLongo
                            id="observacao"
                            rotulo="Observação"
                            linhas={3}
                            placeholder="Algo que você queira registrar sobre este cuidado."
                            value={data.observacao}
                            erro={errors.observacao}
                            onChange={(e) => setData('observacao', e.target.value)}
                        />

                        <Botao type="submit" disabled={processing}>
                            Salvar no diário
                        </Botao>
                    </form>
                ) : (
                    <Botao type="button" onClick={() => setRegistrando(true)}>
                        <Plus size={16} /> Registrar cuidado
                    </Botao>
                )}

                <div>
                    <p className="mb-2 text-xs font-semibold text-cinza">
                        HISTÓRICO RECENTE
                    </p>

                    {historico.length === 0 && (
                        <p className="rounded-2xl bg-white p-6 text-center text-sm text-cinza shadow-cartao">
                            Nenhum cuidado registrado ainda.
                        </p>
                    )}

                    <div className="flex flex-col gap-2">
                        {historico.map((registro) => (
                            <div
                                key={registro.id}
                                className="flex items-start gap-3 rounded-2xl bg-white p-4 shadow-cartao"
                            >
                                <span
                                    className={`mt-1.5 h-2 w-2 shrink-0 rounded-full ${classesDaCor(registro.cor).ponto}`}
                                />
                                <div>
                                    <p className="text-sm font-medium text-marinho">
                                        {registro.titulo}
                                    </p>
                                    <p className="text-xs text-cinza">
                                        {registro.quando}
                                    </p>
                                    {registro.observacao && (
                                        <p className="mt-1 text-xs leading-relaxed text-cinza">
                                            {registro.observacao}
                                        </p>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </LayoutDoAplicativo>
    );
}
