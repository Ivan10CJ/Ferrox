<div>
    @php
        $totalVentas = 0;
        $totalCostos = 0;
        $totalGanancias = 0;
        $totalEfectivo = 0;
        $totalGeneral = 0;
        foreach ($cortes as $cortesDia) {
            foreach ($cortesDia as $corte) {
                $totalVentas += $corte->total_ventas;
                $totalCostos += $corte->total_costos;
                $totalGanancias += $corte->total_ganancias;
                $totalEfectivo += $corte->total_efectivo;
                $totalGeneral += $corte->total_efectivo + $corte->total_ventas;
            }
        }
    @endphp
    @if($cortes->count() > 0)
        <div class="flex justify-end mb-4">
            <a href="#" id="btn-exportar-historico-pdf" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-800 font-semibold shadow flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Exportar Histórico PDF
            </a>
        </div>
        <script>
document.getElementById('btn-exportar-historico-pdf')?.addEventListener('click', function(e) {
    e.preventDefault();
    const form = document.getElementById('form-filtro-historico');
    if (!form) return;
    const fechaInicio = form.elements['fecha_inicio'].value;
    const fechaFin = form.elements['fecha_fin'].value;
    if (!fechaInicio || !fechaFin) {
        alert('Selecciona un rango de fechas primero');
        return;
    }
    const params = new URLSearchParams({
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
    });
    window.open('/historico-cortes/pdf?' + params.toString(), '_blank');
});
</script>
        <div class="bg-gray-100 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-2">PREVISUALIZACIÓN DEL PERIODO</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="font-semibold">Total Ventas:</p>
                    <p class="text-xl font-bold">${{ number_format($totalVentas, 2) }}</p>
                </div>
                <div>
                    <p class="font-semibold">Total Efectivo:</p>
                    <p class="text-xl font-bold">${{ number_format($totalEfectivo, 2) }}</p>
                </div>
                <div>
                    <p class="font-semibold">Total General:</p>
                    <p class="text-xl font-bold">${{ number_format($totalGeneral, 2) }}</p>
                </div>
                <div>
                    <p class="font-semibold">Ganancias Totales:</p>
                    <p class="text-xl font-bold text-green-600">${{ number_format($totalGanancias, 2) }}</p>
                </div>
            </div>
        </div>
        @foreach($cortes as $fecha => $cortesDia)
        <div class="mb-6">
            <h4 class="text-lg font-semibold bg-gray-100 p-2 rounded-lg">
                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
            </h4>
            <div class="overflow-x-auto mt-2">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-[#0a2a4d] text-white">
                        <tr>
                            <th class="py-2 px-4 text-left">ID Corte</th>
                            <th class="py-2 px-4 text-left">Responsable</th>
                            <th class="py-2 px-4 text-right">Total Ventas</th>
                            <th class="py-2 px-4 text-right">Total Efectivo</th>
                            <th class="py-2 px-4 text-right">Total General</th>
                            <th class="py-2 px-4 text-right">Ganancias</th>
                            <th class="py-2 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($cortesDia as $corte)
                        <tr>
                            <td class="py-2 px-4">C-{{ $corte->id }}</td>
                            <td class="py-2 px-4">{{ $corte->usuario->nombre_completo }}</td>
                            <td class="py-2 px-4 text-right font-medium">${{ number_format($corte->total_ventas, 2) }}</td>
                            <td class="py-2 px-4 text-right font-medium">${{ number_format($corte->total_efectivo, 2) }}</td>
                            <td class="py-2 px-4 text-right font-medium">${{ number_format($corte->total_efectivo + $corte->total_ventas, 2) }}</td>
                            <td class="py-2 px-4 text-right font-medium text-green-600">${{ number_format($corte->total_ganancias, 2) }}</td>
                            <td class="py-2 px-4 text-center">
                                <a href="{{ route('corte.pdf', $corte->id) }}" class="text-[#0a2a4d] hover:text-blue-800 p-1 rounded-full hover:bg-gray-100" title="Ver PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
        <div class="bg-gray-100 p-4 rounded-lg mt-6">
            <h3 class="text-lg font-semibold mb-2">RESUMEN DEL PERIODO</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="font-semibold">Total Ventas:</p>
                    <p class="text-xl font-bold">${{ number_format($totalVentas, 2) }}</p>
                </div>
                <div>
                    <p class="font-semibold">Total Efectivo:</p>
                    <p class="text-xl font-bold">${{ number_format($totalEfectivo, 2) }}</p>
                </div>
                <div>
                    <p class="font-semibold">Total General:</p>
                    <p class="text-xl font-bold">${{ number_format($totalGeneral, 2) }}</p>
                </div>
                <div>
                    <p class="font-semibold">Ganancias Totales:</p>
                    <p class="text-xl font-bold text-green-600">${{ number_format($totalGanancias, 2) }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-info-circle text-3xl text-[#0a2a4d] mb-2"></i>
            <p class="text-lg">No se encontraron cortes en el periodo seleccionado</p>
        </div>
    @endif
</div>