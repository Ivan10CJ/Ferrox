@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')

<div class="container"> <h2 class="mb-4">Editar Usuario</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="nombre_completo" class="form-label">Nombre completo</label>
        <input type="text" class="form-control" name="nombre_completo" value="{{ old('nombre_completo', $usuario->nombre_completo) }}" required>
    </div>

    <div class="mb-3">
        <label for="nombre_usuario" class="form-label">Nombre de usuario</label>
        <input type="text" class="form-control" name="nombre_usuario" value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Nueva contraseña (opcional)</label>
        <input type="password" class="form-control" name="password">
        <small class="form-text text-muted">
            Solo llena este campo si deseas cambiar la contraseña.
            Debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.
        </small>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
        <input type="password" class="form-control" name="password_confirmation">
    </div>

    <div class="mb-3">
        <label for="id_rol" class="form-label">Rol</label>
        <select name="id_rol" class="form-select" required>
            @foreach ($roles as $rol)
                <option value="{{ $rol->id_rol }}" {{ $usuario->id_rol == $rol->id_rol ? 'selected' : '' }}>
                    {{ $rol->nombre_rol }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-danger">Actualizar</button>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
</div> @endsection