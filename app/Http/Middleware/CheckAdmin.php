<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->rol->id_rol === 'ADMIN') {
            return $next($request);
        }

        abort(403, 'Acceso no autorizado');
    }
}
