<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar zona horaria para Carbon
        Carbon::setLocale('es');
        
        // Asegurar que todas las fechas se manejen en la zona horaria de México
        date_default_timezone_set('America/Mexico_City');
    }
}
