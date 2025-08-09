<?php

namespace App\Http\Controllers;

use App\Models\CorteCaja;
use App\Models\Venta;
use App\Models\MovimientoCaja;
use App\Models\Usuario;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DateTimeHelper;

class CorteController extends Controller
{

    public function index()
{
    $hoy = DateTimeHelper::today()->format('Y-m-d');
    
    // Obtener o crear el corte del día
    $corte = CorteCaja::firstOrCreate(
        ['fecha_inicio' => $hoy],
        [
            'total_efectivo' => 0,
            'total_costos' => 0,
            'total_ventas' => 0,
            'total_ganancias' => 0,
            'creado_en' => DateTimeHelper::now()
        ]
    );

    // Obtener ventas del día con relaciones
    $ventas = Venta::with(['usuario', 'detalles.inventario.producto'])
        ->whereDate('fecha', $hoy)
        ->orderBy('fecha', 'desc')
        ->get();

    // Obtener movimientos de caja del día
    $movimientos = MovimientoCaja::with('usuario')
        ->whereDate('created_at', $hoy)
        ->orderBy('created_at', 'desc')
        ->get();

    // Calcular totales
    $totalVentas = $ventas->sum('total');
    $totalGanancias = $ventas->sum('ganancia');
    $totalCostos = $totalVentas - $totalGanancias;

    // Sumar movimientos de caja (ingresos - egresos)
    $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
    $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
    $totalMovimientos = $totalIngresos - $totalEgresos;

    // Actualizar el corte
    $corte->update([
        'total_efectivo' => $totalMovimientos,
        'total_ventas' => $totalVentas,
        'total_costos' => $totalCostos,
        'total_ganancias' => $totalGanancias
    ]);

    // Refrescar el modelo para obtener los datos actualizados
    $corte->refresh();

    return view('corte.index', compact('corte', 'ventas', 'movimientos'));
}

    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $movimiento = new MovimientoCaja();
            $movimiento->tipo = $request->tipo;
            $movimiento->monto = $request->monto;
            $movimiento->concepto = $request->concepto;
            $movimiento->usuario_id = Auth::id();
            $movimiento->save();

            // Obtener el corte actual
            $corte = CorteCaja::whereDate('fecha_inicio', today())->first();
            
            // Calcular nuevos totales
            if ($request->tipo == 'ingreso') {
                $corte->total_efectivo += $request->monto;
            } else {
                $corte->total_efectivo -= $request->monto;
            }
            
            // Recalcular ganancias sumando las ganancias de todas las ventas del día
            $ventasDelDia = \App\Models\Venta::whereDate('fecha', $corte->fecha_inicio)->get();
            $corte->total_ganancias = $ventasDelDia->sum('ganancia');
            $corte->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'movimiento' => $movimiento->load('usuario'),
                'nuevos_totales' => [
                    'efectivo' => $corte->total_efectivo,
                    'costos' => $corte->total_costos,
                    'ventas' => $corte->total_ventas,
                    'ganancias' => $corte->total_ganancias
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar movimiento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTotalesActuales()
{
    $corte = CorteCaja::whereDate('fecha_inicio', today())->firstOrFail();
    
    // Calcular totales actualizados
    $ventas = \App\Models\Venta::whereDate('fecha', $corte->fecha_inicio)->get();
    $totalVentasDia = $ventas->sum('total');
    
    $movimientos = \App\Models\MovimientoCaja::whereDate('created_at', $corte->fecha_inicio)->get();
    $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
    $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
    
    $totalCaja = $totalVentasDia + $totalIngresos - $totalEgresos;
    
    return response()->json([
        'success' => true,
        'totales' => [
            'efectivo' => $totalCaja,
            'costos' => $corte->total_costos,
            'ventas' => $totalVentasDia,
            'ganancias' => $corte->total_ganancias
        ]
    ]);
}

    public function generarCorte(Request $request)
    {
        Log::info('Intentando cerrar corte de caja', ['request' => $request->all()]);
        $request->validate([
            'efectivo_final' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $corte = CorteCaja::whereDate('fecha_inicio', today())
            ->whereNull('fecha_fin')
            ->first();
        if (!$corte) {
            Log::error('No se encontró corte abierto para hoy');
            return response()->json(['success' => false, 'message' => 'No se encontró corte abierto para hoy'], 404);
        }

        // Calcular totales del día
        $ventas = \App\Models\Venta::whereDate('fecha', $corte->fecha_inicio)->get();
        $totalVentas = $ventas->sum('total');
        $totalGanancias = $ventas->sum('ganancia');
        $totalCostos = $totalVentas - $totalGanancias;

        $movimientos = \App\Models\MovimientoCaja::whereDate('created_at', $corte->fecha_inicio)->get();
        $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
        $totalEfectivo = $totalIngresos - $totalEgresos;

        $corte->fecha_fin = DateTimeHelper::now();
        $corte->total_efectivo = $totalEfectivo;
        $corte->total_ventas = $totalVentas;
        $corte->total_costos = $totalCostos;
        $corte->total_ganancias = $totalGanancias;
        $corte->usuario_id = Auth::id();
        $corte->observaciones = $request->observaciones;
        $corte->save();
        Log::info('Corte actualizado', ['corte' => $corte->toArray()]);

        // Generar y guardar el PDF automáticamente
        $ventas = \App\Models\Venta::with('usuario')
            ->whereDate('fecha', $corte->fecha_inicio)
            ->orderBy('fecha', 'desc')
            ->get();
        $movimientos = \App\Models\MovimientoCaja::whereDate('created_at', $corte->fecha_inicio)->with('usuario')->get();
        $data = [
            'corte' => $corte,
            'ventas' => $ventas,
            'movimientos' => $movimientos,
            'negocio' => config('app.name', 'Ferretería Ferros'),
            'preparado_por' => auth()->user()->nombre_completo ?? 'Usuario',
            'fecha_exportacion' => now()->format('d/m/Y H:i'),
            'periodo_corte' => \Carbon\Carbon::parse($corte->fecha_inicio)->format('d/m/Y') . ($corte->fecha_fin ? ' - ' . \Carbon\Carbon::parse($corte->fecha_fin)->format('d/m/Y') : ' (abierto)'),
        ];
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('corte.pdf_corte', $data);
        $pdfPath = 'cortes/corte_' . $corte->id . '.pdf';
        // Asegurar que la carpeta existe
        if (!Storage::disk('public')->exists('cortes')) {
            Storage::disk('public')->makeDirectory('cortes');
        }
        try {
            Storage::disk('public')->put($pdfPath, $pdf->output());
            Log::info('PDF de corte guardado', ['path' => $pdfPath]);
        } catch (\Exception $e) {
            Log::error('Error al guardar PDF de corte', ['error' => $e->getMessage()]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Corte de caja cerrado correctamente y PDF generado.'
            ]);
        }

        return redirect()->route('corte.index')
            ->with('success', 'Corte de caja cerrado correctamente y PDF generado.');
    }

    public function verDetalleVenta($id)
{
    $venta = Venta::with(['detalles.inventario.producto', 'usuario'])->findOrFail($id);

    $gananciaTotal = $venta->ganancia;

    return view('ventas.detalle', compact('venta', 'gananciaTotal'));
}

    public function historial(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $cortes = CorteCaja::with(['ventas.usuario'])
            ->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
            ->whereNotNull('fecha_fin')
            ->orderBy('fecha_inicio', 'desc')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->fecha_inicio)->format('Y-m-d');
            });

        $totalGeneral = $cortes->flatten()->sum('total');
        $totalVentas = $cortes->flatten()->sum(function($corte) {
            return $corte->ventas->sum('total');
        });

        return view('corte._modal_historico', compact('cortes', 'totalGeneral', 'totalVentas'))->render();
    }

    /**
     * Exporta el PDF del corte de caja.
     */
    public function exportarPDF(\App\Models\CorteCaja $corte)
    {
        // Obtener todas las ventas del día del corte
        $ventas = Venta::with('usuario')
            ->whereDate('fecha', $corte->fecha_inicio)
            ->orderBy('fecha', 'desc')
            ->get();
        $movimientos = \App\Models\MovimientoCaja::whereDate('created_at', $corte->fecha_inicio)->with('usuario')->get();
        $data = [
            'corte' => $corte,
            'ventas' => $ventas,
            'movimientos' => $movimientos,
            'negocio' => config('app.name', 'Ferretería Ferros'),
            'preparado_por' => auth()->user()->nombre_completo ?? 'Usuario',
            'fecha_exportacion' => now()->format('d/m/Y H:i'),
            'periodo_corte' => \Carbon\Carbon::parse($corte->fecha_inicio)->format('d/m/Y') . ($corte->fecha_fin ? ' - ' . \Carbon\Carbon::parse($corte->fecha_fin)->format('d/m/Y') : ' (abierto)'),
        ];
        $pdf = Pdf::loadView('corte.pdf_corte', $data);
        return $pdf->stream('Corte_Caja_' . $corte->fecha_inicio . '.pdf');
    }

    /**
     * Devuelve un array de fechas (Y-m-d) de los últimos 30 días que no tienen corte registrado.
     */
    public function diasSinCorte()
    {
        $dias = [];
        $hoy = now()->startOfDay();
        $inicio = (clone $hoy)->subDays(30);
        $fechas = collect();
        for ($date = $inicio; $date <= $hoy; $date->addDay()) {
            $fechas->push($date->format('Y-m-d'));
        }
        $fechasConCorte = CorteCaja::whereBetween('fecha_inicio', [$inicio, $hoy])->pluck('fecha_inicio')->map(fn($f) => substr($f,0,10));
        $dias = $fechas->diff($fechasConCorte)->values()->all();
        return response()->json(['dias' => $dias]);
    }

    // Ajustar el método de corte fuera para validar que la fecha seleccionada no tenga corte
    public function iniciarCorteFuera(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|before_or_equal:today',
            'efectivo_inicial' => 'required|numeric|min:0',
        ]);

        // Verificar que no exista corte para esa fecha
        $existe = CorteCaja::whereDate('fecha_inicio', $request->fecha)->exists();
        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un corte para esa fecha.'
            ], 422);
        }

        CorteCaja::create([
            'fecha_inicio' => $request->fecha,
            'total_efectivo' => $request->efectivo_inicial,
            'total_costos' => 0,
            'total_ventas' => 0,
            'total_ganancias' => 0,
            'creado_en' => DateTimeHelper::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Corte fuera de tiempo creado correctamente.'
        ]);
    }

    public function exportarHistoricoPDF(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);
        $cortes = CorteCaja::whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
            ->whereNotNull('fecha_fin')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        if ($cortes->isEmpty()) {
            return response()->make('<h2 style="color:red;text-align:center;">No hay cortes en el periodo seleccionado</h2>', 404, ['Content-Type' => 'text/html']);
        }
        $totalVentas = $cortes->sum('total_ventas');
        $totalEfectivo = $cortes->sum('total_efectivo');
        $totalGeneral = $cortes->sum(function($corte) { return $corte->total_efectivo + $corte->total_ventas; });
        $totalGanancias = $cortes->sum('total_ganancias');
        $data = [
            'cortes' => $cortes,
            'fechaInicio' => $request->fecha_inicio,
            'fechaFin' => $request->fecha_fin,
            'totalVentas' => $totalVentas,
            'totalEfectivo' => $totalEfectivo,
            'totalGeneral' => $totalGeneral,
            'totalGanancias' => $totalGanancias,
            'negocio' => config('app.name', 'Ferretería Ferros'),
            'preparado_por' => auth()->user()->nombre_completo ?? 'Usuario',
            'fecha_exportacion' => now()->format('d/m/Y H:i'),
        ];
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('corte.pdf_historico', $data);
        return $pdf->stream('Historico_Cortes_' . $request->fecha_inicio . '_a_' . $request->fecha_fin . '.pdf');
    }

    public function vistaHistorico(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);
        $cortes = CorteCaja::whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
            ->whereNotNull('fecha_fin')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        $totalVentas = $cortes->sum('total_ventas');
        $totalEfectivo = $cortes->sum('total_efectivo');
        $totalGeneral = $cortes->sum(function($corte) { return $corte->total_efectivo + $corte->total_ventas; });
        $totalGanancias = $cortes->sum('total_ganancias');
        return view('corte._historico_nuevo', compact('cortes', 'totalVentas', 'totalEfectivo', 'totalGeneral', 'totalGanancias'))->render();
    }

    /**
     * Exporta el PDF del histórico de cortes (versión pública y robusta)
     */
    public function exportarHistoricoCortesPDF(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);
        $cortes = CorteCaja::with('usuario')
            ->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
            ->whereNotNull('fecha_fin')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        if ($cortes->isEmpty()) {
            return response()->make('<h2 style="color:red;text-align:center;">No hay cortes en el periodo seleccionado</h2>', 404, ['Content-Type' => 'text/html']);
        }
        $totalVentas = $cortes->sum('total_ventas');
        $totalEfectivo = $cortes->sum('total_efectivo');
        $totalGeneral = $cortes->sum(function($corte) { return $corte->total_efectivo + $corte->total_ventas; });
        $totalGanancias = $cortes->sum('total_ganancias');
        $data = [
            'cortes' => $cortes,
            'fechaInicio' => $request->fecha_inicio,
            'fechaFin' => $request->fecha_fin,
            'totalVentas' => $totalVentas,
            'totalEfectivo' => $totalEfectivo,
            'totalGeneral' => $totalGeneral,
            'totalGanancias' => $totalGanancias,
            'negocio' => config('app.name', 'Ferretería Ferros'),
            'preparado_por' => auth()->user()->nombre_completo ?? 'Usuario',
            'fecha_exportacion' => now()->format('d/m/Y H:i'),
        ];
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('corte.pdf_historico', $data);
        return $pdf->stream('Historico_Cortes_' . $request->fecha_inicio . '_a_' . $request->fecha_fin . '.pdf');
    }
}
