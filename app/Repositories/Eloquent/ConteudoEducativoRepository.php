<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ConteudoEducativo;
use App\Models\Usuario;
use App\Repositories\Contracts\ConteudoEducativoRepositoryInterface;
use Illuminate\Support\Collection;

class ConteudoEducativoRepository implements ConteudoEducativoRepositoryInterface
{
    public function buscarPublicadosParaPaciente(Usuario $paciente, ?int $dispositivoId): Collection
    {
        return ConteudoEducativo::publicados()
            ->paraDispositivo($dispositivoId)
            // Resolve o "é favorito?" no banco, evitando N+1 na listagem.
            ->withExists(['favoritadoPor as favorito' => function ($consulta) use ($paciente): void {
                $consulta->where('usuarios.id', $paciente->id);
            }])
            ->orderBy('titulo')
            ->get();
    }

    public function buscarPublicadoVisivelParaPaciente(int $conteudoId, ?int $dispositivoId): ?ConteudoEducativo
    {
        return ConteudoEducativo::publicados()
            ->paraDispositivo($dispositivoId)
            ->find($conteudoId);
    }

    public function alternarFavorito(Usuario $paciente, ConteudoEducativo $conteudo): bool
    {
        $resultado = $paciente->conteudosFavoritos()->toggle($conteudo);

        return $resultado['attached'] !== [];
    }
}
