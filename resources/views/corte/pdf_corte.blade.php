<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja - {{ $periodo_corte }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
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
    @php
        $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
        $totalEfectivo = $totalIngresos - $totalEgresos;
        $totalVentasDia = $ventas->sum('total');
        $totalCaja = $totalVentasDia + $totalIngresos - $totalEgresos;
        $totalGanancias = $ventas->sum('ganancia');
        $totalCostos = $totalVentasDia - $totalGanancias;
        $totalGeneral = $totalCaja;
        $fmtFecha = fn($fecha) => $fecha->setTimezone('America/Mexico_City')->format('d/m/Y H:i');
    @endphp
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
                <td>Total en Efectivo (Ingresos - Egresos)</td>
                <td class="text-right">${{ number_format($totalEfectivo, 2) }}</td>
            </tr>
            <tr>
                <td>Total Ventas del Día</td>
                <td class="text-right">${{ number_format($totalVentasDia, 2) }}</td>
            </tr>
            <tr>
                <td>Total en Caja (Ventas + Ingresos - Egresos)</td>
                <td class="text-right">${{ number_format($totalCaja, 2) }}</td>
            </tr>
            <tr>
                <td>Total Costos</td>
                <td class="text-right">${{ number_format($totalCostos, 2) }}</td>
            </tr>
            <tr>
                <td>Total Ganancias</td>
                <td class="text-right">${{ number_format($totalGanancias, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total General</strong></td>
                <td class="text-right"><strong>${{ number_format($totalGeneral, 2) }}</strong></td>
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
                <td>{{ $fmtFecha($venta->fecha) }}</td>
                <td>{{ $venta->usuario->nombre_completo }}</td>
                <td class="text-right">${{ number_format($venta->total, 2) }}</td>
                <td class="text-right">${{ number_format($venta->ganancia ?? 0, 2) }}</td>
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
                <td>{{ $fmtFecha($movimiento->created_at) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        {{ $negocio }} - {{ config('app.url') }} - Sistema de Corte de Caja
    </div>
</body>
</html> 