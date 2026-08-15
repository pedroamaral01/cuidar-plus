<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege as telas do paciente. Um administrador é redirecionado para a área
 * dele em vez de ver um painel de paciente vazio.
 */
class GarantirQueEhPaciente
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        abort_if($usuario === null, 403);

        if ($usuario->ehAdministrador()) {
            return redirect()->route('administracao.painel');
        }

        return $next($request);
    }
}
