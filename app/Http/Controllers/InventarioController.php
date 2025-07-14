<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::query();

        if ($request->filled('buscar_codigo')) {
            $query->where('codigo', 'like', '%' . $request->buscar_codigo . '%');
        }

        $inventarios = $query->get();

        return view('inventario.index', compact('inventarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:100|unique:inventarios,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_venta' => 'required|string',
            'unidad' => 'nullable|numeric|min:0',
            'metros' => 'nullable|numeric|min:0',
            'metros_unidad' => 'nullable|numeric|min:0', // <-- agregado
            'precio_unidad' => 'required|numeric|min:0',
            'precio_metro' => 'nullable|numeric|min:0',
        ]);

        // Crear el inventario con los campos, incluyendo metros_unidad
        Inventario::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'tipo_venta' => $request->tipo_venta,
            'unidad' => $request->unidad,
            'metros' => $request->metros,
            'metros_unidad' => $request->metros_unidad, // <-- agregado
            'precio_unidad' => $request->precio_unidad,
            'precio_metro' => $request->precio_metro,
        ]);

        return redirect()->back()->with('success', 'Producto agregado con éxito');
    }

    public function destroy($id)
    {
        $item = Inventario::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Producto eliminado');
    }

    public function update(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);

        $request->validate([
            'codigo' => 'required|string|max:100|unique:inventarios,codigo,' . $id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_venta' => 'required|string',
            'unidad' => 'nullable|numeric|min:0',
            'metros' => 'nullable|numeric|min:0',
            'metros_unidad' => 'nullable|numeric|min:0', // <-- agregado
            'precio_unidad' => 'required|numeric|min:0',
            'precio_metro' => 'nullable|numeric|min:0',
        ]);

        $inventario->codigo = $request->codigo;
        $inventario->nombre = $request->nombre;
        $inventario->descripcion = $request->descripcion;
        $inventario->tipo_venta = $request->tipo_venta;
        $inventario->unidad = $request->unidad;
        $inventario->metros = $request->metros;
        $inventario->metros_unidad = $request->metros_unidad; // <-- agregado
        $inventario->precio_unidad = $request->precio_unidad;
        $inventario->precio_metro = $request->precio_metro;

        $inventario->save();

        return redirect()->route('inventario.index')->with('success', 'Producto actualizado correctamente.');
    }

    // Nuevo método para actualizar existencias
    public function actualizarExistencias(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);
        $usuario = auth()->user();

        // Validar que 'cantidad' sea numérico requerido
        $request->validate([
            'cantidad' => 'required|numeric',
        ]);

        $cantidad = $request->input('cantidad');

        // Control de permisos por rol
        if ($usuario->rol === 'empleado' && $cantidad < 0) {
            return redirect()->back()->withErrors(['cantidad' => 'No tienes permiso para disminuir existencias.'])->withInput()->with('from_modal', true);
        }

        $nuevoStock = $inventario->unidad + $cantidad;

        if ($nuevoStock < 0) {
            return redirect()->back()->withErrors(['cantidad' => 'No se puede dejar stock negativo.'])->withInput()->with('from_modal', true);
        }

        // Actualizar existencias
        $inventario->unidad = $nuevoStock;
        $inventario->save();

        return redirect()->route('inventario.index')->with('success', 'Existencias actualizadas correctamente.');
    }
}
