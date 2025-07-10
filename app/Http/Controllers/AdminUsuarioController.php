<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUsuarioController extends Controller
{
    // Mostrar lista de usuarios
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $roles = Rol::all();
        return view('admin.usuarios.create', compact('roles'));
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'nombre_usuario' => 'required|string|max:255|unique:usuarios,nombre_usuario',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',      // al menos una minúscula
                'regex:/[A-Z]/',      // al menos una mayúscula
                'regex:/[0-9]/',      // al menos un número
                'regex:/[^a-zA-Z0-9]/', // al menos un símbolo
                'confirmed'           // debe coincidir con confirmación
            ],
            'id_rol' => 'required|exists:roles,id_rol'
        ]);

        Usuario::create([
            'nombre_completo' => $request->nombre_completo,
            'nombre_usuario' => $request->nombre_usuario,
            'password_hash' => Hash::make($request->password),
            'id_rol' => $request->id_rol
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $roles = Rol::all();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
'nombre_completo' => 'required|string|max:255',
            'nombre_usuario' => 'required|string|max:255|unique:usuarios,nombre_usuario,' . $id . ',id_usuario',
            'id_rol' => 'required|exists:roles,id_rol',
'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[a-z]/',      // al menos una minúscula
                'regex:/[A-Z]/',      // al menos una mayúscula
                'regex:/[0-9]/',      // al menos un número
                'regex:/[^a-zA-Z0-9]/' // al menos un símbolo
            ],
            'password_confirmation' => 'same:password',
        ]);

        $usuario->update([
            'nombre_completo' => $request->nombre_completo,
            'nombre_usuario' => $request->nombre_usuario,
            'id_rol' => $request->id_rol,
             'password_hash' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado.');
    }
}