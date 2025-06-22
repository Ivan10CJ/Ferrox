<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';

    protected $fillable = ['nombre'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'unidad_base_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'unidad_venta_id');
    }
}
