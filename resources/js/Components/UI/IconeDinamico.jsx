import {
    AlertTriangle,
    Circle,
    Clock,
    Droplets,
    FileText,
    MessageCircle,
    MoreHorizontal,
    Pill,
    PlayCircle,
    RefreshCw,
    ShieldCheck,
    Waves,
} from 'lucide-react';

/**
 * Traduz o nome de ícone que vem do backend (coluna `icone` de dispositivos e
 * os Enums do domínio) no componente correspondente.
 *
 * O registro é explícito de propósito: `import * as Icones from 'lucide-react'`
 * funciona, mas arrasta a biblioteca inteira para o bundle (~500 kB).
 */
const REGISTRO = {
    AlertTriangle,
    Circle,
    Clock,
    Droplets,
    FileText,
    MessageCircle,
    MoreHorizontal,
    Pill,
    PlayCircle,
    RefreshCw,
    ShieldCheck,
    Waves,
};

export default function IconeDinamico({ nome, ...props }) {
    const Icone = REGISTRO[nome] ?? Circle;

    return <Icone {...props} />;
}
