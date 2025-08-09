<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'fecha',
        'total',
        'ganancia',
        'corte_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function corte()
    {
        return $this->belongsTo(CorteCaja::class, 'corte_id');
    }
}

