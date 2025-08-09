@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('content')
<style>
    :root {
        --ferreteria-red: #8F001A;
        --ferreteria-blue: #052A59;
        --ferreteria-light-gray: #E7E8E7;
        --ferreteria-cream: #F4F3EB;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background-color: var(--ferreteria-cream);
    }

    /* Contenedor general del dashboard */
    .dashboard-card {
        background-color: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Botones */
    .dashboard-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        border-radius: 10px;
        text-decoration: none;
        color: white;
        font-weight: bold;
        font-size: 16px;
        transition: all 0.3s ease;
        height: 120px;
    }

    .dashboard-btn i {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .dashboard-btn.red {
        background-color: var(--ferreteria-red);
    }

    .dashboard-btn.red:hover {
        background-color: #6b0014;
    }

    .dashboard-btn.blue {
        background-color: var(--ferreteria-blue);
    }

    .dashboard-btn.blue:hover {
        background-color: #031b3a;
    }

    .dashboard-btn:hover {
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    /* Mensaje de bienvenida */
    .welcome-message {
        background: linear-gradient(135deg, var(--ferreteria-red), var(--ferreteria-blue));
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        animation: fadeInDown 1s ease-in-out;
    }

    .welcome-message h2 {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .welcome-message p {
        margin: 0;
        font-size: 1rem;
    }

    /* Animaciones */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container mt-4">


    <!-- Mensaje de bienvenida -->
    <div class="welcome-message">
        <h2>¡Bienvenido, Administrador!</h2>
        <p>Gestiona tu ferretería de forma rápida, segura y eficiente desde un solo lugar.</p>
    </div>

    <!-- Botones de módulos -->
    <div class="dashboard-card">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <a href="{{ route('ventas.index') }}" class="dashboard-btn red">
                    <i class="fas fa-cash-register"></i>
                    <div>Realizar Venta</div>
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="{{ route('corte.index') }}" class="dashboard-btn blue">
                    <i class="fas fa-calculator"></i>
                    <div>Corte de Caja</div>
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="{{ route('inventario.index') }}" class="dashboard-btn red">
                    <i class="fas fa-boxes"></i>
                    <div>Inventario</div>
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="{{ route('admin.usuarios.index') }}" class="dashboard-btn blue">
                    <i class="fas fa-users"></i>
                    <div>Usuarios</div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
