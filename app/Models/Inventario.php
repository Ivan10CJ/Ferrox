<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo_venta',
        'unidad',
        'metros',
        'metros_unidad',
        'precio_unidad',
        'precio_compra_unidad',
        'precio_metro',
        'precio_compra_metro',
    ];

    // Relación con detalles de venta
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'inventario_id');
    }

    // Método para verificar disponibilidad
    public function verificarDisponibilidad($tipo, $cantidad)
    {
        if ($tipo === 'unidad') {
            return $this->unidad >= $cantidad;
        } elseif ($tipo === 'metro') {
            $metrosDisponibles = $this->metros + ($this->unidad * $this->metros_unidad);
            return $metrosDisponibles >= $cantidad;
        }
        return false;
    }

    // Método para disminuir stock
    public function disminuirStock($tipo, $cantidad)
    {
        if ($tipo === 'unidad') {
            $this->decrement('unidad', $cantidad);
        } elseif ($tipo === 'metro') {
            $this->disminuirMetros($cantidad);
        }
    }

    // Método para disminuir metros (con lógica de conversión)
    protected function disminuirMetros($metrosVendidos)
    {
        $metrosRestantes = $metrosVendidos;
        
        // Primero descontar de los metros sueltos
        if ($this->metros > 0) {
            $metrosADescontar = min($metrosRestantes, $this->metros);
            $this->decrement('metros', $metrosADescontar);
            $metrosRestantes -= $metrosADescontar;
        }
        
        // Si aún quedan metros por descontar, convertir unidades
        if ($metrosRestantes > 0 && $this->unidad > 0) {
            $unidadesNecesarias = ceil($metrosRestantes / $this->metros_unidad);
            $this->decrement('unidad', $unidadesNecesarias);
            $metrosConvertidos = $unidadesNecesarias * $this->metros_unidad;
            $this->increment('metros', $metrosConvertidos - $metrosRestantes);
        }
        
        $this->save();
    }

    // Scope para búsqueda
    public function scopeBuscar($query, $termino)
    {
        return $query->where('codigo', 'LIKE', "%$termino%")
                    ->orWhere('nombre', 'LIKE', "%$termino%");
    }
}