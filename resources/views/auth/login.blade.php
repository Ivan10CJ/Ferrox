@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')


<!-- Formulario de Login -->
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

    <div class="form-group form-check">
        <label class="form-check-label" for="privacyCheck">
            He leído y acepto el <a href="{{ asset('docs/aviso_privacidad.pdf') }}" target="_blank">Aviso de Privacidad</a>
        </label>
        
        <input type="checkbox" class="form-check-input" id="privacyCheck" required>
    </div>

    <button type="submit" class="btn-primary">Ingresar</button>
</form>
@endsection