import CartaoAcao from '@/Components/UI/CartaoAcao';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';
import { navegacaoDoAdministrador } from '@/Layouts/navegacao';

const CARTOES = [
    { chave: 'pacientesAtivos', rotulo: 'Pacientes ativos', cor: 'teal' },
    { chave: 'dispositivosCadastrados', rotulo: 'Dispositivos cadastrados', cor: 'lavender' },
    { chave: 'notificacoesHoje', rotulo: 'Notificações hoje', cor: 'amber' },
    { chave: 'conteudosPublicados', rotulo: 'Conteúdos publicados', cor: 'coral' },
];

export default function Painel({ estatisticas }) {
    const atalhos = navegacaoDoAdministrador.filter(
        (item) => item.id !== 'painel' && route().has(item.rota),
    );

    return (
        <LayoutDoAplicativo titulo="Painel" secaoAtiva="painel">
            <div className="flex flex-col gap-5">
                <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    {CARTOES.map(({ chave, rotulo, cor }) => (
                        <div
                            key={chave}
                            className="rounded-2xl bg-white p-4 shadow-cartao"
                        >
                            <p
                                className={`font-display text-2xl font-bold ${classesDaCor(cor).texto}`}
                            >
                                {estatisticas[chave]}
                            </p>
                            <p className="mt-1 text-xs text-cinza">{rotulo}</p>
                        </div>
                    ))}
                </div>

                {atalhos.length > 0 && (
                    <div>
                        <p className="mb-2 text-xs font-semibold text-cinza">
                            GERENCIAR
                        </p>
                        <div className="grid grid-cols-2 gap-3 md:grid-cols-3">
                            {atalhos.map((item) => (
                                <CartaoAcao
                                    key={item.id}
                                    rotulo={item.rotulo}
                                    href={route(item.rota)}
                                    Icone={item.Icone}
                                    cor={item.cor}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </LayoutDoAplicativo>
    );
}
