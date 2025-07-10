@extends('layouts.app')

@section('title', 'Administrar Usuarios')

@section('content')

<div class="container"> <div class="d-flex justify-content-between align-items-center mb-4"> <h2 class="mb-0">Gestión de Usuarios</h2> <a href="{{ route('admin.usuarios.create') }}" class="btn btn-danger">Nuevo Usuario</a> </div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->nombre_completo }}</td>
                <td>{{ $usuario->nombre_usuario }}</td>
                <td>{{ $usuario->rol->nombre_rol }}</td>
                <td>
                    @if(auth()->user()->id_usuario !== $usuario->id_usuario)
                        <a href="{{ route('admin.usuarios.edit', $usuario->id_usuario) }}" class="btn btn-sm btn-primary">Editar</a>
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id_usuario) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar este usuario?')">Eliminar</button>
                        </form>
                    @else
                        <span class="text-muted">Usuario en sesión</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div> 
@endsection