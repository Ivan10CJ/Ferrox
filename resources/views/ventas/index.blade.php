@extends('layouts.app')

@section('title', 'Realizar Venta')

@section('content')
<div class="container">
    <h2 class="mb-4">Realizar Venta</h2>

    {{-- Buscador --}}
    <form id="form-buscar" class="mb-4 d-flex align-items-center">
        <label for="buscar" class="form-label me-2"><i class="fas fa-search"></i> Buscar producto:</label>
        <input type="text" id="buscar" name="buscar" class="form-control me-2" placeholder="Código o nombre del producto" style="max-width: 300px;">
        <button type="submit" class="btn btn-danger"><i class="fas fa-search"></i> Buscar</button>
    </form>

    {{-- Resultados de búsqueda --}}
    <div id="lista-productos" class="mb-4" style="display: none;"></div>

    {{-- Detalle del producto seleccionado --}}
    <div id="detalle-producto" class="p-4 mb-4" style="background-color: #F4F3EB; border-radius: 5px; display: none;">
        <div id="info-producto"></div>

        {{-- Tipo de venta y cantidades --}}
        <div class="mt-3">
            <div class="mb-3">
                <label for="tipo_venta" class="form-label">Tipo de venta:</label>
                <select id="tipo_venta" class="form-select" style="max-width: 200px;">
                    <option value="unidad">Por unidad</option>
                    <option value="metro">Por metro</option>
                </select>
            </div>

            <div id="venta-unidad" class="mb-3">
                <label for="cantidad_unidades" class="form-label">Cantidad de unidades:</label>
                <input type="number" id="cantidad_unidades" class="form-control" style="width: 150px;" value="1" min="1">
            </div>

            <div id="venta-metro" class="mb-3" style="display: none;">
                <label for="cantidad_metros" class="form-label">Cantidad en metros:</label>
                <input type="number" id="cantidad_metros" class="form-control" style="width: 150px;" value="1" min="0.01" step="0.01">
            </div>

            <button type="button" id="btn-agregar" class="btn btn-danger">Agregar al carrito</button>
        </div>
    </div>

    {{-- Mensaje de error si no se encuentra --}}
    <div id="mensaje-error" class="p-4 mb-4 text-center" style="background-color: #F4F3EB; border-radius: 5px; display: none;">
        Producto no encontrado.
    </div>

    {{-- Carrito --}}
    <table class="table table-bordered" style="background-color: #052A59; color: white;">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="detalle-venta"></tbody>
    </table>

    <div class="d-flex justify-content-end">
        <h4>Total: $<span id="total">0.00</span></h4>
    </div>

    {{-- Pago (inicialmente oculto) --}}
    <div id="pago-section" class="d-flex justify-content-end mt-3" style="display: none;">
        <div class="me-3">
            <label for="monto_pagado" class="form-label">Cantidad Pagada:</label>
            <input type="number" id="monto_pagado" class="form-control" placeholder="Monto pagado" min="0" step="0.01">
        </div>
        <div class="align-self-end">
            <button type="button" id="btn-realizar-venta" class="btn btn-danger" disabled>Realizar Venta</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let productoSeleccionado = null;
    let carrito = [];
    let totalVenta = 0;
    let ventaGeneradaId = null;

    document.getElementById('form-buscar').addEventListener('submit', function(e) {
        e.preventDefault();
        let buscar = document.getElementById('buscar').value;

        fetch(`/ventas/buscar/${buscar}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) mostrarOpciones(data);
                else mostrarError();
            });
    });

    function mostrarOpciones(productos) {
        let opciones = '<h5>Seleccione un producto:</h5><ul class="list-group">';
        productos.forEach(prod => {
            opciones += `<li class="list-group-item list-group-item-action" style="cursor:pointer" onclick='seleccionarProducto(${JSON.stringify(prod)})'>${prod.codigo} - ${prod.nombre}</li>`;
        });
        opciones += '</ul>';
        document.getElementById('lista-productos').innerHTML = opciones;
        document.getElementById('lista-productos').style.display = 'block';
        document.getElementById('mensaje-error').style.display = 'none';
        document.getElementById('detalle-producto').style.display = 'none';
    }

    function mostrarError() {
        document.getElementById('mensaje-error').style.display = 'block';
        document.getElementById('lista-productos').style.display = 'none';
        document.getElementById('detalle-producto').style.display = 'none';
    }

    function seleccionarProducto(prod) {
        productoSeleccionado = prod;
        let html = `<strong>Código:</strong> ${prod.codigo} | <strong>Nombre:</strong> ${prod.nombre}<br>
                    <strong>Unidades disponibles:</strong> ${prod.unidades} | <strong>Metros sobrantes:</strong> ${prod.metros_sobrantes}<br>
                    <strong>Precio por unidad:</strong> $${prod.precio_unidad} | <strong>Precio por metro:</strong> $${prod.precio_metro}`;
        document.getElementById('info-producto').innerHTML = html;
        document.getElementById('detalle-producto').style.display = 'block';
        document.getElementById('lista-productos').style.display = 'none';
    }

    document.getElementById('tipo_venta').addEventListener('change', function() {
        let tipo = this.value;
        document.getElementById('venta-unidad').style.display = (tipo === 'unidad') ? 'block' : 'none';
        document.getElementById('venta-metro').style.display = (tipo === 'metro') ? 'block' : 'none';
    });

    document.getElementById('btn-agregar').addEventListener('click', function() {
        if (!productoSeleccionado) return;

        let tipo = document.getElementById('tipo_venta').value;
        let cantidad = tipo === 'unidad' 
            ? parseInt(document.getElementById('cantidad_unidades').value) 
            : parseFloat(document.getElementById('cantidad_metros').value);

        if (!cantidad || cantidad <= 0) return;

        let precio = tipo === 'unidad' ? productoSeleccionado.precio_unidad : productoSeleccionado.precio_metro;
        let subtotal = cantidad * precio;

        carrito.push({
            id: productoSeleccionado.id,
            nombre: productoSeleccionado.nombre,
            tipo: tipo,
            cantidad: cantidad,
            precio: precio,
            subtotal: subtotal,
            unidad_venta_id: tipo === 'unidad' ? 1 : 2
        });

        actualizarTabla();
        productoSeleccionado = null;
        document.getElementById('detalle-producto').style.display = 'none';
    });

    function actualizarTabla() {
        let tbody = document.getElementById('detalle-venta');
        tbody.innerHTML = '';
        let total = 0;
        carrito.forEach((item, index) => {
            total += item.subtotal;
            tbody.innerHTML += `<tr>
                <td>${item.nombre}</td>
                <td>${item.tipo}</td>
                <td>${item.cantidad}</td>
                <td>$${item.precio.toFixed(2)}</td>
                <td>$${item.subtotal.toFixed(2)}</td>
                <td><button class="btn btn-sm btn-danger" onclick="eliminar(${index})">Eliminar</button></td>
            </tr>`;
        });
        document.getElementById('total').innerText = total.toFixed(2);
        totalVenta = total;
        document.getElementById('pago-section').style.display = carrito.length > 0 ? 'flex' : 'none';
        validarPago();
    }

    function eliminar(index) {
        carrito.splice(index, 1);
        actualizarTabla();
    }

    document.getElementById('monto_pagado').addEventListener('input', function() {
        validarPago();
    });

    function validarPago() {
        let pagado = parseFloat(document.getElementById('monto_pagado').value);
        document.getElementById('btn-realizar-venta').disabled = !(pagado >= totalVenta);
    }

    document.getElementById('btn-realizar-venta').addEventListener('click', function () {
        const pago = parseFloat(document.getElementById('monto_pagado').value);
        const cambio = pago - totalVenta;

        fetch('/ventas/guardar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                productos: carrito,
                pago_cliente: pago,
                cambio_cliente: cambio
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                ventaGeneradaId = data.venta_id;

                const link = document.createElement('a');
                link.href = `/ventas/ticket/${ventaGeneradaId}`;
                link.download = `ticket_venta_${ventaGeneradaId}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                setTimeout(() => location.reload(), 1500);
            } else {
                alert(data.message || 'Error al guardar la venta.');
            }
        });
    });
</script>
@endpush
