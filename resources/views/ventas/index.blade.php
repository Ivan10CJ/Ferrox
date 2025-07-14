@extends('layouts.app')

@section('title', 'Módulo Ventas')

@section('content')
<div class="container py-4">
    <h1 class="h2 fw-bold text-white bg-primary p-3 rounded mb-4">Venta de Productos</h1>

    <!-- Buscador -->
    <div class="mb-4 d-flex">
        <input type="text" id="buscarProducto" class="form-control me-2" placeholder="Buscar por código o nombre..." autocomplete="off">
        <button class="btn btn-danger" onclick="buscar()">
            <i class="fas fa-search me-1"></i> Buscar
        </button>
    </div>
    <div id="resultadosBusqueda" class="mt-2 border rounded p-2 bg-light d-none"></div>

    <!-- Carrito -->
    <div id="carrito" class="bg-light p-4 rounded shadow mt-4">
        <h3 class="h5 fw-bold text-primary mb-3">Carrito de Compras</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr class="table-primary">
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Tipo</th>
                        <th>P. Unitario</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tablaCarrito">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No hay productos en el carrito</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <p class="fw-bold fs-5">Total: $<span id="total">0.00</span></p>
        </div>

        <div class="mt-4">
            <div class="input-group mb-2">
                <span class="input-group-text fw-bold">Monto recibido:</span>
                <input type="number" id="montoRecibido" class="form-control" min="0" step="0.01" oninput="validarVenta()">
            </div>
            <div id="cambioMensaje" class="text-end"></div>
        </div>

        <button id="btnVenta" class="btn btn-danger mt-4 w-100" onclick="realizarVenta()" disabled>
            <i class="fas fa-cash-register me-2"></i> Realizar Venta
        </button>
    </div>
</div>

<!-- Modal de Producto Seleccionado -->
<div class="modal fade" id="modalProductoSeleccionado" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalProductoLabel">Producto Seleccionado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="datosProducto"></div>
        <div class="mt-3">
            <label class="form-label fw-bold">Cantidad:</label>
            <input type="number" id="cantidad" class="form-control mb-3" min="0.01" step="0.01" value="1">
            
            <select id="tipoVenta" class="form-select mb-3 d-none"></select>

            <button class="btn btn-danger w-100 py-2" onclick="agregarProducto()">
                <i class="fas fa-cart-plus me-2"></i> Agregar al Carrito
            </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmación de Venta -->
<div class="modal fade" id="modalVentaExitosa" tabindex="-1" aria-labelledby="modalVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalVentaLabel">Venta Registrada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>La venta se registró correctamente.</p>
        <p class="fw-bold">Total: $<span id="ventaTotal"></span></p>
        <p>¿Deseas generar el ticket?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="generarTicket()">
            <i class="fas fa-receipt me-2"></i> Generar Ticket
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Verificación de Stock -->
<div class="modal fade" id="modalStockError" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Error de Stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="stockErrorMensaje"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notifications -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Notificación</strong>
            <small class="text-muted">justo ahora</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

@endsection

@push('styles')
<style>
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}
.cursor-pointer {
    cursor: pointer;
}
#resultadosBusqueda {
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endpush

@push('scripts')
<script>
let productoSeleccionado = null;
let carrito = [];
let ventaIdGenerada = null;
const modalProducto = new bootstrap.Modal(document.getElementById('modalProductoSeleccionado'));
const modalStockError = new bootstrap.Modal(document.getElementById('modalStockError'));

// Función para mostrar notificaciones toast
function mostrarToast(titulo, mensaje, tipo = 'success') {
    const toastEl = document.getElementById('liveToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-info');
    toastEl.classList.add(`bg-${tipo}`);
    
    toastTitle.innerText = titulo;
    toastMessage.innerText = mensaje;
    
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Búsqueda de productos
document.getElementById('buscarProducto').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') buscar();
});

function buscar() {
    const query = document.getElementById('buscarProducto').value.trim();
    if (query.length < 2) {
        document.getElementById('resultadosBusqueda').classList.add('d-none');
        return;
    }

    // Mostrar loading
    const resultados = document.getElementById('resultadosBusqueda');
    resultados.innerHTML = `
        <div class="text-center py-2">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <span class="ms-2">Buscando productos...</span>
        </div>`;
    resultados.classList.remove('d-none');

    fetch(`/ventas/buscar-producto?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            if (data.length === 0) {
                html = `<p class="text-muted p-2">No se encontraron productos</p>`;
            } else {
                data.forEach(p => {
                    const precioMostrar = p.precio_unidad ? `$${parseFloat(p.precio_unidad).toFixed(2)}` : '';
                    const stockMostrar = p.tipo_venta === 'rollo' || p.tipo_venta === 'tubo' 
                        ? `${p.metros} m (${p.unidad} rollos)` 
                        : `${p.unidad} unidades`;

                    html += `
                        <div onclick='seleccionarProducto(${JSON.stringify(p)})'
                             class="cursor-pointer p-2 border-bottom hover-bg-light d-flex justify-content-between align-items-center">
                             <div>
                                <strong>${p.nombre}</strong><br>
                                <small class="text-muted">${p.codigo} | ${p.tipo_venta}</small>
                             </div>
                             <div class="text-end">
                                <span class="text-primary fw-bold">${precioMostrar}</span><br>
                                <small class="text-muted">${stockMostrar}</small>
                             </div>
                        </div>`;
                });
            }
            resultados.innerHTML = html;
        })
        .catch(err => {
            resultados.innerHTML = `<p class="text-danger p-2">Error al buscar productos</p>`;
        });
}

function seleccionarProducto(data) {
    productoSeleccionado = data;
    document.getElementById('buscarProducto').value = '';
    document.getElementById('resultadosBusqueda').innerHTML = '';
    document.getElementById('resultadosBusqueda').classList.add('d-none');

    const tipoVentaSelect = document.getElementById('tipoVenta');
    tipoVentaSelect.innerHTML = '';
    
    if (data.tipo_venta === 'kilo' || data.tipo_venta === 'pieza') {
        tipoVentaSelect.classList.add('d-none');
        tipoVentaSelect.innerHTML = `<option value="unidad" selected>Unidad</option>`;
    } else {
        tipoVentaSelect.classList.remove('d-none');
        tipoVentaSelect.innerHTML = `
            <option value="unidad">Unidad ($${data.precio_unidad})</option>
            <option value="metro" selected>Metro ($${data.precio_metro})</option>`;
    }

    // Mostrar detalles del producto
    document.getElementById('datosProducto').innerHTML = `
        <p><strong>Nombre:</strong> ${data.nombre}</p>
        <p><strong>Código:</strong> ${data.codigo}</p>
        <p><strong>Tipo de venta:</strong> ${data.tipo_venta}</p>
        ${data.precio_unidad ? `<p><strong>Precio por unidad:</strong> $${parseFloat(data.precio_unidad).toFixed(2)}</p>` : ''}
        ${data.precio_metro ? `<p><strong>Precio por metro:</strong> $${parseFloat(data.precio_metro).toFixed(2)}</p>` : ''}
        <p><strong>Stock disponible:</strong> ${data.tipo_venta === 'rollo' || data.tipo_venta === 'tubo' 
            ? `${data.metros} metros (${data.unidad} unidades completas)` 
            : `${data.unidad} unidades`}</p>`;

    // Resetear cantidad
    document.getElementById('cantidad').value = 1;
    modalProducto.show();
}

function agregarProducto() {
    const cantidadInput = document.getElementById('cantidad');
    const cantidad = parseFloat(cantidadInput.value);
    const tipo = document.getElementById('tipoVenta').value;

    if (!productoSeleccionado || isNaN(cantidad) || cantidad <= 0) {
        mostrarToast('Error', 'Por favor ingrese una cantidad válida', 'danger');
        cantidadInput.focus();
        return;
    }

    // Validar stock máximo
    const stockMaximo = tipo === 'unidad' 
        ? productoSeleccionado.unidad 
        : productoSeleccionado.metros + (productoSeleccionado.unidad * productoSeleccionado.metros_unidad);
    
    if (cantidad > stockMaximo) {
        mostrarToast('Error', `No hay suficiente stock (Máximo: ${stockMaximo})`, 'danger');
        return;
    }

    const precio = tipo === 'unidad' 
        ? parseFloat(productoSeleccionado.precio_unidad) 
        : parseFloat(productoSeleccionado.precio_metro);

    const subtotal = precio * cantidad;

    // Verificar si el producto ya está en el carrito
    const indexExistente = carrito.findIndex(item => 
        item.id === productoSeleccionado.id && item.tipo === tipo);
    
    if (indexExistente >= 0) {
        // Actualizar cantidad si ya existe
        carrito[indexExistente].cantidad += cantidad;
        carrito[indexExistente].subtotal = carrito[indexExistente].cantidad * precio;
        mostrarToast('Actualizado', 'Cantidad del producto actualizada', 'info');
    } else {
        // Agregar nuevo producto
        carrito.push({
            id: productoSeleccionado.id,
            nombre: productoSeleccionado.nombre,
            tipo: tipo,
            cantidad: cantidad,
            precio: precio,
            subtotal: subtotal,
            tipo_venta: productoSeleccionado.tipo_venta
        });
        mostrarToast('Agregado', 'Producto añadido al carrito');
    }

    actualizarCarrito();
    modalProducto.hide();
}

function actualizarCarrito() {
    let html = '';
    let total = 0;

    if (carrito.length === 0) {
        html = `<tr><td colspan="6" class="text-center py-4 text-muted">No hay productos en el carrito</td></tr>`;
        document.getElementById('btnVenta').disabled = true;
    } else {
        carrito.forEach((item, index) => {
            html += `
                <tr>
                    <td class="align-middle">${item.nombre}</td>
                    <td class="align-middle">
                        <div class="input-group input-group-sm" style="width: 120px">
                            <button class="btn btn-outline-secondary" onclick="actualizarCantidad(${index}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" 
                                   value="${item.cantidad}" min="0.01" step="0.01"
                                   onchange="actualizarCantidad(${index}, 0, this.value)">
                            <button class="btn btn-outline-secondary" onclick="actualizarCantidad(${index}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="align-middle text-capitalize">${item.tipo}</td>
                    <td class="align-middle">$${item.precio.toFixed(2)}</td>
                    <td class="align-middle">$${item.subtotal.toFixed(2)}</td>
                    <td class="align-middle">
                        <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;
            total += item.subtotal;
        });
    }

    document.getElementById('tablaCarrito').innerHTML = html;
    document.getElementById('total').innerText = total.toFixed(2);
    validarVenta();
}

function actualizarCantidad(index, cambio, nuevoValor = null) {
    if (nuevoValor !== null) {
        carrito[index].cantidad = parseFloat(nuevoValor) || 0.01;
    } else {
        carrito[index].cantidad += cambio;
        if (carrito[index].cantidad < 0.01) carrito[index].cantidad = 0.01;
    }
    
    carrito[index].subtotal = carrito[index].cantidad * carrito[index].precio;
    actualizarCarrito();
}

function eliminarProducto(index) {
    const productoEliminado = carrito[index].nombre;
    carrito.splice(index, 1);
    actualizarCarrito();
    mostrarToast('Eliminado', `${productoEliminado} fue removido del carrito`, 'danger');
}

function validarVenta() {
    const monto = parseFloat(document.getElementById('montoRecibido').value);
    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);
    const cambioElement = document.getElementById('cambioMensaje');

    const boton = document.getElementById('btnVenta');
    
    if (isNaN(monto)) {
        cambioElement.innerHTML = '';
        boton.disabled = true;
        return;
    }
    
    if (monto < total) {
        const faltante = total - monto;
        cambioElement.innerHTML = `<span class="text-danger">Faltan $${faltante.toFixed(2)}</span>`;
        boton.disabled = true;
    } else {
        const cambio = monto - total;
        cambioElement.innerHTML = `<span class="text-success">Cambio: $${cambio.toFixed(2)}</span>`;
        boton.disabled = carrito.length === 0;
    }
}

async function verificarStock() {
    try {
        const response = await fetch('/ventas/verificar-stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ productos: carrito })
        });

        const data = await response.json();

        if (data.error) {
            // Mostrar modal con errores de stock
            document.getElementById('stockErrorMensaje').innerHTML = 
                data.error.split(', ').map(err => `<p>• ${err}</p>`).join('');
            modalStockError.show();
            return false;
        }

        return true;
    } catch (error) {
        mostrarToast('Error', 'Error al verificar el stock', 'danger');
        return false;
    }
}

async function realizarVenta() {
    const monto = parseFloat(document.getElementById('montoRecibido').value);
    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);

    if (isNaN(monto) || monto < total) {
        mostrarToast('Error', 'El monto recibido es insuficiente', 'danger');
        return;
    }

    // Verificar stock antes de proceder
    const stockOk = await verificarStock();
    if (!stockOk) return;

    // Mostrar loading en el botón
    const btnVenta = document.getElementById('btnVenta');
    const btnOriginalHTML = btnVenta.innerHTML;
    btnVenta.disabled = true;
    btnVenta.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Procesando venta...`;

    try {
        // Preparar datos para enviar
        const datosVenta = {
            productos: carrito.map(item => ({
                id: item.id,
                nombre: item.nombre,
                tipo: item.tipo,
                cantidad: item.cantidad,
                precio: item.precio
            })),
            monto_recibido: monto
        };

        const response = await fetch("/ventas/registrar", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: JSON.stringify(datosVenta)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Error en el servidor');
        }

        if (data.success) {
            // Limpiar carrito y mostrar éxito
            carrito = [];
            document.getElementById('montoRecibido').value = '';
            actualizarCarrito();

            // Guardar ID de venta para el ticket
            ventaIdGenerada = data.venta_id;
            document.getElementById('ventaTotal').textContent = data.total.toFixed(2);

            // Mostrar modal de éxito
            const modal = new bootstrap.Modal(document.getElementById('modalVentaExitosa'));
            modal.show();
        } else {
            throw new Error(data.error || 'Error al registrar la venta');
        }
    } catch (error) {
        console.error('Error al registrar venta:', error);
        mostrarToast('Error', error.message, 'danger');
        
        // Mostrar detalles del error en consola para depuración
        if (error.response) {
            error.response.json().then(errData => {
                console.error('Detalles del error:', errData);
            });
        }
    } finally {
        btnVenta.innerHTML = btnOriginalHTML;
        btnVenta.disabled = false;
    }
}

function generarTicket() {
    if (!ventaIdGenerada) {
        mostrarToast('Error', 'No se encontró el ID de la venta', 'danger');
        return;
    }

    // Abrir en una nueva pestaña
    window.open(`/ventas/ticket/${ventaIdGenerada}`, '_blank');
    
    // Cerrar el modal después de generar el ticket
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalVentaExitosa'));
    if (modal) {
        modal.hide();
    }
}
</script>
@endpush