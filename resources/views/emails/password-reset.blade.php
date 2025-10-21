<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            background-color: #bebebe;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #dcdcdc;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 200px;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .content h1 {
            color: #034b8d;
        }
        .button {
            display: inline-block;
            margin: 25px 0;
            padding: 12px 24px;
            background-color: #034b8d;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
        }
        .footer {
            background: #f1f1f1;
            padding: 15px;
            text-align: center;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('storage/img/tbn-logo.png') }}" alt="Logo de la empresa">
    </div>
    <div class="content">
        <h1>Hola, {{ $user->name }} 👋</h1>
        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
        <p>Haz clic en el siguiente botón para continuar:</p>
        <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
        <p>Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} trabajonautas.com | Todos los derechos reservados.
    </div>
</div>
</body>
</html>
