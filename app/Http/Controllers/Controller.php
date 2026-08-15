<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Habilita $this->authorize() nos Controllers, usado pelas Policies que
    // garantem a regra de isolamento (paciente A nunca acessa dado do B).
    use AuthorizesRequests;
}
