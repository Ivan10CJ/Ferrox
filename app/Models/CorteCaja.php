<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table = 'cortes_caja';

    public $timestamps = false;

    protected $fillable = ['fecha_inicio', 'fecha_fin', 'total', 'creado_en'];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'corte_id');
    }
}
