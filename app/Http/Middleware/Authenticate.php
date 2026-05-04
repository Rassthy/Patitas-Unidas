<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Obtiene la ruta a la que se debe redirigir al usuario cuando no esté autenticado.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Redirigir a la página de inicio (donde está el modal de login)
        return route('home') ?? '/';
    }
}
