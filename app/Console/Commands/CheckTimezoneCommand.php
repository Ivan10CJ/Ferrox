<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Helpers\DateTimeHelper;

/**
 * Comando para verificar la configuración de zona horaria
 * 
 * Este comando verifica que la zona horaria esté configurada
 * correctamente para la Ciudad de México
 */
class CheckTimezoneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timezone:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la configuración de zona horaria';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🕐 Verificando configuración de zona horaria...');
        $this->newLine();

        // Verificar zona horaria de PHP
        $phpTimezone = date_default_timezone_get();
        $this->line("📅 Zona horaria de PHP: {$phpTimezone}");
        
        if ($phpTimezone === 'America/Mexico_City') {
            $this->info('✅ Zona horaria de PHP configurada correctamente');
        } else {
            $this->error('❌ Zona horaria de PHP incorrecta. Debería ser America/Mexico_City');
        }

        // Verificar zona horaria de Carbon
        $carbonTimezone = Carbon::now()->timezone->getName();
        $this->line("🕒 Zona horaria de Carbon: {$carbonTimezone}");
        
        if ($carbonTimezone === 'America/Mexico_City') {
            $this->info('✅ Zona horaria de Carbon configurada correctamente');
        } else {
            $this->warn('⚠️ Zona horaria de Carbon: ' . $carbonTimezone);
        }

        // Verificar configuración de Laravel
        $laravelTimezone = config('app.timezone');
        $this->line("⚙️ Zona horaria de Laravel: {$laravelTimezone}");
        
        if ($laravelTimezone === 'America/Mexico_City') {
            $this->info('✅ Zona horaria de Laravel configurada correctamente');
        } else {
            $this->error('❌ Zona horaria de Laravel incorrecta. Debería ser America/Mexico_City');
        }

        $this->newLine();

        // Mostrar hora actual en diferentes formatos
        $this->info('🕐 Hora actual:');
        $this->line("   PHP date(): " . date('Y-m-d H:i:s'));
        $this->line("   Carbon::now(): " . Carbon::now()->format('Y-m-d H:i:s'));
        $this->line("   DateTimeHelper::now(): " . DateTimeHelper::now()->format('Y-m-d H:i:s'));
        $this->line("   UTC: " . Carbon::now('UTC')->format('Y-m-d H:i:s'));

        $this->newLine();

        // Verificar si hay diferencia horaria
        $mexicoTime = Carbon::now('America/Mexico_City');
        $utcTime = Carbon::now('UTC');
        $difference = $mexicoTime->diffInHours($utcTime, false);
        
        $this->info("⏰ Diferencia horaria con UTC: {$difference} horas");
        
        if ($difference === -6 || $difference === -5) {
            $this->info('✅ Diferencia horaria correcta para México');
        } else {
            $this->warn('⚠️ Diferencia horaria inesperada');
        }

        $this->newLine();

        // Verificar horario de verano
        $isDST = $mexicoTime->isDST();
        $this->line("🌞 Horario de verano: " . ($isDST ? 'Sí' : 'No'));

        $this->newLine();
        $this->info('✅ Verificación de zona horaria completada');

        return 0;
    }
} 