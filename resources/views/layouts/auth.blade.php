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
            background-color: #072b58;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-container {
            background-color: #072b58;
            color: white;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            box-sizing: border-box;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header i {
            font-size: 2rem;
            margin: 0 1rem;
        }

        .auth-title {
            font-size: 1.4rem;
            font-weight: bold;
            margin-top: 1rem;
        }

        .auth-subtitle {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: white;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 2.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #000;
        }

        .input-icon input {
            padding-left: 2.5rem;
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: #a10c1e;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #870a19;
        }

        .auth-footer {
            margin-top: 2rem;
            text-align: center;
            color: #ccc;
        }

        .auth-footer a {
            color: #fff;
            text-decoration: underline;
        }

        .error-message {
            color: #ffaaaa;
            font-size: 0.875rem;
            margin-top: 0.25rem;
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
