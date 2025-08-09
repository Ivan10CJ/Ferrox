@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-ferreteria-blue">CORTE DE CAJA - {{ now()->format('d/m/Y') }}</h1>
        <div class="flex space-x-2">
            @if($corte->fecha_fin === null)
            <button id="btn-generar-corte" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-900 transition font-semibold shadow">
                <i class="fas fa-lock mr-2"></i>Cerrar Corte
            </button>
            <button id="btn-corte-fuera" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-900 transition font-semibold shadow">
                <i class="fas fa-lock mr-2"></i>Corte Fuera
            </button>
            @endif
            <a href="{{ route('corte.pdf', $corte->id) }}" class="bg-[#0a2a4d] text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition font-semibold shadow" target="_blank">
                <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
            </a>
            <button id="btn-historico-nuevo" class="bg-[#0a2a4d] text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition font-semibold shadow ml-2">
    <i class="fas fa-history mr-2"></i>Histórico
</button>
        </div>
    </div>

    <!-- Resumen Diario -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total en Caja -->
        <div class="bg-[#0a2a4d] text-white p-4 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold">Total en Caja</h3>
                    @php
                        $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
                        $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
                        $totalVentasDia = $ventas->sum('total');
                        $totalCaja = $totalVentasDia + $totalIngresos - $totalEgresos;
                    @endphp
                    <p class="text-2xl font-bold mt-2" data-total-caja>${{ number_format($totalCaja, 2) }}</p>
                    <span class="text-xs text-gray-200">(Ventas + Ingresos - Egresos)</span>
                </div>
                <i class="fas fa-cash-register text-2xl opacity-70"></i>
            </div>
        </div>
        
        <!-- Total Costos -->
        <div class="bg-[#0a2a4d] text-white p-4 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold">Total Costos</h3>
                    <p class="text-2xl font-bold mt-2" data-total-costos>${{ number_format($corte->total_costos, 2) }}</p>
                </div>
                <i class="fas fa-receipt text-2xl opacity-70"></i>
            </div>
        </div>
        
        <!-- Total Ventas -->
        <div class="bg-[#8F001A] text-white p-4 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold">Total Ventas</h3>
                    @php 
                        $totalVentasDia = $ventas->sum('total');
                    @endphp
                    <p class="text-2xl font-bold mt-2" data-total-ventas>${{ number_format($totalVentasDia, 2) }}</p>
                    <span class="text-xs text-gray-200">(Ventas del día)</span>
                </div>
                <i class="fas fa-shopping-cart text-2xl opacity-70"></i>
            </div>
        </div>
        
        <!-- Total Ganancias -->
        <div class="bg-[#8F001A] text-white p-4 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold">Total Ganancias</h3>
                    <p class="text-2xl font-bold mt-2" data-total-ganancias>${{ number_format($corte->total_ganancias, 2) }}</p>
                </div>
                <i class="fas fa-chart-line text-2xl opacity-70"></i>
            </div>
        </div>
    </div>

    <!-- Movimientos de Caja -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-ferreteria-blue">MOVIMIENTOS DE CAJA</h2>
            <div class="text-sm text-gray-500">
                <span id="movimientos-count">{{ $movimientos->count() }}</span> registros
            </div>
        </div>
        
        @if($corte->fecha_fin === null)
        <div class="mb-4">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Monto</label>
                    <input type="number" id="monto-movimiento" placeholder="0.00" step="0.01" min="0.01" 
                           class="w-full border border-ferreteria-light-gray rounded-lg p-2 focus:ring-2 focus:ring-ferreteria-blue">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Concepto</label>
                    <input type="text" id="concepto-movimiento" placeholder="Descripción del movimiento"
                           class="w-full border border-ferreteria-light-gray rounded-lg p-2 focus:ring-2 focus:ring-ferreteria-blue">
                </div>
                <div class="flex items-end gap-2">
                    <button id="btn-ingreso" 
                            class="bg-[#0a2a4d] text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition flex items-center h-[42px] font-semibold shadow">
                        <i class="fas fa-plus-circle mr-2"></i>Ingreso
                    </button>
                    <button id="btn-egreso" 
                            class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-900 transition flex items-center h-[42px] font-semibold shadow">
                        <i class="fas fa-minus-circle mr-2"></i>Egreso
                    </button>
                </div>
            </div>
            <p id="movimiento-error" class="text-red-500 text-sm mt-1 hidden"></p>
        </div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-[#0a2a4d] text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">ID</th>
                        <th class="py-3 px-4 text-left">Tipo</th>
                        <th class="py-3 px-4 text-left">Concepto</th>
                        <th class="py-3 px-4 text-right">Monto</th>
                        <th class="py-3 px-4 text-left">Fecha/Hora</th>
                        <th class="py-3 px-4 text-left">Responsable</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ferreteria-light-gray" id="lista-movimientos">
                    @forelse($movimientos as $movimiento)
                    <tr>
                        <td class="py-2 px-4">MC-{{ $movimiento->id }}</td>
                        <td class="py-2 px-4">
                            @if($movimiento->tipo === 'ingreso')
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs border border-green-300">
                                    Ingreso
                                </span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs border border-red-300 underline underline-offset-2 decoration-2 decoration-red-600">
                                    Egreso
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-4">{{ $movimiento->concepto }}</td>
                        <td class="py-2 px-4 text-right font-medium {{ $movimiento->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $movimiento->tipo === 'ingreso' ? '+' : '-' }} ${{ number_format($movimiento->monto, 2) }}
                        </td>
                        <td class="py-2 px-4">{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-4">{{ $movimiento->usuario->nombre_completo }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                            No hay movimientos registrados hoy
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold text-ferreteria-blue mb-4">VENTAS DEL DÍA ({{ $ventas->count() }})</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-[#0a2a4d] text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Ticket</th>
                        <th class="py-3 px-4 text-left">Responsable</th>
                        <th class="py-3 px-4 text-left">Fecha</th>
                        <th class="py-3 px-4 text-left">Hora</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-right">Ganancia</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ferreteria-light-gray">
                    @forelse($ventas as $venta)
                    <tr>
                        <td class="py-2 px-4">V-{{ $venta->id }}</td>
                        <td class="py-2 px-4">{{ $venta->usuario->nombre_completo }}</td>
                        <td class="py-2 px-4">{{ $venta->fecha->format('d/m/Y') }}</td>
                        <td class="py-2 px-4">{{ $venta->fecha->format('H:i') }}</td>
                        <td class="py-2 px-4 text-right font-medium">${{ number_format($venta->total, 2) }}</td>
                        <td class="py-2 px-4 text-right font-medium text-green-600">${{ number_format($venta->ganancia, 2) }}</td>
                        <td class="py-2 px-4 text-center">
                            <button class="btn-ver-venta text-ferreteria-blue hover:text-ferreteria-red p-1 rounded-full hover:bg-gray-100" 
                                    data-venta-id="{{ $venta->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                            No hay ventas registradas hoy
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detalle Venta -->
<div id="modal-venta" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-ferreteria-blue">DETALLE DE VENTA</h3>
            <button onclick="cerrarModal('modal-venta')" class="text-ferreteria-red hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modal-venta-content">
            <!-- Cargado por AJAX -->
        </div>
    </div>
</div>

<!-- Modal Cerrar Corte -->
<div id="modal-cerrar-corte" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#0a2a4d]">CERRAR CORTE DE CAJA</h3>
            <button onclick="cerrarModal('modal-cerrar-corte')" class="text-[#8F001A] hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="form-cerrar-corte" action="{{ route('corte.generar') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Efectivo Final en Caja</label>
                <input type="number" name="efectivo_final" step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]">
            </div>
            
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Diferencia:</span>
                <div class="flex items-center">
                    <span id="leyenda-diferencia" class="text-sm font-medium mr-2"></span>
                    <span id="diferencia-efectivo" class="text-sm font-bold"></span>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="3" 
                          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]"></textarea>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" id="confirmar-cerrar" name="confirmacion" class="mr-2">
                <label for="confirmar-cerrar" class="text-sm text-gray-700">Confirmo que deseo cerrar el corte de caja</label>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="cerrarModal('modal-cerrar-corte')" 
                        class="bg-gray-200 text-[#0a2a4d] px-4 py-2 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-800 disabled:opacity-50" 
                        id="btn-submit-cerrar" disabled>
                    <i class="fas fa-lock mr-2"></i>Cerrar Corte
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Corte Fuera -->
<div id="modal-corte-fuera" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#0a2a4d]">CORTE FUERA DE TIEMPO</h3>
            <button onclick="cerrarModal('modal-corte-fuera')" class="text-[#8F001A] hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="form-corte-fuera" action="{{ route('corte.iniciar-fuera') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Selecciona un día pendiente</label>
                <select name="fecha" id="dias-sin-corte" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]">
                    <option value="">Cargando días...</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Efectivo Inicial</label>
                <input type="number" name="efectivo_inicial" step="0.01" min="0" 
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]">
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="cerrarModal('modal-corte-fuera')" 
                        class="bg-gray-200 text-[#0a2a4d] px-4 py-2 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-800">
                    <i class="fas fa-lock mr-2"></i>Generar Corte
                </button>
            </div>
        </form>
        <div id="corte-fuera-error" class="text-red-600 text-sm mt-2 hidden"></div>
    </div>
</div>

<!-- Modal Movimiento -->
<div id="modal-movimiento" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-ferreteria-blue" id="titulo-movimiento">REGISTRAR INGRESO</h3>
            <button onclick="cerrarModal('modal-movimiento')" class="text-ferreteria-red hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="form-movimiento-modal">
            @csrf
            <input type="hidden" id="tipo-movimiento" name="tipo">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                <input type="number" id="monto-modal" name="monto" step="0.01" min="0.01" 
                       class="w-full border border-ferreteria-light-gray rounded-lg p-2 focus:ring-2 focus:ring-ferreteria-blue">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                <input type="text" id="concepto-modal" name="concepto" 
                       class="w-full border border-ferreteria-light-gray rounded-lg p-2 focus:ring-2 focus:ring-ferreteria-blue">
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" id="confirmar-movimiento" name="confirmacion" class="mr-2">
                <label for="confirmar-movimiento" class="text-sm text-gray-700">Confirmo que deseo registrar este movimiento</label>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="cerrarModal('modal-movimiento')" 
                        class="bg-ferreteria-light-gray text-ferreteria-blue px-4 py-2 rounded-lg hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit" class="bg-ferreteria-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 disabled:opacity-50" 
        id="btn-submit-movimiento" disabled style="background-color: #0a2a4d;">
    <i class="fas fa-check mr-2"></i>Confirmar
</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Histórico -->
<div id="modal-historico" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-ferreteria-blue">HISTÓRICO DE CORTES</h3>
            <button onclick="cerrarModal('modal-historico')" class="text-ferreteria-red hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Filtros -->
        <form id="form-filtro-historico" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-[#0a2a4d] text-white px-4 py-2 rounded-lg hover:bg-blue-800 h-[42px]">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
            <div class="flex items-end justify-end">
                <button type="button" id="btn-exportar-historico" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-800 h-[42px]">
                    <i class="fas fa-file-pdf mr-2"></i>Exportar
                </button>
            </div>
        </form>
        
        <!-- Contenido dinámico -->
        <div id="historico-content">
            <!-- Cargado por AJAX -->
        </div>
    </div>
</div>

<!-- Botón para abrir el nuevo histórico -->
<button id="btn-historico-nuevo" class="bg-[#0a2a4d] text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition font-semibold shadow ml-2">
    <i class="fas fa-history mr-2"></i>Histórico (Nuevo)
</button>

<!-- Modal Histórico Nuevo -->
<div id="modal-historico-nuevo" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-5xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#0a2a4d]">HISTÓRICO DE CORTES</h3>
            <button onclick="document.getElementById('modal-historico-nuevo').classList.add('hidden')" class="text-[#8F001A] hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="form-filtro-historico-nuevo" class="flex flex-col md:flex-row gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0a2a4d]">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-[#0a2a4d] text-white px-4 py-2 rounded-lg hover:bg-blue-800 h-[42px]">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
            <div class="flex items-end justify-end">
                <button type="button" id="btn-exportar-historico-nuevo" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-800 h-[42px]">
                    <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                </button>
            </div>
        </form>
        <div id="historico-nuevo-content">
            <!-- Aquí se cargará el HTML del histórico -->
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
// Función para cerrar modales
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Función para mostrar mensajes Toast
function mostrarToast(mensaje, tipo = 'success') {
    const colores = {
        success: '#4CAF50',
        error: '#F44336',
        warning: '#FF9800',
        info: '#2196F3'
    };
    
    Toastify({
        text: mensaje,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: colores[tipo] || colores.info,
    }).showToast();
}

// Función para formatear moneda
function formatCurrency(value) {
    return `$${parseFloat(value || 0).toFixed(2)}`;
}

// Función para mostrar loader
function mostrarLoader(contenedor) {
    contenedor.innerHTML = `
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-ferreteria-blue"></div>
        </div>
    `;
}

// Controlador de Movimientos (Versión Corregida)
class MovimientosController {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
        // Registrar movimientos desde modal
        document.getElementById('form-movimiento-modal')?.addEventListener('submit', (e) => this.handleMovimientoSubmit(e));
        
        // Botones de ingreso/egreso
        document.getElementById('btn-ingreso')?.addEventListener('click', () => this.openMovimientoModal('ingreso'));
        document.getElementById('btn-egreso')?.addEventListener('click', () => this.openMovimientoModal('egreso'));
        
        // Filtrado de movimientos
        document.getElementById('monto-movimiento')?.addEventListener('input', () => this.filtrarMovimientos());
        document.getElementById('concepto-movimiento')?.addEventListener('input', () => this.filtrarMovimientos());
        
        // Escuchar cambios en el checkbox de confirmación
        document.getElementById('confirmar-movimiento')?.addEventListener('change', (e) => {
            const submitBtn = document.getElementById('btn-submit-movimiento');
            if (submitBtn) {
                submitBtn.disabled = !e.target.checked;
            }
        });
    }

    async handleMovimientoSubmit(e) {
        e.preventDefault();
        
        try {
            const formData = this.validateMovimientoForm();
            const response = await this.sendMovimientoData(formData);
            this.updateUI(response);
            this.resetForm();
            
            mostrarToast('Movimiento registrado correctamente');
            
        } catch (error) {
            console.error('Error:', error);
            this.showError(error.message);
        }
    }

    validateMovimientoForm() {
        const tipo = document.getElementById('tipo-movimiento').value;
        const montoInput = document.getElementById('monto-modal');
        const conceptoInput = document.getElementById('concepto-modal');
        const confirmacion = document.getElementById('confirmar-movimiento').checked;
        
        // Validaciones
        if (!tipo) throw new Error('No se ha especificado el tipo de movimiento');
        
        const monto = parseFloat(montoInput.value);
        if (isNaN(monto)) throw new Error('El monto debe ser un número válido');
        if (monto <= 0) throw new Error('El monto debe ser mayor que cero');
        
        const concepto = conceptoInput.value.trim();
        if (!concepto) throw new Error('Debe ingresar un concepto');
        if (!confirmacion) throw new Error('Debe confirmar el movimiento');
        
        return {
            tipo,
            monto: monto.toFixed(2),
            concepto,
            _token: '{{ csrf_token() }}'
        };
    }

    async sendMovimientoData(formData) {
        const response = await fetch("{{ route('corte.movimiento') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error en el servidor');
        }

        return await response.json();
    }

    updateUI(responseData) {
        // Actualizar lista de movimientos
        const movimiento = responseData.movimiento;
        const newRow = `
            <tr>
                <td class="py-2 px-4">MC-${movimiento.id}</td>
                <td class="py-2 px-4">
                    <span class="${movimiento.tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} px-2 py-1 rounded-full text-xs">
                        ${movimiento.tipo === 'ingreso' ? 'Ingreso' : 'Egreso'}
                    </span>
                </td>
                <td class="py-2 px-4">${movimiento.concepto}</td>
                <td class="py-2 px-4 text-right font-medium ${movimiento.tipo === 'ingreso' ? 'text-green-600' : 'text-red-600'}">
                    ${movimiento.tipo === 'ingreso' ? '+' : '-'} $${parseFloat(movimiento.monto).toFixed(2)}
                </td>
                <td class="py-2 px-4">${new Date(movimiento.created_at).toLocaleDateString('es-MX')} ${new Date(movimiento.created_at).toLocaleTimeString('es-MX', {hour: '2-digit', minute:'2-digit'})}</td>
                <td class="py-2 px-4">${movimiento.usuario.nombre_completo}</td>
            </tr>
        `;
        
        const listaMovimientos = document.getElementById('lista-movimientos');
        if (listaMovimientos) {
            listaMovimientos.insertAdjacentHTML('afterbegin', newRow);
            
            // Actualizar contador
            const contador = document.getElementById('movimientos-count');
            if (contador) {
                contador.textContent = parseInt(contador.textContent) + 1;
            }
        }
        
        // Actualizar totales
        if (responseData.nuevos_totales) {            
            const totalCajaElement = document.querySelector('[data-total-caja]');
            if (totalCajaElement) {
                totalCajaElement.textContent = formatCurrency(responseData.nuevos_totales.efectivo);
            }
            
            const totalCostosElement = document.querySelector('[data-total-costos]');
            if (totalCostosElement && responseData.nuevos_totales.costos !== undefined) {
                totalCostosElement.textContent = formatCurrency(responseData.nuevos_totales.costos);
            }
            
            const totalVentasElement = document.querySelector('[data-total-ventas]');
            if (totalVentasElement && responseData.nuevos_totales.ventas !== undefined) {
                totalVentasElement.textContent = formatCurrency(responseData.nuevos_totales.ventas);
            }
            
            const totalGananciasElement = document.querySelector('[data-total-ganancias]');
            if (totalGananciasElement && responseData.nuevos_totales.ganancias !== undefined) {
                totalGananciasElement.textContent = formatCurrency(responseData.nuevos_totales.ganancias);
            }
        }
    }

    resetForm() {
        const form = document.getElementById('form-movimiento-modal');
        if (form) {
            form.reset();
            document.getElementById('confirmar-movimiento').checked = false;
            document.getElementById('btn-submit-movimiento').disabled = true;
        }
        cerrarModal('modal-movimiento');
    }

    showError(message) {
        const errorElement = document.getElementById('movimiento-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            setTimeout(() => errorElement.classList.add('hidden'), 5000);
        } else {
            mostrarToast(message, 'error');
        }
    }

    openMovimientoModal(tipo) {
        document.getElementById('titulo-movimiento').textContent = tipo === 'ingreso' ? 'REGISTRAR INGRESO' : 'REGISTRAR EGRESO';
        document.getElementById('tipo-movimiento').value = tipo;
        document.getElementById('modal-movimiento').classList.remove('hidden');
        
        // Resetear el formulario al abrir
        document.getElementById('form-movimiento-modal').reset();
        document.getElementById('confirmar-movimiento').checked = false;
        document.getElementById('btn-submit-movimiento').disabled = true;
        
        document.getElementById('monto-modal').focus();
    }

    filtrarMovimientos() {
        const monto = document.getElementById('monto-movimiento').value;
        const concepto = document.getElementById('concepto-movimiento').value.toLowerCase();
        
        const filas = document.querySelectorAll('#lista-movimientos tr');
        
        filas.forEach(fila => {
            const montoFila = fila.querySelector('td:nth-child(4)').textContent.replace(/[^0-9.-]/g, '');
            const conceptoFila = fila.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            const coincideMonto = !monto || montoFila.includes(monto);
            const coincideConcepto = !concepto || conceptoFila.includes(concepto);
            
            fila.style.display = (coincideMonto && coincideConcepto) ? '' : 'none';
        });
    }
}

// Controlador de Ventas
class VentasController {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
        // Botones de detalle de venta
        document.querySelectorAll('.btn-ver-venta').forEach(btn => {
            btn.addEventListener('click', () => this.mostrarDetalleVenta(btn));
        });
    }

    async mostrarDetalleVenta(btn) {
    const ventaId = btn.getAttribute('data-venta-id');
    const modalContent = document.getElementById('modal-venta-content');
    const modal = document.getElementById('modal-venta');
    
    // Mostrar loader
    mostrarLoader(modalContent);
    modal.classList.remove('hidden');
    
    try {
        const response = await fetch(`/ventas/${ventaId}/detalle`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Error ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.html) {
            modalContent.innerHTML = data.html;
        } else {
            throw new Error(data.message || 'Formato de respuesta inválido');
        }
    } catch (error) {
        console.error('Error al cargar detalle:', error);
        modalContent.innerHTML = `
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                <p class="font-bold">Error</p>
                <p>${error.message}</p>
                <p class="text-sm mt-2">Venta ID: ${ventaId}</p>
            </div>
        `;
    }
}
}

// Controlador de Histórico
class HistoricoController {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
        // Botón de abrir histórico
        document.getElementById('btn-abrir-historico')?.addEventListener('click', () => this.cargarHistoricoInicial());
        
        // Formulario de filtrado
        document.getElementById('form-filtro-historico')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.cargarHistorico();
        });
        
        // Botón de exportar histórico
        document.getElementById('btn-exportar-historico')?.addEventListener('click', () => this.exportarHistorico());
    }

    async cargarHistoricoInicial() {
        const modal = document.getElementById('modal-historico');
        const content = document.getElementById('historico-content');
        
        mostrarLoader(content);
        modal.classList.remove('hidden');
        
        // Establecer fechas por defecto (últimos 7 días)
        const fechaFin = new Date();
        const fechaInicio = new Date();
        fechaInicio.setDate(fechaInicio.getDate() - 7);
        
        const form = document.getElementById('form-filtro-historico');
        form.elements['fecha_inicio'].value = fechaInicio.toISOString().split('T')[0];
        form.elements['fecha_fin'].value = fechaFin.toISOString().split('T')[0];
        
        await this.cargarHistorico();
    }

    async cargarHistorico() {
        const content = document.getElementById('historico-content');
        const form = document.getElementById('form-filtro-historico');
        
        // Validar fechas
        const fechaInicio = form.elements['fecha_inicio'].value;
        const fechaFin = form.elements['fecha_fin'].value;
        
        if (!fechaInicio || !fechaFin) {
            content.innerHTML = `
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                    <p class="font-bold">Advertencia</p>
                    <p>Por favor seleccione ambas fechas</p>
                </div>
            `;
            return;
        }
        
        mostrarLoader(content);
        
        try {
            const formData = new FormData(form);
            const response = await fetch("{{ route('corte.historial') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const html = await response.text();
            content.innerHTML = html;
        } catch (error) {
            console.error('Error al cargar histórico:', error);
            content.innerHTML = `
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                    <p class="font-bold">Error</p>
                    <p>No se pudo cargar el histórico: ${error.message}</p>
                </div>
            `;
        }
    }

    exportarHistorico() {
        const form = document.getElementById('form-filtro-historico');
        const fechaInicio = form.elements['fecha_inicio'].value;
        const fechaFin = form.elements['fecha_fin'].value;
        
        if (!fechaInicio || !fechaFin) {
            mostrarToast('Por favor seleccione un rango de fechas primero', 'warning');
            return;
        }
        
        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        });
        
        // window.open("{{ route('corte.exportar') }}?" + params.toString(), '_blank');
        mostrarToast('Para exportar un PDF, haz clic en el ícono de PDF de cada corte en el histórico.', 'info');
    }
}

// Controlador de Cierre de Corte
class CorteController {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
        // Botón de generar corte normal
        document.getElementById('btn-generar-corte')?.addEventListener('click', () => this.prepararCierreCorte());
        
        // Botón de corte fuera
        document.getElementById('btn-corte-fuera')?.addEventListener('click', () => this.mostrarModalCorteFuera());
        
        // Formulario de cierre normal
        document.getElementById('form-cerrar-corte')?.addEventListener('submit', (e) => this.cerrarCorte(e));
        
        // Formulario de corte fuera
        // document.getElementById('form-corte-fuera')?.addEventListener('submit', (e) => this.procesarCorteFuera(e)); // This line is removed as per the new_code
        
        // Validación de confirmación para cerrar corte
        document.getElementById('confirmar-cerrar')?.addEventListener('change', (e) => {
            document.getElementById('btn-submit-cerrar').disabled = !e.target.checked;
        });
        
        // Validación de efectivo final
        const efectivoFinalInput = document.querySelector('input[name="efectivo_final"]');
        if (efectivoFinalInput) {
            efectivoFinalInput.addEventListener('change', () => this.calcularDiferencia());
        }
    }

    prepararCierreCorte() {
        // Obtener los totales actuales del DOM
        const totalCaja = parseFloat(document.querySelector('[data-total-caja]').textContent.replace(/[^0-9.-]/g, ''));
        
        // El totalCaja ya incluye las ventas del día, no necesitamos sumarlas de nuevo
        const efectivoEsperado = totalCaja;
        
        // Establecer el valor en el input
        const efectivoFinalInput = document.querySelector('input[name="efectivo_final"]');
        efectivoFinalInput.value = efectivoEsperado.toFixed(2);
        efectivoFinalInput.dataset.expected = efectivoEsperado.toFixed(2);
        
        // Calcular diferencia inicial
        this.calcularDiferencia();
        
        // Mostrar el modal
        document.getElementById('modal-cerrar-corte').classList.remove('hidden');
    }

    calcularDiferencia() {
        const efectivoFinalInput = document.querySelector('input[name="efectivo_final"]');
        if (!efectivoFinalInput || !efectivoFinalInput.dataset.expected) return;
        
        const efectivoEsperado = parseFloat(efectivoFinalInput.dataset.expected);
        const efectivoReportado = parseFloat(efectivoFinalInput.value) || 0;
        const diferencia = efectivoReportado - efectivoEsperado;
        
        const diferenciaElement = document.getElementById('diferencia-efectivo');
        const leyendaElement = document.getElementById('leyenda-diferencia');
        
        if (diferenciaElement) {
            diferenciaElement.textContent = formatCurrency(Math.abs(diferencia));
            diferenciaElement.className = diferencia >= 0 ? 'text-green-600' : 'text-red-600';
        }
        
        if (leyendaElement) {
            if (diferencia > 0) {
                leyendaElement.textContent = 'Sobrante';
                leyendaElement.className = 'text-green-600';
            } else if (diferencia < 0) {
                leyendaElement.textContent = 'Faltante';
                leyendaElement.className = 'text-red-600';
            } else {
                leyendaElement.textContent = 'Correcto';
                leyendaElement.className = 'text-blue-600';
            }
        }
    }

    async cerrarCorte(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Deshabilitar botón para evitar múltiples envíos
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            let data;
            try {
                data = await response.json();
            } catch (err) {
                // Si la respuesta no es JSON, recarga la página (fallback seguro)
                window.location.reload();
                return;
            }
            if (data.success) {
                mostrarToast('Corte cerrado correctamente');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Error al cerrar el corte');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarToast('Error al cerrar corte: ' + error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i>Cerrar Corte';
        }
    }

    mostrarModalCorteFuera() {
        document.getElementById('modal-corte-fuera').classList.remove('hidden');
    }

    async procesarCorteFuera(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Validar fechas
        const fecha = form.elements['fecha'].value;
        const hoy = new Date().toISOString().split('T')[0];
        
        if (fecha > hoy) {
            mostrarToast('No puede crear un corte para una fecha futura', 'warning');
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch("{{ route('corte.iniciar-fuera') }}", {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarToast('Corte fuera de tiempo creado correctamente');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Error al crear corte fuera');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarToast('Error: ' + error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i>Generar Corte';
        }
    }
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new MovimientosController();
    new VentasController();
    new HistoricoController();
    new CorteController();
    
    // Inicializar datepickers (requiere tener flatpickr o similar)
    const fechaInputs = document.querySelectorAll('input[type="date"]');
    fechaInputs.forEach(input => {
        input.max = new Date().toISOString().split('T')[0]; // No permitir fechas futuras
    });
});

// Agregar lógica para cargar días sin corte y manejar el formulario de corte fuera
function cargarDiasSinCorte() {
    const select = document.getElementById('dias-sin-corte');
    select.innerHTML = '<option value="">Cargando días...</option>';
    fetch('/corte/dias-sin-corte')
        .then(res => res.json())
        .then(data => {
            if (data.dias && data.dias.length > 0) {
                select.innerHTML = '<option value="">Selecciona un día</option>';
                data.dias.forEach(fecha => {
                    select.innerHTML += `<option value="${fecha}">${fecha.split('-').reverse().join('/')}</option>`;
                });
            } else {
                select.innerHTML = '<option value="">No hay días pendientes</option>';
            }
        });
}

document.getElementById('btn-corte-fuera')?.addEventListener('click', cargarDiasSinCorte);

document.getElementById('form-corte-fuera')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const errorDiv = document.getElementById('corte-fuera-error');
    errorDiv.classList.add('hidden');
    errorDiv.textContent = '';
    const data = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: data
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            mostrarToast('Corte fuera de tiempo creado correctamente');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            errorDiv.textContent = resp.message || 'Error al crear corte fuera';
            errorDiv.classList.remove('hidden');
        }
    })
    .catch(() => {
        errorDiv.textContent = 'Error al crear corte fuera';
        errorDiv.classList.remove('hidden');
    });
});

// Botón para exportar histórico PDF
document.addEventListener('DOMContentLoaded', () => {
    const btnExportarHistorico = document.getElementById('btn-exportar-historico');
    if (btnExportarHistorico) {
        btnExportarHistorico.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('form-filtro-historico');
            if (!form) return;
            const fechaInicio = form.elements['fecha_inicio'].value;
            const fechaFin = form.elements['fecha_fin'].value;
            if (!fechaInicio || !fechaFin) {
                mostrarToast('Selecciona un rango de fechas primero', 'warning');
                return;
            }
            const params = new URLSearchParams({
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            });
            window.open('/historico-cortes/pdf?' + params.toString(), '_blank');
        });
    }
});

// Abrir el modal
const btnHistoricoNuevo = document.getElementById('btn-historico-nuevo');
if (btnHistoricoNuevo) {
    btnHistoricoNuevo.addEventListener('click', () => {
        document.getElementById('modal-historico-nuevo').classList.remove('hidden');
    });
}
// Buscar histórico
const formHistoricoNuevo = document.getElementById('form-filtro-historico-nuevo');
if (formHistoricoNuevo) {
    formHistoricoNuevo.addEventListener('submit', async function(e) {
        e.preventDefault();
        const content = document.getElementById('historico-nuevo-content');
        content.innerHTML = '<div class="py-8 text-center text-gray-500">Cargando...</div>';
        const formData = new FormData(formHistoricoNuevo);
        const params = new URLSearchParams(formData);
        try {
            const response = await fetch('/corte/historico/vista?' + params.toString());
            if (!response.ok) throw new Error('Error al cargar histórico');
            const html = await response.text();
            content.innerHTML = html;
        } catch (err) {
            content.innerHTML = `<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4'>Error: ${err.message}</div>`;
        }
    });
}
// Exportar PDF
const btnExportarHistoricoNuevo = document.getElementById('btn-exportar-historico-nuevo');
if (btnExportarHistoricoNuevo) {
    btnExportarHistoricoNuevo.addEventListener('click', function() {
        const form = document.getElementById('form-filtro-historico-nuevo');
        if (!form) return;
        const fechaInicio = form.elements['fecha_inicio'].value;
        const fechaFin = form.elements['fecha_fin'].value;
        if (!fechaInicio || !fechaFin) {
            mostrarToast('Selecciona un rango de fechas primero', 'warning');
            return;
        }
        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        });
        window.open('/historico-cortes/pdf?' + params.toString(), '_blank');
    });
}
</script>
@endpush
@endsection