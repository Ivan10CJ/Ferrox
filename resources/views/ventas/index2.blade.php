@extends('layouts.app')

@section('title', 'Módulo Ventas')

@section('content')
<div class="container py-4">
    <h1 class="h2 fw-bold text-white bg-primary p-3 rounded mb-4">Venta de Productos</h1>

    <!-- Buscador -->
    <div class="mb-4 d-flex">
        <input type="text" id="buscarProducto" class="form-control me-2" placeholder="Buscar por código o nombre...">
        <button class="btn btn-danger" onclick="buscar()">Buscar</button>
    </div>
    <div id="resultadosBusqueda" class="mt-2 border rounded p-2 bg-light d-none"></div>

    <!-- Detalles del producto -->
    <div id="detalleProducto" class="d-none bg-light p-4 rounded shadow mb-4">
        <h3 class="h5 fw-bold text-primary mb-2">Producto Seleccionado</h3>
        <div id="datosProducto"></div>
        <div class="mt-2 d-flex align-items-center">
            <label class="fw-bold me-2">Cantidad:</label>
            <input type="number" id="cantidad" class="form-control w-auto me-2" min="1" value="1" style="width: 80px;">
            
            <select id="tipoVenta" class="form-select me-2 d-none" style="width: 120px;"></select>

            <button class="btn btn-danger" onclick="agregarProducto()">Agregar</button>
        </div>
    </div>

    <!-- Carrito -->
    <div id="carrito" class="bg-light p-4 rounded shadow mt-4">
        <h3 class="h5 fw-bold text-primary mb-3">Carrito de Compras</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr class="table-primary">
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tablaCarrito"></tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <p class="fw-bold fs-5">Total: $<span id="total">0.00</span></p>
        </div>

        <div class="mt-4 d-flex align-items-center">
            <label class="fw-bold me-2">Monto recibido:</label>
            <input type="number" id="montoRecibido" class="form-control w-auto" min="0" step="0.01" oninput="validarVenta()" style="width: 150px;">
        </div>

        <button id="btnVenta" class="btn btn-danger mt-4" onclick="realizarVenta()" disabled>
            Realizar Venta
        </button>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalVentaExitosa" tabindex="-1" aria-labelledby="modalVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalVentaLabel">Venta Registrada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        La venta se registró correctamente. ¿Deseas generar el ticket?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="generarTicket()">Generar Ticket</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let productoSeleccionado = null;
let carrito = [];
let ventaIdGenerada = null;

document.getElementById('buscarProducto').addEventListener('keyup', function (e) {
    if (e.key === 'Enter') buscar();
});

function buscar() {
    const query = document.getElementById('buscarProducto').value;
    if (query.length < 2) return;

    fetch(`/ventas/buscar-producto?query=${query}`)
        .then(res => res.json())
        .then(data => {
            const resultados = document.getElementById('resultadosBusqueda');
            let html = '';
            
            if (data.length === 0) {
                html = `<p class="text-muted">No se encontraron productos</p>`;
            } else {
                data.forEach(p => {
                    html += `
                        <div onclick='seleccionarProducto(${JSON.stringify(p)})'
                             class="cursor-pointer p-2 border-bottom hover-bg-light">
                             ${p.nombre} (${p.codigo})
                        </div>`;
                });
            }
            
            resultados.innerHTML = html;
            resultados.classList.remove('d-none');
        });
}

function seleccionarProducto(data) {
    productoSeleccionado = data;
    document.getElementById('detalleProducto').classList.remove('d-none');
    document.getElementById('buscarProducto').value = '';
    document.getElementById('resultadosBusqueda').innerHTML = '';
    document.getElementById('resultadosBusqueda').classList.add('d-none');

    const tipo = data.tipo_venta;
    const tipoVentaSelect = document.getElementById('tipoVenta');
    tipoVentaSelect.innerHTML = '';
    
    if (tipo === 'kilo' || tipo === 'pieza') {
        tipoVentaSelect.classList.add('d-none');
        tipoVentaSelect.innerHTML = `<option value="unidad" selected>Unidad</option>`;
    } else {
        tipoVentaSelect.classList.remove('d-none');
        tipoVentaSelect.innerHTML = `
            <option value="unidad" selected>Unidad</option>
            <option value="metro">Metro</option>`;
    }

    document.getElementById('datosProducto').innerHTML = `
        <p><strong>Nombre:</strong> ${data.nombre}</p>
        <p><strong>Tipo de venta:</strong> ${data.tipo_venta}</p>
        <p><strong>Precio Unidad:</strong> $${parseFloat(data.precio_unidad).toFixed(2)}</p>
        <p><strong>Precio Metro:</strong> ${data.precio_metro ? '$' + parseFloat(data.precio_metro).toFixed(2) : 'N/A'}</p>
    `;
}

function agregarProducto() {
    const cantidad = parseFloat(document.getElementById('cantidad').value);
    if (!productoSeleccionado || isNaN(cantidad) || cantidad <= 0) return;

    const tipo = (productoSeleccionado.tipo_venta === 'kilo' || productoSeleccionado.tipo_venta === 'pieza')
                 ? 'unidad'
                 : document.getElementById('tipoVenta').value;

    const precio = tipo === 'unidad'
        ? parseFloat(productoSeleccionado.precio_unidad)
        : parseFloat(productoSeleccionado.precio_metro);

    const subtotal = precio * cantidad;

    carrito.push({
        id: productoSeleccionado.id,
        nombre: productoSeleccionado.nombre,
        tipo: tipo,
        cantidad: cantidad,
        precio: precio,
        subtotal: subtotal
    });

    actualizarCarrito();

    productoSeleccionado = null;
    document.getElementById('detalleProducto').classList.add('d-none');
    document.getElementById('cantidad').value = 1;
}

function eliminarProducto(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

function actualizarCarrito() {
    let html = '';
    let total = 0;

    carrito.forEach((item, index) => {
        html += `
            <tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>${item.tipo}</td>
                <td>$${item.precio.toFixed(2)}</td>
                <td>$${item.subtotal.toFixed(2)}</td>
                <td><button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">Eliminar</button></td>
            </tr>`;
        total += item.subtotal;
    });

    document.getElementById('tablaCarrito').innerHTML = html;
    document.getElementById('total').innerText = total.toFixed(2);
    validarVenta();
}

function validarVenta() {
    const monto = parseFloat(document.getElementById('montoRecibido').value);
    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);

    const boton = document.getElementById('btnVenta');
    boton.disabled = isNaN(monto) || monto < total || carrito.length === 0;
}

function realizarVenta() {
    const monto = parseFloat(document.getElementById('montoRecibido').value);
    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);

    if (isNaN(monto) || monto < total) {
        alert("El monto recibido es insuficiente.");
        return;
    }

    fetch("{{ route('ventas.registrar') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            productos: carrito,
            monto_recibido: monto
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.message) {
            carrito = [];
            document.getElementById('montoRecibido').value = '';
            actualizarCarrito();

            // guardar id_venta si lo devuelves del backend
            ventaIdGenerada = data.venta_id ?? null;

            // mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalVentaExitosa'));
            modal.show();
        } else {
            alert("❌ Error: " + data.error);
        }
    })
    .catch(err => {
        console.error(err);
        alert("❌ Error al registrar la venta.");
    });
}

function generarTicket() {
    if (!ventaIdGenerada) {
        alert("No se pudo generar el ticket. Intenta nuevamente.");
        return;
    }

    window.open(`/ventas/${ventaIdGenerada}/ticket`, '_blank');
}
</script>
@endpush