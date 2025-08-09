<div>
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
    <div class="overflow-x-auto mt-2">
        <table class="min-w-full bg-white rounded-lg overflow-hidden">
            <thead class="bg-[#0a2a4d] text-white">
                <tr>
                    <th class="py-2 px-4 text-left">Fecha</th>
                    <th class="py-2 px-4 text-left">ID Corte</th>
                    <th class="py-2 px-4 text-right">Total Ventas</th>
                    <th class="py-2 px-4 text-right">Total Efectivo</th>
                    <th class="py-2 px-4 text-right">Total General</th>
                    <th class="py-2 px-4 text-right">Ganancias</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($cortes as $corte)
                <tr>
                    <td class="py-2 px-4">{{ \Carbon\Carbon::parse($corte->fecha_inicio)->format('d/m/Y') }}</td>
                    <td class="py-2 px-4">C-{{ $corte->id }}</td>
                    <td class="py-2 px-4 text-right font-medium">${{ number_format($corte->total_ventas, 2) }}</td>
                    <td class="py-2 px-4 text-right font-medium">${{ number_format($corte->total_efectivo, 2) }}</td>
                    <td class="py-2 px-4 text-right font-medium">${{ number_format($corte->total_efectivo + $corte->total_ventas, 2) }}</td>
                    <td class="py-2 px-4 text-right font-medium text-green-600">${{ number_format($corte->total_ganancias, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                        No hay cortes en el periodo seleccionado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-end justify-end mt-2">
        <button type="button" id="btn-exportar-historico-pdf-directo" class="bg-[#8F001A] text-white px-4 py-2 rounded-lg hover:bg-red-800 h-[42px]">
            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF Histórico
        </button>
    </div>
</div>

<script>
document.getElementById('btn-exportar-historico-pdf-directo')?.addEventListener('click', function() {
    const form = document.getElementById('form-filtro-historico-nuevo');
    if (!form) {
        alert('No se encontró el formulario de fechas.');
        return;
    }
    const fechaInicio = form.elements['fecha_inicio'].value;
    const fechaFin = form.elements['fecha_fin'].value;
    if (!fechaInicio || !fechaFin) {
        mostrarToast('Selecciona un rango de fechas primero', 'warning');
        return;
    }
    const params = new URLSearchParams({
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
    });
    window.open('/historico-cortes/pdf?' + params.toString(), '_blank');
});
</script> 