@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="nombre_usuario">Usuario o correo electrónico</label>
        <input id="nombre_usuario" type="text" class="form-control @error('nombre_usuario') is-invalid @enderror" 
               name="nombre_usuario" value="{{ old('nombre_usuario') }}" required autofocus>
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

    <button type="submit" class="btn-primary">Ingresar</button>
</form>
@endsection

@section('auth-footer')
    ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
@endsection