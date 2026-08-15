<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege toda a área administrativa. Um paciente autenticado recebe 403 —
 * a área não fica acessível só por conhecer a URL.
 */
class GarantirQueEhAdministrador
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        abort_if($usuario === null || ! $usuario->ehAdministrador(), 403, 'Área restrita à equipe.');

        return $next($request);
    }
}
