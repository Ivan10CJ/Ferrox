<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\CorteCaja;
use Illuminate\Support\Facades\Auth;

class CorteCajaController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();
        $ventas = Venta::whereDate('created_at', $hoy)->get();

        $total_general = $ventas->sum('total');
        $total_ganancia = $ventas->sum('ganancia');
        $total_costo = $total_general - $total_ganancia;
        $total_en_caja = $ventas->sum('total');

        return view('corte_caja.index', compact(
            'ventas',
            'total_general',
            'total_costo',
            'total_ganancia',
            'total_en_caja'
        ));
    }
}
