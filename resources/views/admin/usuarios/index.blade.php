@extends('layouts.app')

@section('title', 'Administrar Usuarios')

@section('content')

<style>
    :root {
        --ferreteria-blue: #052A59;
        --ferreteria-red: #8F001A;
    }

    /* Encabezado azul y letras blancas */
    .table-ferreteria thead,
    .table-ferreteria thead th {
        background-color: var(--ferreteria-blue) !important;
        color: #ffffff !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    .table-ferreteria thead th * {
        color: #ffffff !important;
    }

    /* Botón editar */
    .btn-editar {
        background-color: var(--ferreteria-blue);
        color: #fff;
        border: none;
    }
    .btn-editar:hover {
        background-color: #07335f;
        color: #fff;
    }

    /* Botón dar de baja */
    .btn-baja {
        background-color: var(--ferreteria-red);
        color: #fff;
        border: none;
    }
    .btn-baja:hover {
        background-color: #b30022;
        color: #fff;
    }

    /* Espacio inferior para que el contenido no quede tapado por el botón fijo */
    .container {
        padding-bottom: 100px;
    }

    /* Botón fijo */
    .boton-fijo {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: var(--ferreteria-blue);
        color: #fff;
        border: none;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(5,42,89,0.16);
        z-index: 2100;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: transform .12s ease, background-color .12s ease, box-shadow .12s ease;
    }
    .boton-fijo:hover {
        transform: translateY(-3px);
        background-color: #07335f;
        box-shadow: 0 12px 28px rgba(5,42,89,0.20);
        color: #fff;
    }

    @media (max-width: 576px) {
        .boton-fijo {
            right: 12px;
            bottom: 12px;
            padding: 10px 12px;
            font-size: 14px;
        }
    }
</style>

<div class="container">
    <h1 class="h2 fw-bold text-white bg-primary p-3 rounded mb-4">Gestión de Usuarios</h1>

    <!-- Botones de filtro -->
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('admin.usuarios.index', ['estatus' => 'activo']) }}"
           class="btn btn-success {{ request('estatus') !== 'baja' ? 'active' : '' }}">
            Activos
        </a>
        <a href="{{ route('admin.usuarios.index', ['estatus' => 'baja']) }}"
           class="btn btn-secondary {{ request('estatus') === 'baja' ? 'active' : '' }}">
            Baja
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tabla de usuarios -->
    <table class="table table-bordered table-striped table-ferreteria">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estatus</th>
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
                        @if($usuario->estatus === 'activo')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Baja</span>
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->id_usuario !== $usuario->id_usuario)
                            <a href="{{ route('admin.usuarios.edit', $usuario->id_usuario) }}" class="btn btn-sm btn-editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            @if($usuario->estatus === 'activo')
                                <form action="{{ route('admin.usuarios.destroy', $usuario->id_usuario) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-baja" onclick="return confirm('¿Deseas dar de baja este usuario?')">
                                        <i class="fas fa-user-slash"></i> Dar de baja
                                    </button>
                                </form>
                            @elseif($usuario->estatus === 'baja')
                                <form action="{{ route('admin.usuarios.restore', $usuario->id_usuario) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Deseas dar de alta nuevamente este usuario?')">
                                        <i class="fas fa-user-check"></i> Dar de alta
                                    </button>
                                </form>
                            @endif
                        @else
                            <span class="text-muted">Usuario en sesión</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Botón fijo -->
<a href="{{ route('admin.usuarios.create') }}" class="boton-fijo" title="Nuevo Usuario">
    <i class="fas fa-plus"></i>
    Nuevo Usuario
</a>

@endsection
