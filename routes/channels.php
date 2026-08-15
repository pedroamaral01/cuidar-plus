<?php

declare(strict_types=1);

use App\Models\Usuario;
use Illuminate\Support\Facades\Broadcast;

/**
 * Canal privado de cada usuário.
 *
 * A autorização usa a sessão autenticada — nunca um token no cliente. É este
 * ponto que garante a regra crítica de isolamento também no tempo real:
 * o paciente A não consegue escutar o canal do paciente B.
 *
 * O nome do canal segue o padrão que o trait Notifiable monta sozinho a partir
 * da classe do Model: App.Models.Usuario.{id}.
 */
/*
 * O {id} chega do nome do canal como string. Com declare(strict_types=1),
 * tipar o parâmetro como int faria o PHP lançar TypeError — que o Laravel
 * transforma em 403, barrando até o dono legítimo do canal. Por isso o
 * parâmetro é string e a conversão é explícita.
 */
Broadcast::channel('App.Models.Usuario.{id}', function (Usuario $usuario, string $id): bool {
    return $usuario->id === (int) $id;
});
