<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferretería Ferros - @yield('title')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #F4F3EB;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 220px;
            background-color: #052A59;
            position: fixed;
            top: 0;
            bottom: 0;
            padding: 20px;
            color: white;
        }

        .sidebar h4 {
            margin-bottom: 30px;
            font-size: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .sidebar a:hover {
            text-decoration: underline;
        }

        .header {
            margin-left: 220px;
            background-color: #E7E8E7;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            margin-left: 220px;
            padding: 30px;
        }

        .btn-logout {
            background-color: #8F001A;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-logout:hover {
            background-color: #6b0014;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h4>Bienvenido,<br> {{ auth()->user()->nombre_completo }}</h4>

        @if(auth()->user()->rol->id_rol === 'ADMIN')
            <a href="#">Productos</a>
            <a href="#">Inventario</a>
            <li class="nav-item"><a href="{{ route('ventas.index') }}" class="nav-link text-white">Realizar Venta</a></li>
            <a href="#">Corte de Caja</a>
        @elseif(auth()->user()->rol->id_rol === 'EMPLEA')
            <li class="nav-item"><a href="{{ route('ventas.index') }}" class="nav-link text-white">Realizar Venta</a></li>
            <p><strong>Rol:</strong> Empleado</p>
        @endif

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout w-100 mt-4">Salir</button>
        </form>
    </div>

    <div class="header">
        <h2>Ferretería Ferros - Sistema de Gestión Interna</h2>
        <span>Usuario: {{ auth()->user()->nombre_completo }}</span>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
