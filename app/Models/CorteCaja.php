<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table = 'cortes_caja';
    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'usuario_id',
        'total_efectivo',
        'total_ventas',
        'total_costos',
        'total_ganancias',
        'efectivo_final',
        'observaciones'
    ];
    public $timestamps = false;
    /**
     * Relación: Ventas asociadas a este corte de caja.
     */
    public function ventas()
    {
        return $this->hasMany(\App\Models\Venta::class, 'corte_id', 'id');
    }
    /**
     * Relación: Usuario responsable de este corte de caja.
     */
    public function usuario()
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'usuario_id', 'id_usuario');
    }
}
