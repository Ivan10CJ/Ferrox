<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Middleware para configurar la zona horaria de México
 * 
 * Este middleware asegura que todas las fechas y horas
 * se manejen en la zona horaria de la Ciudad de México
 */
class SetTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Configurar zona horaria para esta petición
        date_default_timezone_set('America/Mexico_City');
        
        return $next($request);
    }
} 