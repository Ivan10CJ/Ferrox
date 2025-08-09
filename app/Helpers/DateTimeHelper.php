<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Helper para manejo de fechas y horas en zona horaria de México
 */
class DateTimeHelper
{
    /**
     * Obtiene la fecha y hora actual en zona horaria de México
     * 
     * @return Carbon
     */
    public static function now()
    {
        return Carbon::now('America/Mexico_City');
    }

    /**
     * Obtiene la fecha actual en zona horaria de México
     * 
     * @return Carbon
     */
    public static function today()
    {
        return Carbon::today('America/Mexico_City');
    }

    /**
     * Formatea una fecha en formato legible para México
     * 
     * @param Carbon|string $date
     * @param string $format
     * @return string
     */
    public static function format($date, $format = 'd/m/Y H:i:s')
    {
        if (is_string($date)) {
            $date = Carbon::parse($date, 'America/Mexico_City');
        }
        
        return $date->format($format);
    }

    /**
     * Convierte una fecha a zona horaria de México
     * 
     * @param Carbon|string $date
     * @return Carbon
     */
    public static function toMexicoTime($date)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $date->setTimezone('America/Mexico_City');
    }

    /**
     * Verifica si una fecha es hoy en zona horaria de México
     * 
     * @param Carbon|string $date
     * @return bool
     */
    public static function isToday($date)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date, 'America/Mexico_City');
        }
        
        return $date->isToday();
    }

    /**
     * Obtiene el timestamp actual en zona horaria de México
     * 
     * @return int
     */
    public static function timestamp()
    {
        return Carbon::now('America/Mexico_City')->timestamp;
    }
} 