<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo', 
        'nombre', 
        'descripcion', 
        'precio_unidad',  // precio de venta
        'precio_costo',
        'unidad_base_id', // Relación con unidad de medida
        'stock'
    ];

    /**
     * Relación con la unidad de medida base
     */
    public function unidadBase(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_base_id');
    }

    /**
     * Relación con los detalles de venta
     */
    public function detallesVenta(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }
}