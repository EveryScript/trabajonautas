<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@8..144,400;700&display=swap');

        /* Estilos base para clientes de correo */
        body {
            font-family: 'Google Sans Flex', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 40px 0;
        }

        .container {
            max-width: 570px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #e8e8e8;
            overflow: hidden;
        }

        .header {
            padding: 25px;
            text-align: center;
            background-color: #ffffff;
        }

        .content {
            padding: 30px 40px;
        }

        .content h1 {
            color: #333333;
            font-size: 22px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #51545e;
            text-align: left;
        }

        .button-container {
            text-align: center;
            padding: 30px 0;
        }

        .button {
            background-color: #ff420a;
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            padding: 14px 30px;
            text-decoration: none;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
        }

        .footer {
            padding: 25px;
            text-align: center;
            font-size: 12px;
            color: #b0adc5;
        }

        .sub-text {
            font-size: 12px;
            color: #74787e;
            margin-top: 25px;
            border-top: 1px solid #e8e8e8;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="{{ $message->embed(public_path('storage/img/tbn-mail-logo.png')) }}" alt="Trabajonautas"
                    style="width: 180px;">
            </div>

            <div class="content">
                <h1>Hola, {{ $user->name }} 👋</h1>
                <p>Bienvenido a Trabajonautas.com, el portal No. 1 en convocatorias de empleo para profesionales de toda
                    Bolivia.</p>
                <p>Haz click en el botón de abajo para verificar el email y continuar con el registro de tu cuenta.</p>

                <div class="button-container">
                    <a href="{{ $url }}" class="button">Verificar email ahora</a>
                </div>

                <p>Si no realizaste esta acción, puedes ignorar este correo de forma segura.</p>

                <p class="sub-text">
                    Si tienes problemas con el botón, copia y pega esta URL en tu navegador: <br>
                    <span style="word-break: break-all; color: #ff420a;">{{ $url }}</span>
                </p>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Trabajonautas. Todos los derechos reservados.<br>
                Visítanos en <a href="https://trabajonautas.com" style="color: #b0adc5;">trabajonautas.com</a>
            </div>
        </div>
    </div>
</body>

</html>
