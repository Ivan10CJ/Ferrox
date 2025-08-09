<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ferretería Ferros - @yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    @if (request()->routeIs('corte.index'))
        @if (file_exists(public_path('css/app.css')))
            <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @endif
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

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
            margin: 0;
            padding: 0;
        }

        /* ===== Menú lateral ===== */
        .sidebar {
            width: 240px; /* <-- CAMBIA este valor para hacerlo más ancho */
            background-color: var(--ferreteria-blue);
            position: fixed;
            top: 0;
            bottom: 0;
            padding: 20px;
            color: white;
            transition: transform 0.3s ease-in-out;
            z-index: 1050;
        }

        .sidebar.hide {
            transform: translateX(-100%);
        }

        .sidebar h4 {
            margin-bottom: 30px;
            font-size: 20px;
        }

        .sidebar .nav-link {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 16px;
            font-size: 17px;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: var(--ferreteria-red);
            transform: translateX(5px) scale(1.02);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .sidebar .nav-link.active {
            background-color: var(--ferreteria-red);
            font-weight: bold;
        }

        /* ===== Encabezado ===== */
        .header {
            margin-left: 240px;
            background-color: var(--ferreteria-light-gray);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: margin-left 0.3s ease-in-out;
        }

        .header.full {
            margin-left: 0;
        }

        /* ===== Contenido ===== */
        .content {
            margin-left: 240px;
            padding: 40px 30px;
            transition: margin-left 0.3s ease-in-out;
        }

        .content.full {
            margin-left: 0;
        }

        /* ===== Botón de salir ===== */
        .btn-logout {
            background-color: var(--ferreteria-red);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background-color: #6b0014;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* ===== Usuario ===== */
        .user-info {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            font-size: 15px;
        }

        .user-text p {
            margin: 0;
            font-weight: 400;
            font-size: 14px;
            color: #ccc;
        }

        .user-text strong {
            color: #fff;
            font-size: 16px;
        }

        /* ===== Colores ===== */
        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
        }

        .bg-primary {
            background-color: #052A59 !important;
        }

        .btn-danger {
            background-color: #8F001A !important;
            border-color: #8F001A !important;
        }

        .bg-light {
            background-color: #E7E8E7 !important;
        }
        /* Botón toggle en azul */
        #toggleMenu {
            background-color: #052A59; /* Azul principal */
            border-color: #052A59;
            color: white;
        }

        #toggleMenu:hover {
            background-color: #0b5ed7; /* Azul más oscuro en hover */
            border-color: #0b5ed7;
        }
    </style>
</head>

<body>

    <!-- ===== Menú lateral ===== -->
    <div class="sidebar" id="sidebarMenu">
        <div class="user-info mb-4">
            <i class="fa fa-user-circle fa-2x me-2"></i>
            <div class="user-text">
                <p class="mb-0">Bienvenido</p>
                <strong>{{ auth()->user()->nombre_completo }}</strong>
                <p class="mb-0">
                    Rol: 
                    @if(auth()->user()->rol->id_rol === 'ADMIN')
                        Administrador
                    @elseif(auth()->user()->rol->id_rol === 'EMPLEA')
                        Empleado
                    @else
                        Otro
                    @endif
                </p>
            </div>
        </div>

        @if(auth()->user()->rol->id_rol === 'ADMIN')
            <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}">
                <i class="fas fa-cash-register me-2"></i> Realizar Venta
            </a>
            <a href="{{ route('corte.index') }}" class="nav-link {{ request()->routeIs('corte.index') ? 'active' : '' }}">
                <i class="fas fa-calculator me-2"></i> Corte de Caja
            </a>
            <a href="{{ route('inventario.index') }}" class="nav-link {{ request()->routeIs('inventario.index') ? 'active' : '' }}">
                <i class="fas fa-boxes me-2"></i> Inventario
            </a>
            <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.index') ? 'active' : '' }}">
                <i class="fas fa-users me-2"></i> Usuarios
            </a>
        @elseif(auth()->user()->rol->id_rol === 'EMPLEA')
            <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}">
                <i class="fas fa-cash-register me-2"></i> Realizar Venta
            </a>
            <a href="{{ route('inventario.index') }}" class="nav-link {{ request()->routeIs('inventario.index') ? 'active' : '' }}">
                <i class="fas fa-boxes me-2"></i> Inventario
            </a>
        @endif

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Salir
            </button>
        </form>
    </div>

 <!-- ===== Encabezado ===== -->
<div class="header shadow-sm d-flex align-items-center justify-content-between p-2 position-relative" id="headerContent">
    <div class="d-flex align-items-center">
        <button class="btn btn-sm btn-primary me-3 text-white" id="toggleMenu">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <h2 class="h4 fw-bold text-ferreteria-blue mb-0 titulo-centrado">
        Ferretería Ferros - Sistema de Gestión Interna
    </h2>

    <span class="text-muted">Usuario: {{ auth()->user()->nombre_completo }}</span>
</div>


    <!-- ===== Contenido ===== -->
    <div class="content" id="mainContent">
        @yield('content')
    </div>

    @if (!request()->routeIs('corte.index'))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endif

    <script>
        document.getElementById('toggleMenu').addEventListener('click', function () {
            document.getElementById('sidebarMenu').classList.toggle('hide');
            document.getElementById('headerContent').classList.toggle('full');
            document.getElementById('mainContent').classList.toggle('full');
        });
    </script>

    @stack('scripts')
</body>
</html>
