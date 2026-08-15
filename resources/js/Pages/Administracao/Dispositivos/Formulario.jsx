import FormularioAdmin from '@/Components/Administracao/FormularioAdmin';
import CampoDeFormulario from '@/Components/UI/CampoDeFormulario';
import CampoDeSelecao from '@/Components/UI/CampoDeSelecao';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import Interruptor from '@/Components/UI/Interruptor';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { useForm } from '@inertiajs/react';

// Os mesmos nomes reconhecidos pelo IconeDinamico.
const ICONES = [
    'Droplets',
    'Waves',
    'Circle',
    'RefreshCw',
    'ShieldCheck',
    'Pill',
    'MoreHorizontal',
];

const CORES = [
    { valor: 'teal', rotulo: 'Verde-azulado' },
    { valor: 'amber', rotulo: 'Âmbar' },
    { valor: 'coral', rotulo: 'Coral' },
    { valor: 'lavender', rotulo: 'Lavanda' },
];

export default function Formulario({ dispositivo }) {
    const editando = dispositivo !== null;

    const { data, setData, post, put, processing, errors } = useForm({
        nome: dispositivo?.nome ?? '',
        descricao: dispositivo?.descricao ?? '',
        icone: dispositivo?.icone ?? 'Droplets',
        cor: dispositivo?.cor ?? 'teal',
        ativo: dispositivo?.ativo ?? true,
    });

    const enviar = (evento) => {
        evento.preventDefault();

        if (editando) {
            put(route('administracao.dispositivos.update', dispositivo.id));
        } else {
            post(route('administracao.dispositivos.store'));
        }
    };

    return (
        <LayoutDoAplicativo
            titulo={editando ? 'Editar dispositivo' : 'Novo dispositivo'}
            secaoAtiva="dispositivos"
        >
            <FormularioAdmin
                titulo={editando ? 'Editar dispositivo' : 'Novo dispositivo'}
                subtitulo="O dispositivo define quais orientações, alertas e conteúdos o paciente enxerga."
                rotaDeVolta={route('administracao.dispositivos.index')}
                aoEnviar={enviar}
                processando={processing}
            >
                <CampoDeFormulario
                    id="nome"
                    rotulo="Nome"
                    placeholder="Colostomia"
                    value={data.nome}
                    erro={errors.nome}
                    comFoco
                    obrigatorio
                    onChange={(e) => setData('nome', e.target.value)}
                />

                <CampoDeFormulario
                    id="descricao"
                    rotulo="Descrição"
                    placeholder="Explicação curta, exibida ao paciente"
                    value={data.descricao}
                    erro={errors.descricao}
                    onChange={(e) => setData('descricao', e.target.value)}
                />

                <div>
                    <span className="text-xs font-semibold text-marinho">
                        Ícone
                        <span className="ml-0.5 text-coral" aria-hidden="true">
                            *
                        </span>
                    </span>

                    <div
                        role="radiogroup"
                        aria-label="Ícone do dispositivo"
                        className="mt-2 flex flex-wrap gap-2"
                    >
                        {ICONES.map((icone) => {
                            const selecionado = data.icone === icone;
                            const cores = classesDaCor(data.cor);

                            return (
                                <button
                                    key={icone}
                                    type="button"
                                    role="radio"
                                    aria-checked={selecionado}
                                    aria-label={icone}
                                    onClick={() => setData('icone', icone)}
                                    className={`flex h-11 w-11 items-center justify-center rounded-xl border-[1.5px] ${cores.fundo} ${
                                        selecionado
                                            ? 'border-teal'
                                            : 'border-transparent'
                                    }`}
                                >
                                    <IconeDinamico
                                        nome={icone}
                                        size={18}
                                        className={cores.texto}
                                    />
                                </button>
                            );
                        })}
                    </div>
                </div>

                <CampoDeSelecao
                    id="cor"
                    rotulo="Cor"
                    opcoes={CORES}
                    value={data.cor}
                    erro={errors.cor}
                    obrigatorio
                    onChange={(e) => setData('cor', e.target.value)}
                />

                <div className="flex items-center justify-between rounded-2xl bg-creme p-4">
                    <span>
                        <span className="block text-sm font-semibold text-marinho">
                            Dispositivo ativo
                        </span>
                        <span className="block text-xs text-cinza">
                            Só dispositivos ativos aparecem no cadastro do
                            paciente.
                        </span>
                    </span>
                    <Interruptor
                        ativo={data.ativo}
                        rotulo="Dispositivo ativo"
                        aoAlternar={() => setData('ativo', !data.ativo)}
                    />
                </div>
            </FormularioAdmin>
        </LayoutDoAplicativo>
    );
}
