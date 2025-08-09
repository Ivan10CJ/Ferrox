@php use Carbon\Carbon; @endphp
<div class="p-4">
    <h2 class="text-xl font-bold mb-4">Detalle de Venta #{{ $venta->id }}</h2>
    <div class="mb-4">
        <p><strong>Responsable:</strong> {{ $venta->usuario->nombre_completo ?? 'N/A' }}</p>
        <p><strong>Fecha:</strong> {{ $venta->fecha ? Carbon::parse($venta->fecha)->format('d/m/Y H:i') : 'N/A' }}</p>
        <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
        <p><strong>Ganancia:</strong> ${{ number_format($gananciaTotal, 2) }}</p>
    </div>
    <h3 class="font-semibold mb-2">Productos vendidos</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg overflow-hidden border">
            <thead class="bg-[#0a2a4d] text-white">
                <tr>
                    <th class="py-2 px-4 text-left">Producto</th>
                    <th class="py-2 px-4 text-left">Cantidad</th>
                    <th class="py-2 px-4 text-left">Precio Unitario</th>
                    <th class="py-2 px-4 text-left">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                    <tr>
                        <td class="py-2 px-4">{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                        <td class="py-2 px-4">{{ $detalle->cantidad }}</td>
                        <td class="py-2 px-4">${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="py-2 px-4">${{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> 