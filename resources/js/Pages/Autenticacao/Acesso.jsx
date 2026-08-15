import Botao from '@/Components/UI/Botao';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import SeloDeConfianca from '@/Components/UI/SeloDeConfianca';
import LayoutDeAcesso from '@/Layouts/LayoutDeAcesso';
import { Link, useForm } from '@inertiajs/react';

export default function Acesso({ status, podeRedefinirSenha }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const enviar = (evento) => {
        evento.preventDefault();

        post(route('login.entrar'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <LayoutDeAcesso titulo="Entrar">
            <form
                onSubmit={enviar}
                className="flex w-full max-w-[320px] flex-col gap-3"
            >
                {status && (
                    <p className="rounded-xl bg-teal-suave px-4 py-2 text-center text-xs font-medium text-teal-profundo">
                        {status}
                    </p>
                )}

                <CampoDeFormulario
                    id="email"
                    rotulo="E-mail"
                    type="email"
                    autoComplete="username"
                    placeholder="seu@email.com"
                    value={data.email}
                    erro={errors.email}
                    comFoco
                    obrigatorio
                    onChange={(e) => setData('email', e.target.value)}
                />

                <CampoDeFormulario
                    id="password"
                    rotulo="Senha"
                    type="password"
                    autoComplete="current-password"
                    placeholder="Sua senha"
                    value={data.password}
                    erro={errors.password}
                    obrigatorio
                    onChange={(e) => setData('password', e.target.value)}
                />

                <label className="flex items-center gap-2 text-xs text-cinza">
                    <input
                        type="checkbox"
                        name="remember"
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                        className="rounded border-teal-suave text-teal focus:ring-teal"
                    />
                    Continuar conectado
                </label>

                <Botao type="submit" disabled={processing}>
                    Entrar
                </Botao>

                <Link
                    href={route('cadastro.criar')}
                    className="flex w-full items-center justify-center rounded-2xl border-[1.5px] border-teal-profundo px-4 py-3 text-sm font-semibold text-teal-profundo transition hover:bg-teal-suave"
                >
                    Criar conta
                </Link>

                {podeRedefinirSenha && (
                    <Link
                        href={route('senha.solicitar')}
                        className="text-center text-xs font-medium text-teal-profundo underline"
                    >
                        Esqueci minha senha
                    </Link>
                )}
            </form>

            <SeloDeConfianca />
        </LayoutDeAcesso>
    );
}
