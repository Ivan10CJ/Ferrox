@extends('layouts.app')

@section('content')
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<div class="container">
    <h2>Módulo Inventario</h2>
    <h4>Rol del usuario: EMPLEADO</h4>
    
    <!-- Formulario de búsqueda -->
    <form method="GET" action="{{ route('inventario.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="busqueda" class="form-control" 
                placeholder="Buscar por código o nombre" 
                value="{{ request('busqueda') }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="form-group mt-2">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-secondary {{ !request('estado') && !request('bajas_unidades') ? 'active' : '' }}">
                    <input type="radio" name="estado" value="" autocomplete="off" 
                        onchange="this.form.submit()" {{ !request('estado') && !request('bajas_unidades') ? 'checked' : '' }}> Todos
                </label>
                <label class="btn btn-outline-secondary {{ request('estado') === 'activos' ? 'active' : '' }}">
                    <input type="radio" name="estado" value="activos" autocomplete="off" 
                        onchange="this.form.submit()" {{ request('estado') === 'activos' ? 'checked' : '' }}> Activos
                </label>
                <label class="btn btn-outline-secondary {{ request('estado') === 'inactivos' ? 'active' : '' }}">
                    <input type="radio" name="estado" value="inactivos" autocomplete="off" 
                        onchange="this.form.submit()" {{ request('estado') === 'inactivos' ? 'checked' : '' }}> Inactivos
                </label>
                <label class="btn btn-outline-danger {{ request('bajas_unidades') ? 'active' : '' }}">
                    <input type="checkbox" name="bajas_unidades" value="1" autocomplete="off" 
                        onchange="this.form.submit()" {{ request('bajas_unidades') ? 'checked' : '' }}> Bajas unidades (<10)
                </label>
            </div>
        </div>
    </form>

    <hr>

    <!-- Tabla de inventario -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Tipo Venta</th>
                <th>Unidad</th>
                <th>Metros</th>
                <th>Precio Por Unidad</th>
                <th>Precio Por Metro</th>
                <th>Metros por Unidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventarios as $item)
            <tr>
                <td>{{ $item->codigo }}</td>
                <td>{{ $item->nombre }}</td>
                <td>{{ $item->descripcion }}</td>
                <td>{{ ucfirst($item->tipo_venta) }}</td>
                <td class="{{ $item->unidad < 10 ? 'bg-danger text-white' : '' }}">
                    {{ $item->unidad }}
                    @if($item->unidad < 10)
                        <span class="badge bg-warning ms-2">¡Bajo stock!</span>
                    @endif
                </td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->metros ?? '0') }}</td>
                <td>${{ number_format($item->precio_unidad, 2) }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->precio_metro ? '$'.number_format($item->precio_metro, 2) : '$0.00') }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->metros_unidad ?? '0') }}</td>
                <td>
                    <span class="badge bg-{{ $item->activo ? 'success' : 'danger' }}">
                        {{ $item->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <!-- Solo botón para actualizar existencias (sin editar ni eliminar) -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalActualizarExistencias{{ $item->id }}">
                        <i class="fas fa-boxes"></i> Actualizar
                    </button>

                    <!-- Modal para actualizar existencias (solo agregar, no reducir) -->
                    <div class="modal fade" id="modalActualizarExistencias{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('inventario.actualizar-existencias', $item->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Actualizar existencias</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Producto: {{ $item->nombre }}</label>
                                            <label class="form-label">Existencias actuales: {{ $item->unidad }}</label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cantidad" class="form-label">Cantidad a agregar</label>
                                            <input type="number" name="cantidad" class="form-control" min="1" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection