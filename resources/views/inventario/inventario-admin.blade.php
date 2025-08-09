@extends('layouts.app')

@section('content')
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container">
    <h2>Módulo Inventario</h2>
    <h4>Rol del usuario: ADMINISTRADOR</h4>
    
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

    <!-- Botón para agregar producto (solo admin) -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">
        <i class="fas fa-plus"></i> Agregar producto
    </button>

    <hr>

    @if ($errors->any() && !session()->has('from_modal'))
        <div class="alert alert-danger">
            <strong>Errores al procesar el formulario:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                    <!-- Botón Editar (solo admin) -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $item->id }}">
                        <i class="fas fa-edit"></i>
                    </button>

                    <!-- Botón Activar/Desactivar (solo admin) -->
                    @if($item->activo)
                        <form action="{{ route('inventario.deactivate', $item->id) }}" method="POST" style="display: inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-secondary" title="Desactivar">
                                <i class="fas fa-toggle-off"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('inventario.activate', $item->id) }}" method="POST" style="display: inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                <i class="fas fa-toggle-on"></i>
                            </button>
                        </form>
                    @endif

                    <!-- Botón Eliminar (solo admin) -->
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $item->id }}" data-codigo="{{ $item->codigo }}" data-nombre="{{ $item->nombre }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>

                    <!-- Modal editar -->
                    <div class="modal fade" id="modalEditar{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <form action="{{ route('inventario.update', $item->id) }}" method="POST" novalidate>
                                @csrf @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row g-2">
                                        <div class="col-md-4">
                                            <label>Código</label>
                                            <input type="text" name="codigo" class="form-control" value="{{ $item->codigo }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="{{ $item->nombre }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Descripción</label>
                                            <input type="text" name="descripcion" class="form-control" value="{{ $item->descripcion }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Tipo de venta</label>
                                            <select name="tipo_venta" class="form-select tipo_venta_modal" required>
                                                <option value="pieza" {{ $item->tipo_venta == 'pieza' ? 'selected' : '' }}>Pieza</option>
                                                <option value="metro" {{ $item->tipo_venta == 'metro' ? 'selected' : '' }}>Metro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Unidad</label>
                                            <input type="number" name="unidad" class="form-control" min="0" value="{{ $item->unidad }}" required>
                                        </div>
                                        <div class="col-md-4 campo-metro">
                                            <label>Metros</label>
                                            <input type="number" step="0.01" min="0" name="metros" class="form-control" value="{{ $item->metros }}">
                                        </div>
                                        <div class="col-md-4 campo-metro">
                                            <label>Precio por metro</label>
                                            <input type="number" step="0.01" min="0" name="precio_metro" class="form-control" value="{{ $item->precio_metro }}">
                                        </div>
                                        <div class="col-md-4 campo-metro">
                                            <label>Precio compra metro</label>
                                            <input type="number" step="0.01" min="0" name="precio_compra_metro" class="form-control" value="{{ $item->precio_compra_metro }}">
                                        </div>
                                        <div class="col-md-4 campo-metro">
                                            <label>Metros por unidad</label>
                                            <input type="number" step="0.01" min="0" name="metros_unidad" class="form-control" value="{{ $item->metros_unidad }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Precio compra unidad</label>
                                            <input type="number" step="0.01" min="0" name="precio_compra_unidad" class="form-control" value="{{ $item->precio_compra_unidad }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Precio por unidad</label>
                                            <input type="number" step="0.01" min="0" name="precio_unidad" class="form-control" value="{{ $item->precio_unidad }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Activo</label>
                                            <select name="activo" class="form-select">
                                                <option value="1" {{ $item->activo ? 'selected' : '' }}>Sí</option>
                                                <option value="0" {{ !$item->activo ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
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

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('inventario.store') }}" method="POST" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-2">
                    <div id="erroresAgregar" class="alert alert-danger d-none"></div>

                    <div class="col-md-4">
                        <label>Código</label>
                        <input type="text" name="codigo" class="form-control" value="{{ old('codigo') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Tipo de venta</label>
                        <select name="tipo_venta" id="tipo_venta" class="form-select tipo_venta_modal" required>
                            <option value="pieza" {{ old('tipo_venta') == 'pieza' ? 'selected' : '' }}>Pieza</option>
                            <option value="metro" {{ old('tipo_venta') == 'metro' ? 'selected' : '' }}>Metro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Unidad</label>
                        <input type="number" name="unidad" class="form-control" min="0" value="{{ old('unidad') }}" required>
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Metros</label>
                        <input type="number" step="0.01" min="0" name="metros" class="form-control" value="{{ old('metros') }}">
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Precio por metro</label>
                        <input type="number" step="0.01" min="0" name="precio_metro" class="form-control" value="{{ old('precio_metro') }}">
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Precio compra metro</label>
                        <input type="number" step="0.01" min="0" name="precio_compra_metro" class="form-control" value="{{ old('precio_compra_metro') }}">
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Metros por unidad</label>
                        <input type="number" step="0.01" min="0" name="metros_unidad" class="form-control" value="{{ old('metros_unidad') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Precio compra unidad</label>
                        <input type="number" step="0.01" min="0" name="precio_compra_unidad" class="form-control" value="{{ old('precio_compra_unidad') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Precio por unidad</label>
                        <input type="number" step="0.01" min="0" name="precio_unidad" class="form-control" value="{{ old('precio_unidad') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Activo</label>
                        <select name="activo" class="form-select">
                            <option value="1" selected>Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar producto</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Función para mostrar/ocultar campos de metro
        function toggleCamposMetro(tipoVentaSelect) {
            const isMetro = tipoVentaSelect.value === 'metro';
            const modalBody = tipoVentaSelect.closest('.modal-body');
            
            modalBody.querySelectorAll('.campo-metro').forEach(campo => {
                campo.style.display = isMetro ? 'block' : 'none';
                if (!isMetro) {
                    const input = campo.querySelector('input');
                    input.value = '';
                }
            });
        }

        // Inicializar para modal agregar
        const selectAgregar = document.querySelector('#modalAgregar .tipo_venta_modal');
        if (selectAgregar) {
            selectAgregar.addEventListener('change', function() {
                toggleCamposMetro(this);
            });
            toggleCamposMetro(selectAgregar);
        }

        // Inicializar para modales de edición
        document.querySelectorAll('.tipo_venta_modal').forEach(select => {
            select.addEventListener('change', function() {
                toggleCamposMetro(this);
            });
            toggleCamposMetro(select);
        });

        // Manejo de errores del formulario
        @if($errors->any() && session()->has('from_modal'))
            (function() {
                var modal = new bootstrap.Modal(document.getElementById('modalAgregar'));
                modal.show();
                
                var erroresDiv = document.getElementById('erroresAgregar');
                
                if (erroresDiv) {
                    erroresDiv.classList.remove('d-none');
                    erroresDiv.innerHTML = '<ul>' + 
                        @json($errors->all()).map(function(error) {
                            return '<li>' + error + '</li>';
                        }).join('') + 
                    '</ul>';
                }
            })();
        @endif

         // Botón Eliminar
        document.querySelectorAll('.btn-eliminar').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                var id = this.getAttribute('data-id');
                var codigo = this.getAttribute('data-codigo');
                var nombre = this.getAttribute('data-nombre');

                Swal.fire({
                    title: '¿Eliminar producto?',
                    html: '<div class="text-left">' +
                            '<p>Esta acción no se puede deshacer</p>' +
                            '<p><strong>Código:</strong> ' + codigo + '</p>' +
                            '<p><strong>Nombre:</strong> ' + nombre + '</p>' +
                        '</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return fetch('/inventario/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor');
                            }
                            return response.json();
                        })
                        .catch(function(error) {
                            Swal.showValidationMessage('Error al eliminar: ' + error.message);
                        });
                    },
                    allowOutsideClick: function() { return !Swal.isLoading(); }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        if (result.value && !result.value.error) {
                            Swal.fire('¡Eliminado!', 'El producto fue eliminado.', 'success')
                                .then(function() {
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire('Error', result.value && result.value.error || 'Error desconocido', 'error');
                        }
                    }
                });
            });
        });
    });
</script>
@endsection