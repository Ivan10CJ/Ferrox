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
    public function index()
    {
        return view('ventas.index');
    }

    public function buscar($buscar)
    {
        $productos = Producto::where('codigo', 'LIKE', "%$buscar%")
            ->orWhere('nombre', 'LIKE', "%$buscar%")
            ->get();

        if ($productos->isEmpty()) {
            return response()->json([]);
        }

        $productosTransformados = $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'unidades' => $producto->unidades,
                'metros_sobrantes' => $producto->metros_sobrantes,
                'metros_unidad' => $producto->metros_unidad,
                'precio_unidad' => $producto->precio_unidad,
                'precio_metro' => $producto->precio_metro
            ];
        });

        return response()->json($productosTransformados);
    }

    public function guardar(Request $request)
    {
        DB::beginTransaction();

        try {
            $productos = $request->input('productos');
            $montoPagado = $request->input('pago_cliente');

            if (empty($productos)) {
                return response()->json(['success' => false, 'message' => 'No hay productos para vender.']);
            }

            $total = collect($productos)->sum('subtotal');

            if ($montoPagado < $total) {
                return response()->json(['success' => false, 'message' => 'El monto pagado es insuficiente.']);
            }

            $venta = new Venta();
            $venta->usuario_id = auth()->user()->id_usuario;
            $venta->total = $total;
            $venta->save();

            foreach ($productos as $item) {
                $producto = Producto::findOrFail($item['id']);

                $detalle = new DetalleVenta();
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $producto->id;
                $detalle->cantidad = $item['cantidad'];
                $detalle->unidad_venta_id = $item['unidad_venta_id'];
                $detalle->precio_unitario = $item['precio'];
                $detalle->subtotal = $item['subtotal'];
                $detalle->save();

                if ($item['unidad_venta_id'] == 1) {
                    $producto->unidades -= $item['cantidad'];
                } elseif ($item['unidad_venta_id'] == 2) {
                    $producto->metros_sobrantes -= $item['cantidad'];

                    while ($producto->metros_sobrantes < 0 && $producto->unidades > 0) {
                        $producto->unidades -= 1;
                        $producto->metros_sobrantes += $producto->metros_unidad;
                    }

                    if ($producto->metros_sobrantes < 0) {
                        throw new \Exception("Stock insuficiente en metros para el producto: {$producto->nombre}");
                    }
                }

                $producto->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'venta_id' => $venta->id,
                'pago' => $montoPagado,
                'cambio' => $montoPagado - $total,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function generarTicket(Request $request, $id)
    {
        $venta = Venta::with('detalles.producto', 'usuario')->findOrFail($id);

        // Se obtienen desde la URL (query string)
        $pago = $request->input('pago', $venta->total);
        $cambio = $request->input('cambio', 0);

        $data = [
            'venta' => $venta,
            'total' => $venta->total,
            'pago' => $pago,
            'cambio' => $cambio,
        ];

        $pdf = Pdf::loadView('ventas.ticket', $data);

        return $pdf->download('ticket_venta_' . $venta->id . '.pdf');
    }
}
