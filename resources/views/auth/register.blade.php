@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label for="nombre_completo">Nombre Completo</label>
        <input id="nombre_completo" type="text" class="form-control @error('nombre_completo') is-invalid @enderror" 
               name="nombre_completo" value="{{ old('nombre_completo') }}" required>
        @error('nombre_completo')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="nombre_usuario">Usuario</label>
        <input id="nombre_usuario" type="text" class="form-control @error('nombre_usuario') is-invalid @enderror" 
               name="nombre_usuario" value="{{ old('nombre_usuario') }}" required>
        @error('nombre_usuario')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Contraseña</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
               name="password" required>
        @error('password')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmar Contraseña</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
    </div>

    <button type="submit" class="btn-primary">Registrarse</button>
</form>
@endsection

@section('auth-footer')
    ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
@endsection