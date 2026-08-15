import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import Botao from '@/Components/UI/Botao';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { Link, router, useForm } from '@inertiajs/react';
import { ChevronRight, LogOut } from 'lucide-react';

export default function Perfil({ paciente, dispositivo }) {
    const { data, setData, put, processing, errors } = useForm({
        nome: paciente.nome,
        email: paciente.email,
        telefone: paciente.telefone ?? '',
        data_de_nascimento: paciente.data_de_nascimento ?? '',
        cuidador_nome: paciente.cuidador_nome ?? '',
        cuidador_telefone: paciente.cuidador_telefone ?? '',
    });

    const salvar = (evento) => {
        evento.preventDefault();
        put(route('paciente.perfil.atualizar'));
    };

    const sair = () => router.post(route('logout'));

    return (
        <LayoutDoAplicativo titulo="Meu perfil" secaoAtiva="perfil">
            <div className="flex flex-col gap-4">
                <AvisoDeStatus />

                <div className="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-cartao">
                    <span className="flex h-15 w-15 items-center justify-center rounded-full bg-teal font-display text-xl font-bold text-white">
                        {paciente.iniciais}
                    </span>
                    <div className="min-w-0">
                        <p className="truncate text-base font-semibold text-marinho">
                            {paciente.nome}
                        </p>
                        <p className="truncate text-xs text-cinza">
                            {paciente.email}
                            {paciente.telefone && ` · ${paciente.telefone}`}
                        </p>
                    </div>
                </div>

                <div className="rounded-2xl bg-white p-5 shadow-cartao">
                    <p className="mb-3 text-xs font-semibold text-cinza">
                        DISPOSITIVO ATUAL
                    </p>

                    {dispositivo ? (
                        <Link
                            href={route('paciente.dispositivo')}
                            className="flex items-center justify-between"
                        >
                            <span className="flex items-center gap-3">
                                <span
                                    className={`flex h-9 w-9 items-center justify-center rounded-full ${classesDaCor(dispositivo.cor).fundo}`}
                                >
                                    <IconeDinamico
                                        nome={dispositivo.icone}
                                        size={17}
                                        className={classesDaCor(dispositivo.cor).texto}
                                    />
                                </span>
                                <span className="text-sm font-medium text-marinho">
                                    {dispositivo.nome}
                                </span>
                            </span>
                            <ChevronRight size={16} className="text-cinza" />
                        </Link>
                    ) : (
                        <Link
                            href={route('paciente.dispositivo')}
                            className="text-sm font-semibold text-teal-profundo"
                        >
                            Escolher meu dispositivo
                        </Link>
                    )}
                </div>

                <form
                    onSubmit={salvar}
                    className="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-cartao"
                >
                    <p className="text-xs font-semibold text-cinza">MEUS DADOS</p>

                    <CampoDeFormulario
                        id="nome"
                        rotulo="Nome completo"
                        value={data.nome}
                        erro={errors.nome}
                        obrigatorio
                        onChange={(e) => setData('nome', e.target.value)}
                    />

                    <CampoDeFormulario
                        id="email"
                        rotulo="E-mail"
                        type="email"
                        autoComplete="username"
                        value={data.email}
                        erro={errors.email}
                        obrigatorio
                        onChange={(e) => setData('email', e.target.value)}
                    />

                    <CampoDeFormulario
                        id="telefone"
                        rotulo="Telefone"
                        placeholder="(00) 00000-0000"
                        value={data.telefone}
                        erro={errors.telefone}
                        onChange={(e) => setData('telefone', e.target.value)}
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

                    <p className="mt-2 text-xs font-semibold text-cinza">
                        CUIDADOR / FAMILIAR
                    </p>

                    <CampoDeFormulario
                        id="cuidador_nome"
                        rotulo="Nome"
                        placeholder="Quem te ajuda no acompanhamento"
                        value={data.cuidador_nome}
                        erro={errors.cuidador_nome}
                        onChange={(e) => setData('cuidador_nome', e.target.value)}
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

                    <Botao type="submit" disabled={processing}>
                        Salvar alterações
                    </Botao>
                </form>

                <Botao type="button" variante="perigo" onClick={sair}>
                    <LogOut size={16} /> Sair da conta
                </Botao>
            </div>
        </LayoutDoAplicativo>
    );
}
