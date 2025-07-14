@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Editar Producto</h3>
    <form action="{{ route('inventario.update', $inventario->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-2">
            <!-- Repite los inputs igual que en index pero con valores: value="{{ $inventario->campo }}" -->
        </div>
        <button type="submit" class="btn btn-success mt-2">Actualizar</button>
        <a href="{{ route('inventario.index') }}" class="btn btn-secondary mt-2">Cancelar</a>
    </form>
</div>
@endsection
