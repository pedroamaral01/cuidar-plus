import Botao from '@/Components/UI/Botao';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import LayoutDeAcesso from '@/Layouts/LayoutDeAcesso';
import { Link, useForm } from '@inertiajs/react';

export default function SolicitarNovaSenha({ status }) {
    const { data, setData, post, processing, errors } = useForm({ email: '' });

    const enviar = (evento) => {
        evento.preventDefault();
        post(route('senha.enviar-link'));
    };

    return (
        <LayoutDeAcesso titulo="Esqueci minha senha">
            <form
                onSubmit={enviar}
                className="flex w-full max-w-[320px] flex-col gap-3"
            >
                <p className="text-sm text-cinza">
                    Informe seu e-mail e enviaremos um link para você criar uma
                    nova senha.
                </p>

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

                <Botao type="submit" disabled={processing}>
                    Enviar link
                </Botao>

                <Link
                    href={route('login')}
                    className="text-center text-xs font-medium text-teal-profundo underline"
                >
                    Voltar para o acesso
                </Link>
            </form>
        </LayoutDeAcesso>
    );
}
