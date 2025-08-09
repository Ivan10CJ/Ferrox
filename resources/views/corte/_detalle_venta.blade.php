<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-ferreteria-light-gray p-3 rounded-lg">
            <p class="font-semibold">Ticket:</p>
            <p>V-{{ $venta->id }}</p>
        </div>
        <div class="bg-ferreteria-light-gray p-3 rounded-lg">
            <p class="font-semibold">Fecha:</p>
            <p>{{ $venta->fecha->format('d/m/Y H:i') }}</p>
        </div>
        <div class="bg-ferreteria-light-gray p-3 rounded-lg">
            <p class="font-semibold">Responsable:</p>
            <p>{{ $venta->usuario->nombre_completo }}</p>
        </div>
    </div>

    <div class="bg-ferreteria-light-gray p-3 rounded-lg">
        <p class="font-semibold mb-2">Productos:</p>
        <table class="min-w-full bg-white rounded-lg overflow-hidden">
            <thead style="background-color: #2563eb; color: white; font-weight: 600;">
                <tr>
                    <th class="py-2 px-4 text-left" style="color: white;">Código</th>
                    <th class="py-2 px-4 text-left" style="color: white;">Producto</th>
                    <th class="py-2 px-4 text-right" style="color: white;">Cantidad</th>
                    <th class="py-2 px-4 text-right" style="color: white;">Precio Venta</th>
                    <th class="py-2 px-4 text-right" style="color: white;">Costo</th>
                    <th class="py-2 px-4 text-right" style="color: white;">Ganancia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ferreteria-light-gray">
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td class="py-2 px-4">{{ $detalle->inventario->codigo }}</td>
                    <td class="py-2 px-4">{{ $detalle->inventario->nombre }}</td>
                    <td class="py-2 px-4 text-right">{{ $detalle->cantidad }}</td>
                    <td class="py-2 px-4 text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="py-2 px-4 text-right">${{ number_format($detalle->inventario->precio_compra_unidad ?? $detalle->inventario->precio_compra_metro ?? 0, 2) }}</td>
                    <td class="py-2 px-4 text-right text-green-600">${{ number_format(($detalle->precio_unitario - ($detalle->inventario->precio_compra_unidad ?? $detalle->inventario->precio_compra_metro ?? 0)) * $detalle->cantidad, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-ferreteria-light-gray p-3 rounded-lg">
            <p class="font-semibold">Total Venta:</p>
            <p class="text-lg font-bold">${{ number_format($venta->total, 2) }}</p>
        </div>
        <div class="bg-ferreteria-light-gray p-3 rounded-lg">
            <p class="font-semibold">Costos:</p>
            <p class="text-lg font-bold">${{ number_format($venta->total - $venta->ganancia, 2) }}</p>
        </div>
        <div class="bg-ferreteria-light-gray p-3 rounded-lg">
            <p class="font-semibold">Ganancia Bruta:</p>
            <p class="text-lg font-bold text-green-600">${{ number_format($venta->ganancia, 2) }}</p>
        </div>
    </div>
</div>