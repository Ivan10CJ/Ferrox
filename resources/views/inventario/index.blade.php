@extends('layouts.app')

@section('content')
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container">
    <h2>Módulo Inventario</h2>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="{{ route('inventario.index') }}" class="d-flex mb-3" style="gap: 10px;">
        <input type="text" name="buscar_codigo" class="form-control" placeholder="Buscar por código" value="{{ request('buscar_codigo') }}" pattern="^[A-Za-z0-9]+$" title="Solo letras y números">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Buscar
        </button>
    </form>

    <!-- Botón para abrir modal -->
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

    <!-- Tabla -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Tipo Venta</th>
                <th>Unidad</th>
                <th>Metros</th>
                <th>Precio Compra Unidad</th>
                <th>Precio Unidad</th>
                <th>Precio Metro</th>
                <th>Precio Compra Metro</th>
                <th>Metros por Unidad</th>
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
                <td>{{ $item->unidad }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->metros ?? '0') }}</td>
                <td>${{ number_format($item->precio_compra_unidad, 2) }}</td>
                <td>${{ number_format($item->precio_unidad, 2) }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->precio_metro ? '$'.number_format($item->precio_metro, 2) : '$0.00') }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->precio_compra_metro ? '$'.number_format($item->precio_compra_metro, 2) : '$0.00') }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->metros_unidad ?? '0') }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $item->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $item->id }}">
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

                    <!-- Campos específicos para Metro -->
                    <div class="col-md-4 campo-metro">
                        <label>Metros</label>
                        <input type="number" step="0.01" min="0" name="metros" class="form-control" value="{{ $item->metros }}" {{ $item->tipo_venta == 'pieza' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Precio por metro</label>
                        <input type="number" step="0.01" min="0" name="precio_metro" class="form-control" value="{{ $item->precio_metro }}" {{ $item->tipo_venta == 'pieza' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Precio compra metro</label>
                        <input type="number" step="0.01" min="0" name="precio_compra_metro" class="form-control" value="{{ $item->precio_compra_metro }}" {{ $item->tipo_venta == 'pieza' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Metros por unidad</label>
                        <input type="number" step="0.01" min="0" name="metros_unidad" class="form-control" value="{{ $item->metros_unidad }}" {{ $item->tipo_venta == 'pieza' ? 'disabled' : '' }}>
                    </div>

                    <!-- Campos comunes -->
                    <div class="col-md-4">
                        <label>Precio compra unidad</label>
                        <input type="number" step="0.01" min="0" name="precio_compra_unidad" class="form-control" value="{{ $item->precio_compra_unidad }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Precio por unidad</label>
                        <input type="number" step="0.01" min="0" name="precio_unidad" class="form-control" value="{{ $item->precio_unidad }}" required>
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

                    <!-- Campos específicos para Metro -->
                    <div class="col-md-4 campo-metro">
                        <label>Metros</label>
                        <input type="number" step="0.01" min="0" name="metros" class="form-control" value="{{ old('metros') }}" disabled>
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Precio por metro</label>
                        <input type="number" step="0.01" min="0" name="precio_metro" class="form-control" value="{{ old('precio_metro') }}" disabled>
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Precio compra metro</label>
                        <input type="number" step="0.01" min="0" name="precio_compra_metro" class="form-control" value="{{ old('precio_compra_metro') }}" disabled>
                    </div>
                    <div class="col-md-4 campo-metro">
                        <label>Metros por unidad</label>
                        <input type="number" step="0.01" min="0" name="metros_unidad" class="form-control" value="{{ old('metros_unidad') }}" disabled>
                    </div>

                    <!-- Campos comunes -->
                    <div class="col-md-4">
                        <label>Precio compra unidad</label>
                        <input type="number" step="0.01" min="0" name="precio_compra_unidad" class="form-control" value="{{ old('precio_compra_unidad') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Precio por unidad</label>
                        <input type="number" step="0.01" min="0" name="precio_unidad" class="form-control" value="{{ old('precio_unidad') }}" required>
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

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function toggleCamposMetro(tipoVentaSelect) {
            const isMetro = tipoVentaSelect.value === 'metro';
            const modalBody = tipoVentaSelect.closest('.modal-body');
            modalBody.querySelectorAll('.campo-metro').forEach(campo => {
                const input = campo.querySelector('input');
                campo.style.display = isMetro ? 'block' : 'none';
                input.disabled = !isMetro;
                if (!isMetro) {
                    input.value = '';
                }
            });
        }

        const selectAgregar = document.querySelector('#modalAgregar .tipo_venta_modal');
        if (selectAgregar) {
            selectAgregar.addEventListener('change', function() {
                toggleCamposMetro(this);
            });
        }

        document.querySelectorAll('.tipo_venta_modal').forEach(select => {
            select.addEventListener('change', function() {
                toggleCamposMetro(this);
            });
            toggleCamposMetro(select);
        });

        @if ($errors->any() && session()->has('from_modal'))
        var modal = new bootstrap.Modal(document.getElementById('modalAgregar'));
        modal.show();
        let errores = @json($errors->all());
        let erroresDiv = document.getElementById('erroresAgregar');
        erroresDiv.classList.remove('d-none');
        erroresDiv.innerHTML = '<ul>' + errores.map(error => `<li>${error}</li>`).join('') + '</ul>';
        @endif

        // Botón Eliminar
        document.querySelectorAll('.btn-eliminar').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: '¿Eliminar producto?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch(`/inventario/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(err.error || 'Error inesperado');
                                });
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Error al eliminar: ${error.message}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value && !result.value.error) {
                        Swal.fire('¡Eliminado!', 'El producto fue eliminado.', 'success')
                            .then(() => window.location.reload());
                    } else if (result.isConfirmed && result.value && result.value.error) {
                        Swal.fire('Error', result.value.error, 'error');
                    }
                });
            });
        });
    });
</script>
@endsection
