import FormularioAdmin from '@/Components/Administracao/FormularioAdmin';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import CampoDeSelecao from '@/Components/UI/CampoDeSelecao';
import CampoDeTextoLongo from '@/Components/UI/CampoDeTextoLongo';
import Interruptor from '@/Components/UI/Interruptor';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { useForm } from '@inertiajs/react';

const TIPOS = [
    { valor: 'texto', rotulo: 'Texto' },
    { valor: 'video', rotulo: 'Vídeo' },
];

export default function Formulario({ conteudo, dispositivos }) {
    const editando = conteudo !== null;

    const { data, setData, post, put, processing, errors } = useForm({
        titulo: conteudo?.titulo ?? '',
        resumo: conteudo?.resumo ?? '',
        tipo: conteudo?.tipo ?? 'texto',
        corpo: conteudo?.corpo ?? '',
        url_do_video: conteudo?.url_do_video ?? '',
        dispositivo_id: conteudo?.dispositivo_id ?? '',
        publicado: conteudo?.publicado ?? true,
    });

    const enviar = (evento) => {
        evento.preventDefault();

        if (editando) {
            put(route('administracao.conteudos.update', conteudo.id));
        } else {
            post(route('administracao.conteudos.store'));
        }
    };

    return (
        <LayoutDoAplicativo
            titulo={editando ? 'Editar conteúdo' : 'Novo conteúdo'}
            secaoAtiva="conteudos"
        >
            <FormularioAdmin
                titulo={editando ? 'Editar conteúdo' : 'Novo conteúdo'}
                subtitulo="Sem dispositivo, o conteúdo aparece para todos os pacientes."
                rotaDeVolta={route('administracao.conteudos.index')}
                aoEnviar={enviar}
                processando={processing}
            >
                <CampoDeFormulario
                    id="titulo"
                    rotulo="Título"
                    placeholder="Como identificar sinais de infecção"
                    value={data.titulo}
                    erro={errors.titulo}
                    comFoco
                    obrigatorio
                    onChange={(e) => setData('titulo', e.target.value)}
                />

                <CampoDeFormulario
                    id="resumo"
                    rotulo="Resumo"
                    placeholder="Uma linha explicando do que se trata"
                    value={data.resumo}
                    erro={errors.resumo}
                    onChange={(e) => setData('resumo', e.target.value)}
                />

                <CampoDeSelecao
                    id="tipo"
                    rotulo="Tipo"
                    opcoes={TIPOS}
                    value={data.tipo}
                    erro={errors.tipo}
                    obrigatorio
                    onChange={(e) => setData('tipo', e.target.value)}
                />

                {/* Vídeo pede a URL; texto pede o corpo. */}
                {data.tipo === 'video' ? (
                    <CampoDeFormulario
                        id="url_do_video"
                        rotulo="Endereço do vídeo"
                        type="url"
                        placeholder="https://..."
                        value={data.url_do_video}
                        erro={errors.url_do_video}
                        obrigatorio
                        onChange={(e) => setData('url_do_video', e.target.value)}
                    />
                ) : (
                    <CampoDeTextoLongo
                        id="corpo"
                        rotulo="Conteúdo"
                        linhas={10}
                        placeholder="Escreva o conteúdo que o paciente vai ler."
                        value={data.corpo}
                        erro={errors.corpo}
                        obrigatorio
                        onChange={(e) => setData('corpo', e.target.value)}
                    />
                )}

                <CampoDeSelecao
                    id="dispositivo_id"
                    rotulo="Dispositivo"
                    placeholder="Todos os dispositivos"
                    opcoes={dispositivos}
                    value={data.dispositivo_id ?? ''}
                    erro={errors.dispositivo_id}
                    onChange={(e) => setData('dispositivo_id', e.target.value)}
                />

                <div className="flex items-center justify-between rounded-2xl bg-creme p-4">
                    <span>
                        <span className="block text-sm font-semibold text-marinho">
                            Publicado
                        </span>
                        <span className="block text-xs text-cinza">
                            Conteúdos publicados aparecem na tela do paciente.
                        </span>
                    </span>
                    <Interruptor
                        ativo={data.publicado}
                        rotulo="Publicado"
                        aoAlternar={() => setData('publicado', !data.publicado)}
                    />
                </div>
            </FormularioAdmin>
        </LayoutDoAplicativo>
    );
}
