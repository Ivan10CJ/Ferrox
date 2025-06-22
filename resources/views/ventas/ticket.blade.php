<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }
    </style>
</head>
<body>

    {{-- Encabezado con logo y nombre --}}
    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Logo de la ferretería">
        <h2>Ferretería Ferros</h2>
    </div>

    {{-- Datos de la venta --}}
    <p><strong>Fecha:</strong> {{ $venta->fecha }}</p>
    <p><strong>Vendedor:</strong> {{ $venta->usuario->nombre_completo }}</p>

    {{-- Tabla de productos --}}
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ $detalle->producto->unidadBase->nombre }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div class="total">
        <p>Total: ${{ number_format($venta->total, 2) }}</p>
        <p>Monto Pagado: ${{ number_format($pago, 2) }}</p>
        <p>Cambio: ${{ number_format($cambio, 2) }}</p>
    </div>

    {{-- Mensaje de cierre --}}
    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>Visítenos nuevamente</p>
    </div>

</body>
</html>
