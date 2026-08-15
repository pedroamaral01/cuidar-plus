import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { useForm } from '@inertiajs/react';
import { CheckCircle2, ChevronRight } from 'lucide-react';

export default function Dispositivo({ dispositivos, dispositivoAtualId }) {
    const { data, setData, put, processing } = useForm({
        dispositivo_id: dispositivoAtualId ?? '',
        aplicar_plano_de_cuidados: true,
    });

    const escolher = (id) => {
        if (id === dispositivoAtualId) {
            return;
        }

        setData('dispositivo_id', id);
        put(route('paciente.dispositivo.definir'), {
            data: { dispositivo_id: id, aplicar_plano_de_cuidados: true },
            preserveScroll: true,
        });
    };

    return (
        <LayoutDoAplicativo titulo="Meu dispositivo" secaoAtiva="dispositivo">
            <AvisoDeStatus />

            <div className="flex flex-col gap-3">
                <p className="text-xs text-cinza">
                    Selecione o dispositivo que você utiliza:
                </p>

                <div
                    className="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                    role="radiogroup"
                    aria-label="Dispositivo utilizado"
                >
                    {dispositivos.map((dispositivo) => {
                        const cores = classesDaCor(dispositivo.cor);
                        const ativo = dispositivo.id === dispositivoAtualId;

                        return (
                            <button
                                key={dispositivo.id}
                                type="button"
                                role="radio"
                                aria-checked={ativo}
                                disabled={processing}
                                onClick={() => escolher(dispositivo.id)}
                                className={`flex items-center justify-between rounded-2xl border-[1.5px] bg-white p-4 text-left shadow-cartao transition ${
                                    ativo ? 'border-teal' : 'border-transparent'
                                } ${processing ? 'opacity-60' : ''}`}
                            >
                                <span className="flex items-center gap-3">
                                    <span
                                        className={`flex h-9 w-9 items-center justify-center rounded-full ${cores.fundo}`}
                                    >
                                        <IconeDinamico
                                            nome={dispositivo.icone}
                                            size={17}
                                            className={cores.texto}
                                        />
                                    </span>
                                    <span>
                                        <span className="block text-sm font-medium text-marinho">
                                            {dispositivo.nome}
                                        </span>
                                        {dispositivo.descricao && (
                                            <span className="mt-0.5 block text-[11px] leading-snug text-cinza">
                                                {dispositivo.descricao}
                                            </span>
                                        )}
                                    </span>
                                </span>

                                {ativo ? (
                                    <CheckCircle2
                                        size={18}
                                        className="shrink-0 text-teal"
                                    />
                                ) : (
                                    <ChevronRight
                                        size={16}
                                        className="shrink-0 text-cinza"
                                    />
                                )}
                            </button>
                        );
                    })}
                </div>

                <p className="mt-2 rounded-2xl bg-teal-suave p-3 text-xs leading-relaxed text-teal-profundo">
                    As orientações, os sinais de alerta e os lembretes são
                    personalizados para o dispositivo selecionado. Ao trocar, os
                    cuidados sugeridos do novo dispositivo são adicionados à sua
                    rotina — os lembretes que você já tinha continuam lá.
                </p>
            </div>
        </LayoutDoAplicativo>
    );
}
