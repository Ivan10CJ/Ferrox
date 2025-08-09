<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::query();

        // Búsqueda por código O nombre (no excluyentes)
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('codigo', 'like', '%' . $busqueda . '%')
                ->orWhere('nombre', 'like', '%' . $busqueda . '%');
            });
        }

        // Filtro por estado
        if ($request->has('estado')) {
            if ($request->estado === 'activos') {
                $query->where('activo', true);
            } elseif ($request->estado === 'inactivos') {
                $query->where('activo', false);
            }
        }

        // Nuevo filtro por bajas unidades
        if ($request->has('bajas_unidades')) {
            $query->where('unidad', '<', 10);
        }

        $user = Auth::user();
        $rolId = $user->rol->id_rol;
        
        $inventarios = $query->get();
        
        if (auth()->user()->rol->id_rol === 'ADMIN') {
        return view('inventario.inventario-admin', compact('inventarios'));
    } else {
        return view('inventario.inventario-empleado', compact('inventarios'));
    }
    }

    public function handleUser(Request $request)
    {
        $rolId = Auth::user()->rol->id_rol ?? 'N/A';
    }

    public function create()
    {
        return view('inventario.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|alpha_num|unique:inventarios,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_venta' => 'required|in:pieza,metro|max:5',
            'unidad' => 'required|integer|min:0',
            'metros' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'metros_unidad' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'precio_compra_unidad' => 'required|numeric|min:0',
            'precio_unidad' => 'required|numeric|min:0',
            'precio_metro' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'precio_compra_metro' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'activo' => 'sometimes|boolean'
        ]);

        $validated['tipo_venta'] = substr($validated['tipo_venta'], 0, 5);
        $validated['activo'] = $request->has('activo') ? 1 : 0;

        if ($validated['tipo_venta'] === 'pieza') {
            $validated['metros'] = null;
            $validated['metros_unidad'] = null;
            $validated['precio_metro'] = null;
            $validated['precio_compra_metro'] = null;
        }

        Inventario::create($validated);

        return redirect()->route('inventario.index')->with('success', 'Producto agregado con éxito');
    }

    public function edit($id)
    {
        $inventario = Inventario::findOrFail($id);
        return view('inventario.edit', compact('inventario'));
    }

    public function update(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'required|alpha_num|unique:inventarios,codigo,'.$inventario->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_venta' => 'required|in:pieza,metro|max:5',
            'unidad' => 'required|integer|min:0',
            'metros' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'metros_unidad' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'precio_compra_unidad' => 'required|numeric|min:0',
            'precio_unidad' => 'required|numeric|min:0',
            'precio_metro' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'precio_compra_metro' => 'nullable|numeric|min:0|required_if:tipo_venta,metro',
            'activo' => 'required|boolean'
        ]);

        if ($validated['tipo_venta'] === 'pieza') {
            $validated['metros'] = null;
            $validated['metros_unidad'] = null;
            $validated['precio_metro'] = null;
            $validated['precio_compra_metro'] = null;
        }

        $inventario->update($validated);

        return redirect()->route('inventario.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function show($id)
    {
        $inventario = Inventario::findOrFail($id);
        return view('inventario.show', compact('inventario'));
    }

    public function activate($id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->update(['activo' => true]);
        return redirect()->back()->with('success', 'Producto activado correctamente');
    }

    public function deactivate($id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->update(['activo' => false]);
        return redirect()->back()->with('success', 'Producto desactivado correctamente');
    }

    public function destroy($id)
{
    try {
        DB::beginTransaction();
        $item = Inventario::with('detalleVentas')->findOrFail($id);

        if ($item->detalleVentas->isNotEmpty()) {
            DB::rollBack();
            return response()->json([
                'error' => 'No se puede eliminar: existen ventas asociadas a este producto',
                'ventas_relacionadas' => $item->detalleVentas->pluck('id')
            ], 422);
        }

        $item->forceDelete();
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado permanentemente',
            'deleted_id' => $id
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Error al eliminar: ' . $e->getMessage()
        ], 500);
    }
}

    public function actualizarExistencias(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $inventario = Inventario::findOrFail($id);
            $usuario = Auth::user();

            $request->validate([
                'cantidad' => 'required|numeric',
                'motivo' => 'nullable|string|max:255'
            ]);

            $cantidad = $request->input('cantidad');

            if ($usuario->rol === 'empleado' && $cantidad < 0) {
                return redirect()->back()
                    ->withErrors(['cantidad' => 'No tienes permiso para disminuir existencias.'])
                    ->withInput()
                    ->with('from_modal', true);
            }

            $nuevoStock = $inventario->unidad + $cantidad;

            if ($nuevoStock < 0) {
                return redirect()->back()
                    ->withErrors(['cantidad' => 'No se puede dejar stock negativo.'])
                    ->withInput()
                    ->with('from_modal', true);
            }

            $inventario->unidad = $nuevoStock;
            $inventario->save();
            
            DB::commit();

            return redirect()->route('inventario.index')
                ->with('success', 'Existencias actualizadas correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function buscarProducto(Request $request)
    {
        $term = $request->get('term');
        
        $productos = Inventario::where('activo', true)
            ->where(function($query) use ($term) {
                $query->where('codigo', 'like', '%'.$term.'%')
                    ->orWhere('nombre', 'like', '%'.$term.'%');
            })
            ->orderBy('nombre', 'asc')
            ->limit(10)
            ->get();

        $results = [];
        foreach ($productos as $producto) {
            $results[] = [
                'id' => $producto->id,
                'value' => $producto->codigo . ' - ' . $producto->nombre,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'precio_unidad' => $producto->precio_unidad,
                'precio_metro' => $producto->precio_metro,
                'tipo_venta' => $producto->tipo_venta,
                'unidad' => $producto->unidad,
                'metros' => $producto->metros
            ];
        }

        return response()->json($results);
    }
} 