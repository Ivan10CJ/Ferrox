@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')

<div class="container"> <h2 class="mb-4">Registrar Nuevo Usuario</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.usuarios.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="nombre_completo" class="form-label">Nombre completo</label>
        <input type="text" class="form-control" name="nombre_completo" value="{{ old('nombre_completo') }}" required>
    </div>

    <div class="mb-3">
        <label for="nombre_usuario" class="form-label">Nombre de usuario</label>
        <input type="text" class="form-control" name="nombre_usuario" value="{{ old('nombre_usuario') }}" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" name="password" required>
        <small class="form-text text-muted">Mínimo 8 caracteres, al menos una mayuscula, un número y un símbolo.</small>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <input type="password" class="form-control" name="password_confirmation" required>
    </div>

    <div class="mb-3">
        <label for="id_rol" class="form-label">Rol</label>
        <select name="id_rol" class="form-select" required>
            @foreach ($roles as $rol)
                <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-danger">Registrar</button>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
</div> @endsection