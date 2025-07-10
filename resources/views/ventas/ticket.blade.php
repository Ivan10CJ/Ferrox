<!DOCTYPE html> <html lang="es"> <head> <meta charset="UTF-8"> <title>Ticket de Venta</title> <style> @page { margin: 10px; }

    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .ticket {
        width: 300px;
        margin: auto;
    }

    .center {
        text-align: center;
    }

    .logo {
        display: block;
        margin: auto;
        margin-bottom: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        padding: 4px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }

    th {
        font-weight: bold;
    }

    .totales {
        margin-top: 10px;
    }

    .totales p {
        margin: 3px 0;
    }

    .gracias {
        text-align: center;
        margin-top: 10px;
    }
</style>
</head> <body> <div class="ticket"> {{-- Logo de la ferretería (opcional) --}} <img src="{{ public_path('logo.png') }}" alt="Logo" width="60" class="logo">

    <h3 class="center">Ferretería Ferros</h3>
    <p class="center">Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</p>
    <p class="center">Atendido por: {{ $venta->usuario->nombre_completo }}</p>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <p><strong>Total:</strong> ${{ number_format($total, 2) }}</p>
        <p><strong>Pago:</strong> ${{ number_format($pago, 2) }}</p>
        <p><strong>Cambio:</strong> ${{ number_format($cambio, 2) }}</p>
    </div>

    <p class="gracias">¡Gracias por su compra!</p>
</div>
</body> </html>