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
    public function index(Request $request)
    {
        $estatus = $request->input('estatus', 'activo'); // valor por defecto: 'activo'

        $usuarios = Usuario::with('rol')
            ->where('estatus', $estatus)
            ->get();

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
        $validatedData = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'nombre_usuario' => [
                'required',
                'string',
                'max:50',
                'min:6',
                'regex:/[A-Z]/', // al menos una mayúscula
                'regex:/[0-9]/', // al menos un número
                'unique:usuarios,nombre_usuario'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',       // al menos una minúscula
                'regex:/[A-Z]/',       // al menos una mayúscula
                'regex:/[0-9]/',       // al menos un número
                'regex:/[^a-zA-Z0-9]/',// al menos un símbolo
                'confirmed'
            ],
            'id_rol' => 'required|exists:roles,id_rol'
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio',
            'nombre_completo.max' => 'El nombre no debe exceder 255 caracteres',
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio',
            'nombre_usuario.min' => 'El nombre de usuario debe tener al menos 6 caracteres',
            'nombre_usuario.max' => 'El nombre de usuario no debe exceder 50 caracteres',
            'nombre_usuario.regex' => 'El nombre de usuario debe contener al menos una mayúscula y un número',
            'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 símbolo',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'id_rol.required' => 'Debe seleccionar un rol',
            'id_rol.exists' => 'El rol seleccionado no es válido'
        ]);

        Usuario::create([
            'nombre_completo' => $validatedData['nombre_completo'],
            'nombre_usuario' => $validatedData['nombre_usuario'],
            'password_hash' => Hash::make($validatedData['password']),
            'id_rol' => $validatedData['id_rol']
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

        $validatedData = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'nombre_usuario' => [
                'required',
                'string',
                'max:50',
                'min:6',
                'regex:/[A-Z]/', // al menos una mayúscula
                'regex:/[0-9]/', // al menos un número
                'unique:usuarios,nombre_usuario,' . $id . ',id_usuario'
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^a-zA-Z0-9]/'
            ],
            'password_confirmation' => 'required_with:password|same:password',
            'id_rol' => 'required|exists:roles,id_rol'
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio',
            'nombre_completo.max' => 'El nombre no debe exceder 255 caracteres',
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio',
            'nombre_usuario.min' => 'El nombre de usuario debe tener al menos 6 caracteres',
            'nombre_usuario.max' => 'El nombre de usuario no debe exceder 50 caracteres',
            'nombre_usuario.regex' => 'El nombre de usuario debe contener al menos una mayúscula y un número',
            'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 símbolo',
            'password_confirmation.required_with' => 'Debe confirmar la contraseña',
            'password_confirmation.same' => 'Las contraseñas no coinciden',
            'id_rol.required' => 'Debe seleccionar un rol',
            'id_rol.exists' => 'El rol seleccionado no es válido'
        ]);

        $updateData = [
            'nombre_completo' => $validatedData['nombre_completo'],
            'nombre_usuario' => $validatedData['nombre_usuario'],
            'id_rol' => $validatedData['id_rol'],
        ];

        if (!empty($validatedData['password'])) {
            $updateData['password_hash'] = Hash::make($validatedData['password']);
        }

        $usuario->update($updateData);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Dar de baja al usuario
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->estatus = 'baja';
        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario dado de baja correctamente.');
    }

    public function restore($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->estatus = 'activo';
        $usuario->save();

        return redirect()->route('admin.usuarios.index', ['estatus' => 'baja'])->with('success', 'Usuario dado de alta correctamente.');
    }
}
