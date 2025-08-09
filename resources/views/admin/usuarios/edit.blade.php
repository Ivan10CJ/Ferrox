@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Editar Usuario</h2>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Por favor corrige los siguientes errores:</h5>
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

                        <!-- Campo Nombre Completo -->
                        <div class="mb-3">
                            <label for="nombre_completo" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre_completo') is-invalid @enderror" 
                                   name="nombre_completo" id="nombre_completo"
                                   value="{{ old('nombre_completo', $usuario->nombre_completo) }}" required>
                            @error('nombre_completo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo Nombre de Usuario -->
                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre de usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre_usuario') is-invalid @enderror" 
                                   name="nombre_usuario" id="nombre_usuario"
                                   value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" required>
                            @error('nombre_usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campos de contraseña (siempre visibles) -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva contraseña (opcional)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" id="password">
                            <small class="form-text text-muted">
                                Dejar en blanco si no deseas cambiar la contraseña. Debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.
                            </small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   name="password_confirmation" id="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo Rol -->
                        <div class="mb-3">
                            <label for="id_rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select name="id_rol" id="id_rol" class="form-select @error('id_rol') is-invalid @enderror" required>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id_rol }}" {{ old('id_rol', $usuario->id_rol) == $rol->id_rol ? 'selected' : '' }}>
                                        {{ $rol->nombre_rol }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-check-circle"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
// Validación de fortaleza de contraseña en tiempo real (opcional)
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthBadge = document.createElement('span');
    strengthBadge.className = 'badge ms-2';
    passwordInput.parentNode.appendChild(strengthBadge);
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (!password) {
            strengthBadge.textContent = '';
            strengthBadge.className = 'badge ms-2';
            return;
        }
        
        let score = 0;
        // Lógica para calcular fortaleza...
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        const strengthText = ['Muy débil', 'Débil', 'Moderada', 'Fuerte', 'Muy fuerte'][score - 1] || '';
        const strengthClass = ['danger', 'warning', 'info', 'success', 'success'][score - 1] || '';
        
        strengthBadge.textContent = strengthText;
        strengthBadge.className = 'badge bg-' + strengthClass + ' ms-2';
    });
});
</script>
@endsection