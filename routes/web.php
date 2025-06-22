<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckEmpleado;
use App\Http\Controllers\VentaController;

// Ruta raíz → Redirige al login
Route::get('/', function () {
    return redirect('/login');
});

// Rutas públicas (usuarios no autenticados)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// Rutas protegidas (usuarios autenticados)
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Rutas exclusivas para administrador
    Route::middleware([CheckAdmin::class])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    // Rutas exclusivas para empleado
    Route::middleware([CheckEmpleado::class])->group(function () {
        Route::get('/empleado/dashboard', function () {
            return view('empleado.dashboard');
        })->name('empleado.dashboard');
    });

    // Rutas compartidas para ventas (admin y empleado)
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/buscar/{buscar}', [VentaController::class, 'buscar'])->name('ventas.buscar');
    Route::post('/ventas/guardar', [VentaController::class, 'guardar'])->name('ventas.guardar');
    Route::get('/ventas/ticket/{id}', [VentaController::class, 'ticket'])->name('ventas.ticket');
    Route::get('/ventas/ticket/{id}', [VentaController::class, 'generarTicket'])->name('ventas.ticket');


});
