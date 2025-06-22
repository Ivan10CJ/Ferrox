<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    // Mostrar la vista
    public function index()
    {
        return view('ventas.index');
    }

    // Buscar productos por código o nombre
    public function buscar($buscar)
    {
        $productos = Producto::where('codigo', 'LIKE', "%$buscar%")
            ->orWhere('nombre', 'LIKE', "%$buscar%")
            ->with('unidadBase') // Cargamos la relación
            ->get();

        if ($productos->isEmpty()) {
            return response()->json([]);
        }

        // Transformamos la respuesta para que unidad_base sea texto, no objeto
        $productosTransformados = $productos->map(function($producto) {
            return [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'unidad_base' => $producto->unidadBase->nombre, // Solo el nombre
                'precio' => $producto->precio,
                'stock' => $producto->stock
            ];
        });

        return response()->json($productosTransformados);
    }

    // Guardar venta
   public function guardar(Request $request)
{
    DB::beginTransaction();

    try {
        $venta = new Venta();
        $venta->usuario_id = auth()->user()->id_usuario;
        $venta->total = collect($request->productos)->sum('subtotal');
        $venta->save();

        foreach ($request->productos as $item) {
            $detalle = new DetalleVenta();
            $detalle->venta_id = $venta->id;
            $detalle->producto_id = $item['id'];
            $detalle->cantidad = $item['cantidad'];
            $detalle->unidad_venta_id = Producto::find($item['id'])->unidad_base_id;
            $detalle->precio_unitario = $item['precio'];
            $detalle->subtotal = $item['subtotal'];
            $detalle->save();

            // Actualizar stock
            $producto = Producto::find($item['id']);
            $producto->stock -= $item['cantidad'];
            $producto->save();
        }

        DB::commit();

        // ✅ Guardamos el pago y el cambio en la sesión
        session(['pago_cliente' => $request->monto_pagado]);
        session(['cambio_cliente' => $request->cambio]);

        return response()->json(['success' => true, 'venta_id' => $venta->id]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Error al registrar la venta.']);
    }
}


    // Generar ticket PDF
    public function generarTicket($id)
    {
        $venta = Venta::with('detalles.producto.unidadBase', 'usuario')->findOrFail($id);

        $data = [
            'venta' => $venta,
            'total' => $venta->total,
            'pago' => session('pago_cliente'),
            'cambio' => session('cambio_cliente'),
        ];

        $pdf = Pdf::loadView('ventas.ticket', $data);
        


        return $pdf->download('ticket_venta_' . $venta->id . '.pdf');
    }
}
