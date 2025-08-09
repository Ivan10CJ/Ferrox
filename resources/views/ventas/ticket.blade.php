<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ticket de Venta #{{ $venta->id }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 10px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h2 { 
            margin: 5px 0; 
            font-size: 18px;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 5px;
        }
        .info { 
            margin-bottom: 10px; 
        }
        .info p { 
            margin: 3px 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0;
            font-size: 11px;
        }
        th { 
            background-color: #f2f2f2;
            font-weight: bold;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 5px; 
            text-align: left; 
        }
        .totals {
            margin-top: 10px;
            text-align: right;
            font-size: 13px;
        }
        .total {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Asegúrate de que la ruta del logo sea correcta -->
        <img src="{{ public_path('logo.png') }}" class="logo" alt="Logo Ferretería Ferros">
        <h2>Ferretería Ferros</h2>
        <p>Ignacio Manuel Altamirano #18, Ixmiquilpan Hidalgo</p>
        <p>TEL. 7721358489 | RFC: EAMS290702HC7</p>
    </div>

    <!-- Resto del código permanece igual -->
    <div class="info">
        <p><strong>Ticket:</strong> #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Fecha:</strong> {{ $fecha }}</p>
        <p><strong>Atendió:</strong> {{ $usuario }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Producto</th>
                <th style="width: 10%;">Cant.</th>
                <th style="width: 10%;">Tipo</th>
                <th style="width: 15%;">P. Unit.</th>
                <th style="width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
                <tr>
                    <td>{{ $detalle->inventario->nombre }}</td>
                    <td>{{ number_format($detalle->cantidad, 2) }}</td>
                    <td>
                        @if($detalle->inventario->tipo_venta === 'kilo' || $detalle->inventario->tipo_venta === 'pieza')
                            Unidad
                        @else
                            {{ ($detalle->precio_unitario == $detalle->inventario->precio_metro) ? 'Metro' : 'Unidad' }}
                        @endif
                    </td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p class="total">TOTAL: ${{ number_format($venta->total, 2) }}</p>
    </div>

    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>Este ticket es un comprobante fiscal</p>
        <p>No válido como factura</p>
    </div>
</body>
</html>