<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'fecha',
        'total',
        'corte_id'
    ];

    // Relación: La venta pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }

    // Relación: La venta puede tener muchos detalles
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    // Relación: La venta puede pertenecer a un corte de caja (opcional)
    public function corte()
    {
        return $this->belongsTo(CorteCaja::class, 'corte_id');
    }
}
