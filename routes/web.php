<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckEmpleado;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\AdminUsuarioController;
use App\Http\Controllers\CorteController;
use App\Http\Controllers\CorteCajaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AdminController;


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
        Route::post('/admin/usuarios/{id}/restore', [AdminUsuarioController::class, 'restore'])->name('admin.usuarios.restore');

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

    // routes/web.php
    Route::resource('inventario', App\Http\Controllers\InventarioController::class);
    Route::resource('inventario', InventarioController::class);

    Route::post('/inventario/{id}/actualizar-existencias', [InventarioController::class, 'actualizarExistencias'])->name('inventario.actualizar_existencias');
    Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
Route::post('/corte/movimiento', [CorteController::class, 'registrarMovimiento'])->name('corte.movimiento');
Route::post('/corte/generar', [CorteController::class, 'generarCorte'])->name('corte.generar');
Route::post('/corte/iniciar-fuera', [CorteController::class, 'iniciarCorteFuera'])->name('corte.iniciar-fuera');
Route::post('/corte/historial', [CorteController::class, 'historial'])->name('corte.historial');
Route::get('/corte/exportar', [CorteController::class, 'exportarPDF'])->name('corte.exportar');
Route::get('/corte/{corte}/pdf', [CorteController::class, 'exportarPDF'])->name('corte.pdf');
Route::get('/ventas/{id}/detalle', [VentaController::class, 'verDetalle'])->name('ventas.detalle');
Route::get('/corte', [CorteController::class, 'index'])->name('corte.index');
Route::get('/corte/historico/vista', [CorteController::class, 'vistaHistorico'])->name('corte.historico.vista');
Route::get('/corte/historico/pdf-test', [App\Http\Controllers\CorteController::class, 'exportarHistoricoPDF']);
Route::get('/historico-cortes/pdf', [App\Http\Controllers\CorteController::class, 'exportarHistoricoCortesPDF'])->name('historico.cortes.pdf');

// RUTAS DE INVENTARIO (Organizadas correctamente)
    
    // Rutas básicas del resource
    Route::resource('inventario', InventarioController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    
    // Rutas adicionales
    Route::match(['post', 'patch'], '/inventario/{id}/actualizar-existencias', [InventarioController::class, 'actualizarExistencias'])
        ->name('inventario.actualizar-existencias');
    Route::patch('/inventario/{id}/activate', [InventarioController::class, 'activate'])
        ->name('inventario.activate');
    Route::patch('/inventario/{id}/deactivate', [InventarioController::class, 'deactivate'])
        ->name('inventario.deactivate');
    Route::post('/inventario/{id}/update-stock', [InventarioController::class, 'updateStock'])
        ->name('inventario.update-stock');

    // Rutas exclusivas para admin (crear/editar/eliminar)
    Route::middleware([CheckAdmin::class])->group(function () {
        Route::get('/inventario/create', [InventarioController::class, 'create'])->name('inventario.create');
        Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
        Route::get('/inventario/{inventario}/edit', [InventarioController::class, 'edit'])->name('inventario.edit');
        Route::put('/inventario/{inventario}', [InventarioController::class, 'update'])->name('inventario.update');
        Route::delete('/inventario/{inventario}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
    });