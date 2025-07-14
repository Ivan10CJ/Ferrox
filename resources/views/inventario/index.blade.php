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
    <button class="btn" style="background-color: #052A59; color: white;" data-bs-toggle="modal" data-bs-target="#modalAgregar">
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
                <th>Metros Sueltos</th>
                <th>Metros por Unidad</th>
                <th>Precio Unidad</th>
                <th>Precio por Metro</th>
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
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->metros !== null ? $item->metros : 'No registrado') }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->metros_unidad !== null ? $item->metros_unidad : 'No registrado') }}</td>
                <td>${{ number_format($item->precio_unidad, 2) }}</td>
                <td>{{ $item->tipo_venta === 'pieza' ? 'No aplica' : ($item->precio_metro !== null ? '$' . number_format($item->precio_metro, 2) : 'No registrado') }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $item->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $item->id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <form id="form-eliminar-{{ $item->id }}" action="{{ route('inventario.destroy', $item->id) }}" method="POST" style="display: none;">
                        @csrf @method('DELETE')
                    </form>

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
                                            <input type="text" name="codigo" class="form-control" value="{{ $item->codigo }}" pattern="^[A-Za-z0-9]+$" title="Solo letras y números" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="{{ $item->nombre }}" pattern="^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$" title="Solo letras" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Descripción</label>
                                            <input type="text" name="descripcion" class="form-control" value="{{ $item->descripcion }}" pattern="^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ.,:;()¡!¿?\"' ]*$" title="No se permiten caracteres especiales">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Tipo de venta</label>
                                            <select name="tipo_venta" class="form-select tipo_venta_modal" required>
                                                <option value="rollo" {{ $item->tipo_venta == 'rollo' ? 'selected' : '' }}>Rollo</option>
                                                <option value="kilo" {{ $item->tipo_venta == 'kilo' ? 'selected' : '' }}>Kilo</option>
                                                <option value="tubo" {{ $item->tipo_venta == 'tubo' ? 'selected' : '' }}>Tubo</option>
                                                <option value="pieza" {{ $item->tipo_venta == 'pieza' ? 'selected' : '' }}>Pieza</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Unidad</label>
                                            <input type="number" name="unidad" class="form-control" min="0" value="{{ $item->unidad }}" required>
                                        </div>
                                        <div class="col-md-4 campo-metros">
                                            <label>Metros sueltos</label>
                                            <input type="number" step="0.01" min="0" name="metros" class="form-control" value="{{ $item->metros }}">
                                        </div>
                                        <div class="col-md-4 campo-metros-unidad">
                                            <label>Metros por unidad</label>
                                            <input type="number" step="0.01" min="0" name="metros_unidad" class="form-control" value="{{ $item->metros_unidad }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Precio por unidad</label>
                                            <input type="number" step="0.01" min="0" name="precio_unidad" class="form-control" value="{{ $item->precio_unidad }}" required>
                                        </div>
                                        <div class="col-md-4 campo-precio-metro">
                                            <label>Precio por metro</label>
                                            <input type="number" step="0.01" min="0" name="precio_metro" class="form-control" value="{{ $item->precio_metro }}">
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
                    <!-- Fin Modal editar -->
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('inventario.store') }}" method="POST" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar producto al inventario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-2">
                    <div id="erroresAgregar" class="alert alert-danger d-none"></div>

                    <div class="col-md-4">
                        <label>Código</label>
                        <input type="text" name="codigo" class="form-control" placeholder="Ej. COD123" value="{{ old('codigo') }}" pattern="^[A-Za-z0-9]+$" title="Solo letras y números" required>
                    </div>
                    <div class="col-md-4">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Tornillo" value="{{ old('nombre') }}" pattern="^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$" title="Solo letras" required>
                    </div>
                    <div class="col-md-4">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Opcional" value="{{ old('descripcion') }}" pattern="^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ.,:;()¡!¿?\"' ]*$" title="No se permiten caracteres especiales">
                    </div>
                    <div class="col-md-4">
                        <label>Tipo de venta</label>
                        <select name="tipo_venta" id="tipo_venta" class="form-select" required>
                            <option value="rollo" {{ old('tipo_venta') == 'rollo' ? 'selected' : '' }}>Rollo</option>
                            <option value="kilo" {{ old('tipo_venta') == 'kilo' ? 'selected' : '' }}>Kilo</option>
                            <option value="tubo" {{ old('tipo_venta') == 'tubo' ? 'selected' : '' }}>Tubo</option>
                            <option value="pieza" {{ old('tipo_venta') == 'pieza' ? 'selected' : '' }}>Pieza</option>
                        </select>
                    </div>
                    <div class="col-md-4 campo-cantidad">
                        <label>Unidad</label>
                        <input type="number" name="unidad" class="form-control" min="0" placeholder="Ej. 5" value="{{ old('unidad') }}" required>
                    </div>
                    <div class="col-md-4 campo-metros">
                        <label>Metros sueltos</label>
                        <input type="number" step="0.01" name="metros" class="form-control" min="0" placeholder="Ej. 3.5" value="{{ old('metros') }}">
                    </div>
                    <div class="col-md-4 campo-metros-unidad">
                        <label>Metros por unidad</label>
                        <input type="number" step="0.01" name="metros_unidad" class="form-control" min="0" placeholder="Ej. 1.25" value="{{ old('metros_unidad') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Precio por unidad</label>
                        <input type="number" step="0.01" name="precio_unidad" class="form-control" min="0" placeholder="Ej. 25.00" value="{{ old('precio_unidad') }}" required>
                    </div>
                    <div class="col-md-4 campo-precio-metro">
                        <label>Precio por metro</label>
                        <input type="number" step="0.01" name="precio_metro" class="form-control" min="0" placeholder="Ej. 8.00" value="{{ old('precio_metro') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #052A59;">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAgregar = document.getElementById('tipo_venta');

        function mostrarCamposAgregar() {
            const tipo = selectAgregar.value;
            const campoMetros = document.querySelector('#modalAgregar .campo-metros');
            const campoMetrosUnidad = document.querySelector('#modalAgregar .campo-metros-unidad');
            const campoPrecioMetro = document.querySelector('#modalAgregar .campo-precio-metro');

            const mostrar = (tipo !== 'pieza');
            campoMetros.style.display = mostrar ? 'block' : 'none';
            campoMetrosUnidad.style.display = mostrar ? 'block' : 'none';
            campoPrecioMetro.style.display = mostrar ? 'block' : 'none';
        }

        selectAgregar.addEventListener('change', mostrarCamposAgregar);
        document.getElementById('modalAgregar').addEventListener('shown.bs.modal', mostrarCamposAgregar);

        @if ($errors->any() && session()->has('from_modal'))
        var modal = new bootstrap.Modal(document.getElementById('modalAgregar'));
        modal.show();

        let errores = @json($errors->all());
        let erroresDiv = document.getElementById('erroresAgregar');
        erroresDiv.classList.remove('d-none');
        erroresDiv.innerHTML = '<ul>' + errores.map(error => `<li>${error}</li>`).join('') + '</ul>';
        @endif

        document.querySelectorAll('.tipo_venta_modal').forEach(select => {
            function actualizarModalCampos() {
                const modal = select.closest('.modal-body');
                const tipo = select.value;
                const campoMetros = modal.querySelector('.campo-metros');
                const campoMetrosUnidad = modal.querySelector('.campo-metros-unidad');
                const campoPrecioMetro = modal.querySelector('.campo-precio-metro');

                const mostrar = (tipo !== 'pieza');
                campoMetros.style.display = mostrar ? 'block' : 'none';
                campoMetrosUnidad.style.display = mostrar ? 'block' : 'none';
                campoPrecioMetro.style.display = mostrar ? 'block' : 'none';
            }

            select.addEventListener('change', actualizarModalCampos);
            actualizarModalCampos();
        });

        document.querySelectorAll('.btn-eliminar').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: '¿Eliminar producto?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`form-eliminar-${id}`).submit();
                    }
                });
            });
        });

        // --- VALIDACIÓN PARA NO PERMITIR CARACTERES ESPECIALES EN INPUTS DE TEXTO ---
        function filtrarInput(event, regex) {
            const input = event.target;
            const valorFiltrado = input.value.split('').filter(char => regex.test(char)).join('');
            if (input.value !== valorFiltrado) {
                input.value = valorFiltrado;
            }
        }

        const inputsTexto = document.querySelectorAll('div.modal input[type="text"]');
        inputsTexto.forEach(input => {
            let regex;
            if (input.name === 'codigo') {
                regex = /^[A-Za-z0-9]$/;
            } else if (input.name === 'nombre') {
                regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ ]$/;
            } else if (input.name === 'descripcion') {
                regex = /^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ.,:;()¡!¿?"' ]$/;
            } else {
                regex = /^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ ]$/;
            }
            input.addEventListener('input', e => filtrarInput(e, regex));
        });
    });
</script>
@endsection
