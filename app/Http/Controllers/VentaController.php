<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramService;
use App\Helpers\DateTimeHelper;

class VentaController extends Controller
{
    public function index()
    {
        return view('ventas.index');
    }

    public function buscarProducto(Request $request)
    {
        $query = $request->input('query');
        $productos = Inventario::buscar($query)->get();
        
        return response()->json($productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'tipo_venta' => $producto->tipo_venta,
                'precio_unidad' => $producto->precio_unidad,
                'precio_metro' => $producto->precio_metro,
                'unidad' => $producto->unidad,
                'metros' => $producto->metros,
                'metros_unidad' => $producto->metros_unidad
            ];
        }));
    }

    public function verificarStock(Request $request)
    {
        $productos = $request->input('productos');
        $errores = [];

        foreach ($productos as $producto) {
            $item = Inventario::find($producto['id']);
            
            if (!$item) {
                $errores[] = "Producto no encontrado: {$producto['nombre']}";
                continue;
            }

            if (!$item->verificarDisponibilidad($producto['tipo'], $producto['cantidad'])) {
                $tipo = $producto['tipo'] === 'unidad' ? 'unidades' : 'metros';
                $errores[] = "Stock insuficiente para {$item->nombre} (necesitas {$producto['cantidad']} {$tipo})";
            }
        }

        if (!empty($errores)) {
            return response()->json(['error' => implode(', ', $errores)], 400);
        }

        return response()->json(['success' => true]);
    }
////////////////////////////////////////////////////////////////////////////////////////
 public function registrarVenta(Request $request, TelegramService $telegram)
    {
         DB::beginTransaction();

    try {
        // Validación básica de los datos recibidos
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:inventarios,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0.01',
            'productos.*.tipo' => 'required|in:unidad,metro',
            'monto_recibido' => 'required|numeric|min:0'
        ]);

        $productos = $request->input('productos');
        $usuarioId = Auth::id();
        $total = 0;
        $gananciaTotal = 0;

        // Crear la venta
        $venta = new Venta();
        $venta->usuario_id = $usuarioId;
        $venta->fecha = DateTimeHelper::now();
        $venta->total = 0; // Se actualizará después
        $venta->ganancia = 0; // Se actualizará después
        $venta->corte_id = $this->obtenerCorteActivo()->id ?? null;
        $venta->save();

        // Procesar cada producto
        foreach ($productos as $prod) {
            $inventario = Inventario::findOrFail($prod['id']);
            
            // Calcular subtotal y ganancia
            $subtotal = $prod['cantidad'] * $prod['precio'];
            $precioCompra = $prod['tipo'] === 'unidad' 
                ? $inventario->precio_compra_unidad 
                : $inventario->precio_compra_metro;
            $ganancia = ($prod['precio'] - $precioCompra) * $prod['cantidad'];

            // Registrar detalle
            $detalle = new DetalleVenta();
            $detalle->venta_id = $venta->id;
            $detalle->inventario_id = $inventario->id;
            $detalle->cantidad = $prod['cantidad'];
            $detalle->precio_unitario = $prod['precio'];
            $detalle->subtotal = $subtotal;
            $detalle->save();

            // Actualizar inventario
            if ($prod['tipo'] === 'unidad') {
                $inventario->decrement('unidad', $prod['cantidad']);
            } else {
                $this->disminuirMetrosInventario($inventario, $prod['cantidad']);
            }

            $total += $subtotal;
            $gananciaTotal += $ganancia;
        }

        // Actualizar totales de la venta
        $venta->total = $total;
        $venta->ganancia = $gananciaTotal;
        $venta->save();


        $mensaje = "<b>🧾 Venta registrada</b>\n";
        $mensaje .= "🧑 Usuario: " . (Auth::user()->nombre_completo ?? 'Desconocido') . "\n";
        $mensaje .= "🕒 Fecha: " . now()->format('d/m/Y H:i:s') . "\n";
        $mensaje .= "💰 Total: $" . number_format($total, 2) . "\n\n";
        $mensaje .= "📦 Productos:\n";
        
        foreach ($productos as $prod) {
            $inventario = Inventario::find($prod['id']);
            $nombre = $inventario->nombre ?? 'Producto eliminado';
            $mensaje .= "- {$nombre} ({$prod['cantidad']} {$prod['tipo']}) x $" . number_format($prod['precio'], 2) . "\n";
        }
        
        $telegram->sendMessage($mensaje);


        DB::commit();

        return response()->json([
            'success' => true,
            'venta_id' => $venta->id,
            'total' => $total,
            'message' => 'Venta registrada correctamente'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al registrar venta: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Error al registrar la venta: ' . $e->getMessage(),
            'trace' => env('APP_DEBUG') ? $e->getTrace() : null
        ], 500);
    }
}
////////////////////////////////////////////////////////////////////////////////////
private function disminuirMetrosInventario($inventario, $metrosVendidos)
{
    $metrosRestantes = $metrosVendidos;
    
    // Primero descontar de los metros sueltos
    if ($inventario->metros > 0) {
        $metrosADescontar = min($metrosRestantes, $inventario->metros);
        $inventario->decrement('metros', $metrosADescontar);
        $metrosRestantes -= $metrosADescontar;
    }
    
    // Si aún quedan metros por descontar, convertir unidades
    if ($metrosRestantes > 0 && $inventario->unidad > 0) {
        $unidadesNecesarias = ceil($metrosRestantes / $inventario->metros_unidad);
        $inventario->decrement('unidad', $unidadesNecesarias);
        $metrosConvertidos = $unidadesNecesarias * $inventario->metros_unidad;
        $inventario->increment('metros', $metrosConvertidos - $metrosRestantes);
    }
}
/////////////////////////////////////////////////////////////////////////////
    protected function obtenerCorteActivo()
    {
        return DB::table('cortes_caja')
            ->whereNull('fecha_fin')
            ->orderBy('fecha_inicio', 'desc')
            ->first();
    }
///////////////////////////////////////////////////////////
    public function generarTicket($id)
    {
         try {
        // Cargar la venta con todas las relaciones necesarias
         $venta = Venta::with([
            'usuario',
            'detalles.inventario' => function($query) {
                $query->select('id', 'nombre', 'tipo_venta', 'precio_unidad', 'precio_metro');
            }
        ])->findOrFail($id);

        // Preparar los datos para la vista
        $data = [
            'venta' => $venta,
            'fecha' => Carbon::parse($venta->fecha)->format('d/m/Y H:i:s'),
            'detalles' => $venta->detalles,
            'usuario' => $venta->usuario->nombre_completo ?? 'Venta rápida',
        ];

        // Cargar la vista y generar el PDF
        $pdf = PDF::loadView('ventas.ticket', $data);

        // Configurar el nombre del archivo
        $filename = "ticket_venta_{$venta->id}.pdf";

        // Retornar el PDF para visualización en el navegador
        return $pdf->stream($filename);

    } catch (\Exception $e) {
        // Registrar el error y retornar una respuesta adecuada
        Log::error("Error al generar ticket: " . $e->getMessage());
        return response()->json([
            'error' => 'No se pudo generar el ticket',
            'message' => $e->getMessage()
        ], 500);
    }
}
////////////////////////////////////////////////////////////////////
    protected function convertirNumeroALetras($numero)
    {
        // Implementación de conversión de número a letras
        // Puedes usar un paquete como "numero-a-letras"
        return "** IMPLEMENTA CONVERSIÓN A LETRAS AQUÍ **";
    }

    public function verDetalle($id)
    {
        $venta = Venta::with([
            'usuario', 
            'detalles.inventario',
            'detalles.inventario.producto'
        ])->findOrFail($id);
        
        $gananciaTotal = $venta->ganancia;
        
        // Renderizar la vista parcial o devolver JSON según la petición
        if (request()->ajax()) {
            // Si la petición viene del módulo de corte, usar la vista del corte
            $referer = request()->header('Referer');
            if (strpos($referer, '/corte') !== false) {
                $html = view('corte._detalle_venta', compact('venta', 'gananciaTotal'))->render();
            } else {
                $html = view('ventas.detalle', compact('venta', 'gananciaTotal'))->render();
            }
            return response()->json(['success' => true, 'html' => $html]);
        }
        return view('ventas.detalle', compact('venta', 'gananciaTotal'));
    }
}