<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $usuario = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'usuario' => $this->dadosDoUsuarioAutenticado($usuario),
            ],
            // Badge do sino, presente em toda a navegação.
            'naoLidas' => fn (): int => $usuario?->unreadNotifications()->count() ?? 0,
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }

    /**
     * Dados mínimos do usuário logado compartilhados com todas as telas.
     * Nunca expõe senha, token ou dado de outro paciente.
     *
     * @return array<string, mixed>|null
     */
    private function dadosDoUsuarioAutenticado(?Usuario $usuario): ?array
    {
        if ($usuario === null) {
            return null;
        }

        return [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'perfil' => $usuario->perfil->value,
            'iniciais' => $this->extrairIniciais($usuario->nome),
        ];
    }

    /** "Maria Aparecida" -> "M"; usado no avatar do cabeçalho. */
    private function extrairIniciais(string $nome): string
    {
        return mb_strtoupper(mb_substr(trim($nome), 0, 1));
    }
}
