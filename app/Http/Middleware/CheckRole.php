<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (!$user || $user->rol->id_rol !== $role) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
