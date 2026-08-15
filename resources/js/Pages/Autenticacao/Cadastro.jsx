import { Check, CheckCircle2, ChevronLeft, Plus } from 'lucide-react';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

import Botao from '@/Components/UI/Botao';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import MensagemDeErro from '@/Components/UI/MensagemDeErro';
import { classesDaCor } from '@/Components/UI/paleta';

const PASSOS = ['Dados pessoais', 'Dispositivo', 'Plano de cuidados'];

// Campos validados em cada passo. Assim o paciente descobre o erro no passo em
// que ele aconteceu, e não só ao concluir o cadastro inteiro.
const CAMPOS_POR_PASSO = {
    1: ['nome', 'email', 'password', 'password_confirmation', 'data_de_nascimento', 'cpf', 'telefone', 'cuidador_nome', 'cuidador_telefone'],
    2: ['dispositivo_id'],
    3: [],
};

export default function Cadastro({ dispositivos, planosPorDispositivo }) {
    const [passo, setPasso] = useState(1);
    const [mostrarCuidador, setMostrarCuidador] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        nome: '',
        email: '',
        password: '',
        password_confirmation: '',
        data_de_nascimento: '',
        cpf: '',
        telefone: '',
        cuidador_nome: '',
        cuidador_telefone: '',
        dispositivo_id: '',
        aplicar_plano_de_cuidados: true,
    });

    const dispositivoEscolhido = dispositivos.find(
        (d) => String(d.id) === String(data.dispositivo_id),
    );
    const planoDoDispositivo = planosPorDispositivo[data.dispositivo_id] ?? [];

    const errosDoPasso = CAMPOS_POR_PASSO[passo].filter((campo) => errors[campo]);

    const avancar = () => {
        if (passo < 3) {
            setPasso(passo + 1);
            return;
        }

        post(route('cadastro.salvar'), {
            onError: (errosRecebidos) => {
                // Leva o paciente de volta ao passo onde está o problema.
                const primeiroPassoComErro = [1, 2, 3].find((numero) =>
                    CAMPOS_POR_PASSO[numero].some((campo) => errosRecebidos[campo]),
                );

                if (primeiroPassoComErro) {
                    setPasso(primeiroPassoComErro);
                }
            },
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    const voltar = () => setPasso(passo - 1);

    const podeAvancar =
        passo !== 2 || data.dispositivo_id !== '';

    return (
        <>
            <Head title="Criar conta" />

            <div className="flex min-h-screen items-center justify-center bg-creme px-6 py-10">
                <div className="w-full max-w-[560px] rounded-3xl bg-white p-6 shadow-cartao md:p-10">
                    <div className="mb-6 flex items-center gap-2">
                        {passo === 1 ? (
                            <Link
                                href={route('login')}
                                aria-label="Voltar para o acesso"
                                className="flex h-8 w-8 items-center justify-center rounded-full bg-teal-suave"
                            >
                                <ChevronLeft size={18} className="text-teal-profundo" />
                            </Link>
                        ) : (
                            <button
                                type="button"
                                onClick={voltar}
                                aria-label="Voltar para o passo anterior"
                                className="flex h-8 w-8 items-center justify-center rounded-full bg-teal-suave"
                            >
                                <ChevronLeft size={18} className="text-teal-profundo" />
                            </button>
                        )}
                        <h1 className="font-display text-lg font-semibold text-marinho">
                            Cadastro do paciente
                        </h1>
                    </div>

                    <ol className="mb-8 flex max-w-[360px] items-center justify-between">
                        {PASSOS.map((rotulo, indice) => {
                            const numero = indice + 1;
                            const ativo = passo >= numero;

                            return (
                                <li
                                    key={rotulo}
                                    className="flex flex-1 items-center"
                                    aria-current={passo === numero ? 'step' : undefined}
                                >
                                    <div className="flex flex-col items-center gap-1">
                                        <span
                                            className={`flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold ${
                                                ativo
                                                    ? 'bg-teal text-white'
                                                    : 'bg-teal-suave text-cinza'
                                            }`}
                                        >
                                            {numero}
                                        </span>
                                        <span
                                            className={`text-[10px] ${ativo ? 'text-teal-profundo' : 'text-cinza'}`}
                                        >
                                            {rotulo}
                                        </span>
                                    </div>
                                    {numero < PASSOS.length && (
                                        <div className="mx-1 h-px flex-1 bg-teal-suave" />
                                    )}
                                </li>
                            );
                        })}
                    </ol>

                    {passo === 1 && (
                        <div className="flex flex-col gap-4">
                            <p className="text-sm text-cinza">
                                Preencha seus dados para que possamos
                                personalizar seu cuidado.
                            </p>

                            <CampoDeFormulario
                                id="nome"
                                rotulo="Nome completo"
                                placeholder="Como você se chama"
                                autoComplete="name"
                                value={data.nome}
                                erro={errors.nome}
                                comFoco
                                obrigatorio
                                onChange={(e) => setData('nome', e.target.value)}
                            />

                            <CampoDeFormulario
                                id="email"
                                rotulo="E-mail"
                                type="email"
                                placeholder="seu@email.com"
                                autoComplete="username"
                                value={data.email}
                                erro={errors.email}
                                obrigatorio
                                onChange={(e) => setData('email', e.target.value)}
                            />

                            <CampoDeFormulario
                                id="password"
                                rotulo="Senha"
                                type="password"
                                autoComplete="new-password"
                                dica="Pelo menos 8 caracteres."
                                value={data.password}
                                erro={errors.password}
                                obrigatorio
                                onChange={(e) => setData('password', e.target.value)}
                            />

                            <CampoDeFormulario
                                id="password_confirmation"
                                rotulo="Confirme a senha"
                                type="password"
                                autoComplete="new-password"
                                value={data.password_confirmation}
                                erro={errors.password_confirmation}
                                obrigatorio
                                onChange={(e) =>
                                    setData('password_confirmation', e.target.value)
                                }
                            />

                            <CampoDeFormulario
                                id="data_de_nascimento"
                                rotulo="Data de nascimento"
                                type="date"
                                value={data.data_de_nascimento}
                                erro={errors.data_de_nascimento}
                                onChange={(e) =>
                                    setData('data_de_nascimento', e.target.value)
                                }
                            />

                            <CampoDeFormulario
                                id="telefone"
                                rotulo="Telefone"
                                placeholder="(00) 00000-0000"
                                autoComplete="tel"
                                value={data.telefone}
                                erro={errors.telefone}
                                onChange={(e) => setData('telefone', e.target.value)}
                            />

                            {mostrarCuidador ? (
                                <div className="flex flex-col gap-3 rounded-2xl bg-teal-suave p-4">
                                    <p className="text-sm font-semibold text-teal-profundo">
                                        Cuidador ou familiar
                                    </p>
                                    <CampoDeFormulario
                                        id="cuidador_nome"
                                        rotulo="Nome do cuidador"
                                        value={data.cuidador_nome}
                                        erro={errors.cuidador_nome}
                                        onChange={(e) =>
                                            setData('cuidador_nome', e.target.value)
                                        }
                                    />
                                    <CampoDeFormulario
                                        id="cuidador_telefone"
                                        rotulo="Telefone do cuidador"
                                        placeholder="(00) 00000-0000"
                                        value={data.cuidador_telefone}
                                        erro={errors.cuidador_telefone}
                                        onChange={(e) =>
                                            setData('cuidador_telefone', e.target.value)
                                        }
                                    />
                                </div>
                            ) : (
                                <button
                                    type="button"
                                    onClick={() => setMostrarCuidador(true)}
                                    className="flex items-center justify-between rounded-2xl bg-teal-suave p-4 text-left"
                                >
                                    <span>
                                        <span className="block text-sm font-semibold text-teal-profundo">
                                            Tem um cuidador ou familiar?
                                        </span>
                                        <span className="block text-xs text-teal-profundo">
                                            Essa pessoa poderá te ajudar no
                                            acompanhamento.
                                        </span>
                                    </span>
                                    <Plus size={18} className="text-teal-profundo" />
                                </button>
                            )}
                        </div>
                    )}

                    {passo === 2 && (
                        <div className="flex flex-col gap-4">
                            <p className="text-sm text-cinza">
                                Selecione o dispositivo que você utiliza:
                            </p>

                            <div
                                className="grid grid-cols-1 gap-2 sm:grid-cols-2"
                                role="radiogroup"
                                aria-label="Dispositivo utilizado"
                            >
                                {dispositivos.map((dispositivo) => {
                                    const cores = classesDaCor(dispositivo.cor);
                                    const ativo =
                                        String(data.dispositivo_id) ===
                                        String(dispositivo.id);

                                    return (
                                        <button
                                            key={dispositivo.id}
                                            type="button"
                                            role="radio"
                                            aria-checked={ativo}
                                            onClick={() =>
                                                setData(
                                                    'dispositivo_id',
                                                    String(dispositivo.id),
                                                )
                                            }
                                            className={`flex items-center gap-3 rounded-2xl border-[1.5px] bg-creme p-3 text-left ${
                                                ativo
                                                    ? 'border-teal'
                                                    : 'border-transparent'
                                            }`}
                                        >
                                            <span
                                                className={`flex h-8 w-8 items-center justify-center rounded-full ${cores.fundo}`}
                                            >
                                                <IconeDinamico
                                                    nome={dispositivo.icone}
                                                    size={15}
                                                    className={cores.texto}
                                                />
                                            </span>
                                            <span className="text-sm font-medium text-marinho">
                                                {dispositivo.nome}
                                            </span>
                                            {ativo && (
                                                <CheckCircle2
                                                    size={16}
                                                    className="ml-auto text-teal"
                                                />
                                            )}
                                        </button>
                                    );
                                })}
                            </div>

                            <MensagemDeErro mensagem={errors.dispositivo_id} />
                        </div>
                    )}

                    {passo === 3 && (
                        <div className="flex flex-col gap-4">
                            <p className="text-sm text-cinza">
                                Revise o plano de cuidados sugerido para o seu
                                dispositivo:
                            </p>

                            <div className="rounded-2xl bg-teal-suave p-4">
                                <p className="mb-2 text-sm font-semibold text-teal-profundo">
                                    {dispositivoEscolhido?.nome ??
                                        'Nenhum dispositivo selecionado'}
                                </p>

                                {planoDoDispositivo.length > 0 ? (
                                    <ul className="flex flex-col gap-1.5">
                                        {planoDoDispositivo.map((item) => (
                                            <li
                                                key={`${item.titulo}-${item.horario}`}
                                                className="flex items-center gap-2 text-xs text-teal-profundo"
                                            >
                                                <Check size={13} /> {item.titulo} ·{' '}
                                                {item.horario}
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    <p className="text-xs text-teal-profundo">
                                        Este dispositivo ainda não tem um plano
                                        sugerido. Você poderá criar seus
                                        lembretes depois.
                                    </p>
                                )}
                            </div>

                            {planoDoDispositivo.length > 0 && (
                                <label className="flex items-start gap-2 text-xs text-cinza">
                                    <input
                                        type="checkbox"
                                        checked={data.aplicar_plano_de_cuidados}
                                        onChange={(e) =>
                                            setData(
                                                'aplicar_plano_de_cuidados',
                                                e.target.checked,
                                            )
                                        }
                                        className="mt-0.5 rounded border-teal-suave text-teal focus:ring-teal"
                                    />
                                    Criar esses lembretes na minha rotina. Você
                                    pode ajustar ou desligar cada um depois.
                                </label>
                            )}
                        </div>
                    )}

                    {errosDoPasso.length === 0 && errors.dispositivo_id && passo !== 2 && (
                        <MensagemDeErro
                            className="mt-4"
                            mensagem={errors.dispositivo_id}
                        />
                    )}

                    <Botao
                        type="button"
                        onClick={avancar}
                        disabled={processing || !podeAvancar}
                        className="mt-6"
                    >
                        {passo < 3 ? 'Continuar' : 'Concluir cadastro'}
                    </Botao>
                </div>
            </div>
        </>
    );
}
