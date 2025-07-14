@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Corte de caja</h2>
    <p>Fecha de corte: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    <p>Elaborado por: {{ Auth::user()->name }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID venta</th>
                <th>Responsable</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Total venta</th>
                <th>Ganancia</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->usuario->name ?? 'N/A' }}</td>
                    <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                    <td>{{ $venta->created_at->format('H:i:s') }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                    <td>${{ number_format($venta->ganancia, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        <p><strong>Total general:</strong> ${{ number_format($total_general, 2) }}</p>
        <p><strong>Total costo:</strong> ${{ number_format($total_costo, 2) }}</p>
        <p><strong>Total ganancia:</strong> ${{ number_format($total_ganancia, 2) }}</p>
        <p><strong>Total en caja:</strong> ${{ number_format($total_en_caja, 2) }}</p>
    </div>

    <div class="mt-3">
        <a href="#" class="btn btn-primary">Caja</a>
        <a href="#" class="btn btn-danger">Exportar a PDF</a>
        <a href="#" class="btn btn-secondary">Histórico</a>
    </div>
</div>
@endsection
