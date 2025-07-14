<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckEmpleado;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\AdminUsuarioController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CorteCajaController;
use App\Http\Controllers\InventarioController;



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

        // Rutas para gestión de usuarios
        Route::get('/admin/usuarios', [AdminUsuarioController::class, 'index'])->name('admin.usuarios.index');
        Route::get('/admin/usuarios/create', [AdminUsuarioController::class, 'create'])->name('admin.usuarios.create');
        Route::post('/admin/usuarios', [AdminUsuarioController::class, 'store'])->name('admin.usuarios.store');
        Route::get('/admin/usuarios/{id}/edit', [AdminUsuarioController::class, 'edit'])->name('admin.usuarios.edit');
        Route::put('/admin/usuarios/{id}', [AdminUsuarioController::class, 'update'])->name('admin.usuarios.update');
        Route::delete('/admin/usuarios/{id}', [AdminUsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');
        Route::post('/admin/usuarios/{id}/reset', [AdminUsuarioController::class, 'reset'])->name('admin.usuarios.reset');
    });

    // Rutas exclusivas para empleado
    Route::middleware([CheckEmpleado::class])->group(function () {
        Route::get('/empleado/dashboard', function () {
            return view('empleado.dashboard');
        })->name('empleado.dashboard');
    });

    // Rutas compartidas para ventas (admin y empleado)
    
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/buscar-producto', [VentaController::class, 'buscarProducto'])->name('ventas.buscarProducto');
    Route::post('/ventas/registrar', [VentaController::class, 'registrarVenta'])->name('ventas.registrar');
    Route::get('/ventas/ticket/{id}', [VentaController::class, 'generarTicket'])->name('ventas.ticket');
    Route::post('/ventas/verificar-stock', [VentaController::class, 'verificarStock'])->name('ventas.verificar-stock');
    


});

    //vic
    Route::get('/corte-caja', [CorteCajaController::class, 'index'])->name('corte-caja.index');
    

    // routes/web.php
    Route::resource('inventario', App\Http\Controllers\InventarioController::class);
    Route::resource('inventario', InventarioController::class);

    Route::post('/inventario/{id}/actualizar-existencias', [InventarioController::class, 'actualizarExistencias'])->name('inventario.actualizar_existencias');
