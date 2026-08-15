import Botao from '@/Components/UI/Botao';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import LayoutDeAcesso from '@/Layouts/LayoutDeAcesso';
import { useForm } from '@inertiajs/react';

export default function RedefinirSenha({ token, email }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    const enviar = (evento) => {
        evento.preventDefault();

        post(route('senha.atualizar'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <LayoutDeAcesso titulo="Criar nova senha">
            <form
                onSubmit={enviar}
                className="flex w-full max-w-[320px] flex-col gap-3"
            >
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
                    id="password"
                    rotulo="Nova senha"
                    type="password"
                    autoComplete="new-password"
                    dica="Pelo menos 8 caracteres."
                    value={data.password}
                    erro={errors.password}
                    comFoco
                    obrigatorio
                    onChange={(e) => setData('password', e.target.value)}
                />

                <CampoDeFormulario
                    id="password_confirmation"
                    rotulo="Confirme a nova senha"
                    type="password"
                    autoComplete="new-password"
                    value={data.password_confirmation}
                    erro={errors.password_confirmation}
                    obrigatorio
                    onChange={(e) =>
                        setData('password_confirmation', e.target.value)
                    }
                />

                <Botao type="submit" disabled={processing}>
                    Salvar nova senha
                </Botao>
            </form>
        </LayoutDeAcesso>
    );
}
