
@extends('layouts.app')

@section('title', 'Realizar Venta')

@section('content')
<div class="container">
    @if(request()->query('success') == 'ticket')
        <div class="alert alert-success">
            Ticket descargado correctamente.
        </div>
    @endif

    <h2 class="mb-4">Realizar Venta</h2>

    {{-- Buscador --}}
    <form id="form-buscar" class="mb-4 d-flex align-items-center">
        <label for="buscar" class="form-label me-2"><i class="fas fa-search"></i> Buscar producto:</label>
        <input type="text" id="buscar" name="buscar" class="form-control me-2" placeholder="Ingresa el código o nombre del producto" style="max-width: 300px;">
        <button type="submit" class="btn btn-danger"><i class="fas fa-search"></i> Buscar</button>
    </form>

    {{-- Resultados de búsqueda --}}
    <div id="lista-productos" class="mb-4" style="display: none;"></div>

    {{-- Detalle del producto seleccionado --}}
    <div id="detalle-producto" class="p-4 mb-4" style="background-color: #F4F3EB; border-radius: 5px; display: none;">
        <div id="info-producto"></div>

        {{-- Cantidad y botón agregar --}}
        <div id="cantidad-agregar" class="d-flex align-items-center mt-3">
            <label class="me-2">Cantidad:</label>
            <input type="number" id="cantidad" class="form-control me-3" value="1" min="1" style="width: 80px;">
            <button type="button" id="btn-agregar" class="btn btn-danger">Agregar</button>
        </div>
    </div>

    {{-- Mensaje si no se encuentra producto --}}
    <div id="mensaje-error" class="p-4 mb-4 text-center" style="background-color: #F4F3EB; border-radius: 5px; display: none;">
        Producto no encontrado.
    </div>

    {{-- Tabla de productos --}}
    <table class="table table-bordered" style="background-color: #052A59; color: white;">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="detalle-venta"></tbody>
    </table>

    {{-- Total --}}
    <div class="d-flex justify-content-end">
        <h4>Total: $<span id="total">0.00</span></h4>
    </div>

    {{-- Pago y botón Realizar Venta --}}
    <div id="btn-venta-container" class="mt-4" style="display: none;">
        <div class="mb-3 d-flex align-items-center">
            <label for="monto-pagado" class="form-label me-2">Monto Pagado:</label>
            <input type="number" id="monto-pagado" class="form-control me-2" style="width: 150px;" min="0" step="0.01">
            <span class="me-2"><strong>Cambio: $<span id="cambio">0.00</span></strong></span>
        </div>
        <div class="d-flex justify-content-end">
            <button type="button" id="btn-guardar" class="btn btn-danger" disabled>Realizar Venta</button>
        </div>
    </div>

    {{-- Botón Generar Ticket (solo visible después de la venta) --}}
    <div id="ticket-container" class="mt-4" style="display: none;">
        <div class="alert alert-success">Venta registrada correctamente.</div>
        <div class="d-flex justify-content-end">
            <button type="button" id="btn-ticket" class="btn btn-primary">Generar Ticket</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let carrito = [];
    let productoSeleccionado = null;
    let totalVenta = 0;
    let ventaId = null;

    document.getElementById('form-buscar').addEventListener('submit', function(e) {
        e.preventDefault();
        let buscar = document.getElementById('buscar').value;

        fetch(`/ventas/buscar/${buscar}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    mostrarOpciones(data);
                } else {
                    document.getElementById('lista-productos').style.display = 'none';
                    document.getElementById('detalle-producto').style.display = 'none';
                    document.getElementById('mensaje-error').style.display = 'block';
                }
            });
    });

    function mostrarOpciones(productos) {
        let opcionesHTML = '<h5>Seleccione un producto:</h5><ul class="list-group">';
        productos.forEach(producto => {
            opcionesHTML += `
                <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick='seleccionarProducto(${JSON.stringify(producto)})'>
                    ${producto.codigo} - ${producto.nombre} (${producto.unidad_base.nombre}) - $${producto.precio}
                </li>
            `;
        });
        opcionesHTML += '</ul>';

        document.getElementById('lista-productos').innerHTML = opcionesHTML;
        document.getElementById('lista-productos').style.display = 'block';
        document.getElementById('detalle-producto').style.display = 'none';
        document.getElementById('mensaje-error').style.display = 'none';
    }

    function seleccionarProducto(producto) {
        productoSeleccionado = producto;

        document.getElementById('info-producto').innerHTML = `
            <strong>Código:</strong> ${producto.codigo} &nbsp;&nbsp;
            <strong>Producto:</strong> ${producto.nombre} &nbsp;&nbsp;
            <strong>Unidad base:</strong> ${producto.unidad_base.nombre} &nbsp;&nbsp;
            <strong>Precio unitario...</strong> $${producto.precio} &nbsp;&nbsp;
            <strong>Stock disponible:</strong> ${producto.stock}
        `;

        document.getElementById('detalle-producto').style.display = 'block';
        document.getElementById('lista-productos').style.display = 'none';
        document.getElementById('cantidad').value = 1;
        document.getElementById('cantidad').setAttribute('max', producto.stock);
    }

    document.getElementById('btn-agregar').addEventListener('click', function() {
        let cantidad = parseInt(document.getElementById('cantidad').value);
        if (cantidad <= 0 || !productoSeleccionado) return;

        if (cantidad > productoSeleccionado.stock) {
            alert('No puedes agregar más cantidad que la disponible en stock.');
            return;
        }

        let subtotal = cantidad * parseFloat(productoSeleccionado.precio);

        carrito.push({
            id: productoSeleccionado.id,
            nombre: productoSeleccionado.nombre,
            unidad: productoSeleccionado.unidad_base.nombre,
            precio: parseFloat(productoSeleccionado.precio),
            cantidad: cantidad,
            subtotal: subtotal
        });

        actualizarTabla();
        actualizarTotal();
        limpiarBusqueda();
    });

    function eliminarProducto(index) {
        carrito.splice(index, 1);
        actualizarTabla();
        actualizarTotal();
    }

    function actualizarTabla() {
        let tbody = document.getElementById('detalle-venta');
        tbody.innerHTML = '';
        carrito.forEach((item, index) => {
            tbody.innerHTML += `
                <tr>
                    <td>${item.nombre}</td>
                    <td>${item.cantidad}</td>
                    <td>${item.unidad}</td>
                    <td>$${item.precio.toFixed(2)}</td>
                    <td>$${item.subtotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">Eliminar</button></td>
                </tr>
            `;
        });

        if (carrito.length > 0) {
            document.getElementById('btn-venta-container').style.display = 'block';
        } else {
            document.getElementById('btn-venta-container').style.display = 'none';
            document.getElementById('btn-guardar').disabled = true;
            document.getElementById('monto-pagado').value = '';
            document.getElementById('cambio').innerText = '0.00';
        }
    }

    function actualizarTotal() {
        totalVenta = carrito.reduce((sum, item) => sum + item.subtotal, 0);
        document.getElementById('total').innerText = totalVenta.toFixed(2);
        validarPago();
    }

    function validarPago() {
        let montoPagado = parseFloat(document.getElementById('monto-pagado').value);
        let cambio = montoPagado - totalVenta;

        if (montoPagado >= totalVenta && carrito.length > 0) {
            document.getElementById('btn-guardar').disabled = false;
        } else {
            document.getElementById('btn-guardar').disabled = true;
        }

        document.getElementById('cambio').innerText = cambio >= 0 ? cambio.toFixed(2) : '0.00';
    }

    document.getElementById('monto-pagado').addEventListener('input', function() {
        validarPago();
    });

    document.getElementById('btn-guardar').addEventListener('click', function() {
        let montoPagado = parseFloat(document.getElementById('monto-pagado').value);
        let cambio = montoPagado - totalVenta;

        fetch('/ventas/guardar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                productos: carrito,
                monto_pagado: montoPagado,
                cambio: cambio
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ventaId = data.venta_id;
                alert('Venta registrada correctamente.');

                document.querySelectorAll('input, button').forEach(element => element.disabled = true);
                document.getElementById('btn-ticket').disabled = false;

                document.getElementById('ticket-container').style.display = 'block';
            } else {
                alert('Error al registrar la venta.');
            }
        });
    });

    document.getElementById('btn-ticket').addEventListener('click', function() {
        window.open(`/ventas/ticket/${ventaId}`, '_blank');
        setTimeout(function() {
            window.location.href = '/ventas?success=ticket';
        }, 2000);
    });

    function limpiarBusqueda() {
        document.getElementById('buscar').value = '';
        document.getElementById('detalle-producto').style.display = 'none';
        document.getElementById('cantidad').value = 1;
    }
</script>
@endpush
