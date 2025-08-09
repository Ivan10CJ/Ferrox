<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Histórico de Cortes {{ $fechaInicio }} a {{ $fechaFin }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { max-width: 80px; max-height: 80px; margin-bottom: 10px; }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header p { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .resumen { margin-top: 30px; }
        .resumen-item { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { font-weight: bold; border-top: 2px solid #000; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Logo" class="logo">
        <h1>{{ $negocio }}</h1>
        <p>Histórico de Cortes del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p>Preparado por: {{ $preparado_por }}</p>
        <p>Fecha de exportación: {{ $fecha_exportacion }}</p>
        <div class="business-info" style="font-size: 11px; margin-top: 8px; color: #666;">
            <div>Ignacio Manuel Altamirano #18, Ixmiquilpan Hidalgo</div>
            <div>TEL. 7721358489 | RFC: EAMS290702HC7</div>
        </div>
    </div>
    <div class="resumen">
        <h2>Resumen General del Período</h2>
        <div class="resumen-item">
            <span>Total Ventas:</span>
            <span>${{ number_format($totalVentas, 2) }}</span>
        </div>
        <div class="resumen-item">
            <span>Total Efectivo:</span>
            <span>${{ number_format($totalEfectivo, 2) }}</span>
        </div>
        <div class="resumen-item">
            <span>Total General:</span>
            <span>${{ number_format($totalGeneral, 2) }}</span>
        </div>
        <div class="resumen-item total">
            <span>Ganancias Totales:</span>
            <span>${{ number_format($totalGanancias, 2) }}</span>
        </div>
    </div>
    <h2 style="margin-top:30px;">Detalle de Cortes</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Responsable</th>
                <th class="text-right">Total Ventas</th>
                <th class="text-right">Total Efectivo</th>
                <th class="text-right">Total General</th>
                <th class="text-right">Ganancias</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cortes as $corte)
            <tr>
                <td>{{ \Carbon\Carbon::parse($corte->fecha_inicio)->format('d/m/Y') }}</td>
                <td>{{ $corte->usuario->nombre_completo ?? '-' }}</td>
                <td class="text-right">${{ number_format($corte->total_ventas, 2) }}</td>
                <td class="text-right">${{ number_format($corte->total_efectivo, 2) }}</td>
                <td class="text-right">${{ number_format($corte->total_efectivo + $corte->total_ventas, 2) }}</td>
                <td class="text-right">${{ number_format($corte->total_ganancias, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>