<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja - {{ $fecha_corte }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .logo { max-width: 80px; max-height: 80px; margin-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; margin-top: 5px; }
        .info { margin: 15px 0; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background-color: #f2f2f2; font-weight: bold; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 20px; font-weight: bold; }
        .section-title { font-weight: bold; margin: 15px 0 5px 0; }
        .footer { margin-top: 30px; font-size: 10px; text-align: center; border-top: 1px solid #333; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Logo" class="logo">
        <div class="title">{{ $negocio }}</div>
        <div class="subtitle">Reporte de Corte de Caja</div>
        <div class="business-info" style="font-size: 11px; margin-top: 8px; color: #666;">
            <div>Ignacio Manuel Altamirano #18, Ixmiquilpan Hidalgo</div>
            <div>TEL. 7721358489 | RFC: EAMS290702HC7</div>
        </div>
    </div>
    
    <div class="info">
        <div><strong>Período de corte:</strong> {{ $periodo_corte }}</div>
        <div><strong>Preparado por:</strong> {{ $preparado_por }}</div>
        <div><strong>Fecha de exportación:</strong> {{ $fecha_exportacion }}</div>
    </div>
    
    <div class="section-title">Resumen del Corte</div>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total en Efectivo</td>
                <td class="text-right">${{ number_format($corte->total_efectivo, 2) }}</td>
            </tr>
            <tr>
                <td>Total Ventas</td>
                <td class="text-right">${{ number_format($corte->total_ventas, 2) }}</td>
            </tr>
            <tr>
                <td>Total Costos</td>
                <td class="text-right">${{ number_format($corte->total_costos, 2) }}</td>
            </tr>
            <tr>
                <td>Total Ganancias</td>
                <td class="text-right">${{ number_format($corte->total_ganancias, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="section-title">Detalle de Ventas ({{ count($ventas) }})</div>
    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Fecha/Hora</th>
                <th>Responsable</th>
                <th class="text-right">Total</th>
                <th class="text-right">Ganancia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>V-{{ $venta->id }}</td>
                <td>{{ $venta->fecha->format('d/m/Y H:i') }}</td>
                <td>{{ $venta->usuario->nombre_completo }}</td>
                <td class="text-right">${{ number_format($venta->total, 2) }}</td>
                <td class="text-right">${{ number_format($venta->ganancia, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="section-title">Movimientos de Caja</div>
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
                <th>Responsable</th>
                <th>Fecha/Hora</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $movimiento)
            <tr>
                <td>{{ ucfirst($movimiento->tipo) }}</td>
                <td>{{ $movimiento->concepto }}</td>
                <td class="text-right">{{ $movimiento->tipo == 'ingreso' ? '+' : '-' }}${{ number_format($movimiento->monto, 2) }}</td>
                <td>{{ $movimiento->usuario->nombre_completo }}</td>
                <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        Ferros - {{ config('app.url') }} - Sistema de Corte de Caja
    </div>
</body>
</html>