export default function LogoMarca({ tamanho = 32, className = '' }) {
    return (
        <svg
            width={tamanho}
            height={tamanho}
            viewBox="0 0 48 48"
            fill="none"
            className={className}
            role="img"
            aria-label="Cuidar+"
        >
            <path
                d="M24 40C24 40 6 28.5 6 17.5C6 11.5 10.8 7 16.5 7C19.8 7 22.6 8.7 24 11.3C25.4 8.7 28.2 7 31.5 7C37.2 7 42 11.5 42 17.5C42 28.5 24 40 24 40Z"
                fill="#2F9C8F"
            />
            <rect x="21.2" y="14" width="5.6" height="16" rx="1.5" fill="#FBF9F4" />
            <rect x="16" y="19.2" width="16" height="5.6" rx="1.5" fill="#FBF9F4" />
        </svg>
    );
}
