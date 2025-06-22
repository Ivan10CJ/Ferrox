<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Usuario::where('nombre_usuario', $request->nombre_usuario)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'nombre_usuario' => 'Credenciales incorrectas',
            ]);
        }

        auth()->login($user);

        $rol = $user->rol->id_rol;

        if ($rol === 'ADMIN') {
            return redirect()->intended('/admin/dashboard');
        } elseif ($rol === 'EMPLEA') {
            return redirect()->intended('/empleado/dashboard');
        } else {
            auth()->logout();
            return redirect('/login')->withErrors([
                'nombre_usuario' => 'Rol no autorizado.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
