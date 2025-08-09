<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferretería Ferros - @yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:rgb(207, 205, 197); /* azul en el fondo general */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-container {
            background-color: #ffffff; /* centro blanco */
            color: #2c2c2c;
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header i {
            font-size: 1.8rem;
            margin: 0 0.5rem;
            color: #072b58; /* íconos en azul oscuro */
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 0.8rem;
            color: #072b58;
        }

        .auth-subtitle {
            font-size: 1rem;
            margin-bottom: 2rem;
            color: #555;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 2.5rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            background-color: #f9f9f9;
            color: #333;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .input-icon input {
            padding-left: 2.5rem;
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: #072b58;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #870a19;
        }

        .auth-footer {
            margin-top: 2rem;
            text-align: center;
            color: #555;
            font-size: 0.95rem;
        }

        .auth-footer a {
            color: #a10c1e;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #d9534f;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-hammer"></i>
            <i class="fas fa-wrench"></i>
            <div class="auth-title">Ferretería Ferros</div>
            <div class="auth-subtitle">Sistema de Gestión Interna</div>
        </div>

        @yield('content')

        <div class="auth-footer">
            @yield('auth-footer')
        </div>
    </div>
</body>
</html>
