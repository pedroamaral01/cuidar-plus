import FormularioAdmin from '@/Components/Administracao/FormularioAdmin';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import CampoDeSelecao from '@/Components/UI/CampoDeSelecao';
import CampoDeTextoLongo from '@/Components/UI/CampoDeTextoLongo';
import Interruptor from '@/Components/UI/Interruptor';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { useForm } from '@inertiajs/react';

const GRAVIDADES = [
    { valor: 'alta', rotulo: 'Alta — procurar atendimento' },
    { valor: 'media', rotulo: 'Média — observar e informar' },
    { valor: 'baixa', rotulo: 'Baixa — acompanhar' },
];

export default function Formulario({ sinal, dispositivos }) {
    const editando = sinal !== null;

    const { data, setData, post, put, processing, errors } = useForm({
        nome: sinal?.nome ?? '',
        orientacao: sinal?.orientacao ?? '',
        gravidade: sinal?.gravidade ?? 'media',
        dispositivo_id: sinal?.dispositivo_id ?? '',
        publicado: sinal?.publicado ?? true,
    });

    const enviar = (evento) => {
        evento.preventDefault();

        if (editando) {
            put(route('administracao.alertas.update', sinal.id));
        } else {
            post(route('administracao.alertas.store'));
        }
    };

    return (
        <LayoutDoAplicativo
            titulo={editando ? 'Editar sinal de alerta' : 'Novo sinal de alerta'}
            secaoAtiva="alertas"
        >
            <FormularioAdmin
                titulo={editando ? 'Editar sinal de alerta' : 'Novo sinal de alerta'}
                subtitulo="O paciente vê o nome na lista e a orientação ao expandir."
                rotaDeVolta={route('administracao.alertas.index')}
                aoEnviar={enviar}
                processando={processing}
            >
                <CampoDeFormulario
                    id="nome"
                    rotulo="Nome do sinal"
                    placeholder="Sangramento"
                    value={data.nome}
                    erro={errors.nome}
                    comFoco
                    obrigatorio
                    onChange={(e) => setData('nome', e.target.value)}
                />

                <CampoDeTextoLongo
                    id="orientacao"
                    rotulo="O que o paciente deve fazer"
                    placeholder="Se o sangramento for intenso ou não parar, procure atendimento imediatamente."
                    dica="Escreva em linguagem simples e direta."
                    value={data.orientacao}
                    erro={errors.orientacao}
                    obrigatorio
                    onChange={(e) => setData('orientacao', e.target.value)}
                />

                <CampoDeSelecao
                    id="gravidade"
                    rotulo="Gravidade"
                    opcoes={GRAVIDADES}
                    value={data.gravidade}
                    erro={errors.gravidade}
                    obrigatorio
                    onChange={(e) => setData('gravidade', e.target.value)}
                />

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
                            Sinais publicados aparecem para os pacientes.
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
