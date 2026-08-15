import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import Botao from '@/Components/UI/Botao';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import CampoDeSelecao from '@/Components/UI/CampoDeSelecao';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import Interruptor from '@/Components/UI/Interruptor';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { Link, router, useForm } from '@inertiajs/react';
import { Check, Plus, Trash2, X } from 'lucide-react';
import { useState } from 'react';

export default function Lembretes({ agenda, tipos }) {
    const [criando, setCriando] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        titulo: '',
        tipo: 'troca',
        horario: '08:00',
        ativo: true,
    });

    const criar = (evento) => {
        evento.preventDefault();

        // Sem preserveScroll: o aviso de confirmação fica no topo, e o
        // formulário no fim da lista.
        post(route('paciente.lembretes.store'), {
            onSuccess: () => {
                reset();
                setCriando(false);
            },
        });
    };

    const alternar = (lembrete) =>
        router.patch(
            route('paciente.lembretes.alternar', lembrete.id),
            {},
            { preserveScroll: true },
        );

    const concluir = (lembrete) =>
        router.post(
            route('paciente.lembretes.concluir', lembrete.id),
            {},
            { preserveScroll: true },
        );

    const remover = (lembrete) =>
        router.delete(route('paciente.lembretes.destroy', lembrete.id), {
            preserveScroll: true,
        });

    return (
        <LayoutDoAplicativo titulo="Lembretes" secaoAtiva="lembretes">
            <div className="flex flex-col gap-4">
                <AvisoDeStatus />

                {/* ---------- Calendário semanal ---------- */}
                <div className="rounded-2xl bg-white p-4 shadow-cartao">
                    <p className="mb-3 text-xs font-semibold text-cinza">
                        {agenda.mesDoDiaSelecionado}
                    </p>

                    <div className="flex justify-between gap-1">
                        {agenda.semana.map((dia) => {
                            const selecionado = dia.data === agenda.diaSelecionado;

                            // Dia futuro não tem cuidado registrado ainda.
                            if (dia.noFuturo) {
                                return (
                                    <span
                                        key={dia.data}
                                        aria-disabled="true"
                                        className="flex flex-1 flex-col items-center gap-1 rounded-xl py-2 opacity-40"
                                    >
                                        <span className="text-[10px] text-cinza">
                                            {dia.inicial}
                                        </span>
                                        <span className="text-sm font-semibold text-marinho">
                                            {dia.diaDoMes}
                                        </span>
                                    </span>
                                );
                            }

                            return (
                                <Link
                                    key={dia.data}
                                    href={route('paciente.lembretes.index', {
                                        dia: dia.data,
                                    })}
                                    preserveScroll
                                    aria-current={selecionado ? 'date' : undefined}
                                    aria-label={`Dia ${dia.diaDoMes}`}
                                    className={`flex flex-1 flex-col items-center gap-1 rounded-xl py-2 ${
                                        selecionado ? 'bg-teal-profundo' : ''
                                    }`}
                                >
                                    <span
                                        className={`text-[10px] ${
                                            selecionado
                                                ? 'text-teal-suave'
                                                : 'text-cinza'
                                        }`}
                                    >
                                        {dia.inicial}
                                    </span>
                                    <span
                                        className={`text-sm font-semibold ${
                                            selecionado
                                                ? 'text-white'
                                                : 'text-marinho'
                                        }`}
                                    >
                                        {dia.diaDoMes}
                                    </span>
                                </Link>
                            );
                        })}
                    </div>
                </div>

                {/* ---------- Lista do dia ---------- */}
                {agenda.lembretes.length === 0 && (
                    <p className="rounded-2xl bg-white p-6 text-center text-sm text-cinza shadow-cartao">
                        Você ainda não tem lembretes. Crie o primeiro abaixo.
                    </p>
                )}

                <div className="flex flex-col gap-2">
                    {agenda.lembretes.map((lembrete) => {
                        const cores = classesDaCor(lembrete.cor);

                        return (
                            <div
                                key={lembrete.id}
                                className="flex items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-cartao"
                            >
                                <div className="flex min-w-0 items-center gap-3">
                                    <span
                                        className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-full ${cores.fundo}`}
                                    >
                                        <IconeDinamico
                                            nome={lembrete.icone}
                                            size={16}
                                            className={cores.texto}
                                        />
                                    </span>

                                    <div className="min-w-0">
                                        <p
                                            className={`truncate text-sm font-medium text-marinho ${
                                                lembrete.concluido
                                                    ? 'line-through opacity-60'
                                                    : ''
                                            }`}
                                        >
                                            {lembrete.titulo}
                                        </p>
                                        <p className="text-xs text-cinza">
                                            {lembrete.tipoRotulo} ·{' '}
                                            {lembrete.horario}
                                            {lembrete.concluido && ' · feito'}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex shrink-0 items-center gap-2">
                                    {/* Só o dia de hoje aceita marcar como feito. */}
                                    {agenda.ehHoje && lembrete.ativo && (
                                        <button
                                            type="button"
                                            onClick={() => concluir(lembrete)}
                                            aria-label={`Marcar ${lembrete.titulo} como feito`}
                                            className={`rounded-lg p-2 ${
                                                lembrete.concluido
                                                    ? 'bg-teal text-white'
                                                    : 'bg-teal-suave text-teal-profundo'
                                            }`}
                                        >
                                            <Check size={14} />
                                        </button>
                                    )}

                                    <Interruptor
                                        ativo={lembrete.ativo}
                                        rotulo={`Ligar ou desligar ${lembrete.titulo}`}
                                        aoAlternar={() => alternar(lembrete)}
                                    />

                                    <button
                                        type="button"
                                        onClick={() => remover(lembrete)}
                                        aria-label={`Remover ${lembrete.titulo}`}
                                        className="rounded-lg bg-coral-suave p-2"
                                    >
                                        <Trash2 size={14} className="text-coral" />
                                    </button>
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* ---------- Novo lembrete ---------- */}
                {criando ? (
                    <form
                        onSubmit={criar}
                        className="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-cartao"
                    >
                        <div className="flex items-center justify-between">
                            <h2 className="font-display text-base font-bold text-marinho">
                                Novo lembrete
                            </h2>
                            <button
                                type="button"
                                onClick={() => setCriando(false)}
                                aria-label="Cancelar"
                                className="rounded-full bg-teal-suave p-1.5"
                            >
                                <X size={14} className="text-teal-profundo" />
                            </button>
                        </div>

                        <CampoDeFormulario
                            id="titulo"
                            rotulo="O que você precisa lembrar"
                            placeholder="Trocar bolsa"
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

                        <CampoDeFormulario
                            id="horario"
                            rotulo="Horário"
                            type="time"
                            value={data.horario}
                            erro={errors.horario}
                            obrigatorio
                            onChange={(e) => setData('horario', e.target.value)}
                        />

                        <Botao type="submit" disabled={processing}>
                            Criar lembrete
                        </Botao>
                    </form>
                ) : (
                    <Botao type="button" onClick={() => setCriando(true)}>
                        <Plus size={16} /> Novo lembrete
                    </Botao>
                )}
            </div>
        </LayoutDoAplicativo>
    );
}
