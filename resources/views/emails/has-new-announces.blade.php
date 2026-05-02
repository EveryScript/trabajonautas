<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Trabajonautas</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');

        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 24px;
            border: 1px solid #f0e2e2;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .header {
            padding: 48px 40px 32px;
            text-align: center;
        }

        .content {
            padding: 0 48px 40px;
        }

        .hero-badge {
            display: inline-block;
            padding: 6px 12px;
            background-color: #fdf0f0;
            color: #ff420a;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .title {
            color: #1d1d1d;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.2;
            margin: 0 0 16px 0;
        }

        .text {
            font-size: 14px;
            line-height: 1.7;
            color: #475569;
            margin-bottom: 1rem;
        }

        .cta-box {
            background-color: #f9f2f1;
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            margin: 32px 0;
        }

        .button {
            background-color: #ff420a;
            color: #ffffff !important;
            display: inline-block;
            font-size: 16px;
            font-weight: 600;
            padding: 18px 40px;
            text-decoration: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(185, 16, 16, 0.2);
        }

        .footer {
            text-align: center;
            padding: 24px 40px;
            font-size: 13px;
            color: #94a3b8;
        }

        .footer a {
            color: #64748b;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header/Logo -->
            <div class="header">
                <img src="{{ $message->embed(public_path('storage/img/tbn-mail-logo.png')) }}" alt="Trabajonautas"
                    style="width: 200px;">
            </div>

            <!-- Cuerpo -->
            <div class="content">
                <span class="hero-badge">Misión: Nueva Vacante</span>
                <h1 class="title">¡Hola, 👩‍🚀 {{ $user->name }}!</h1>
                <p class="text">
                    Trabajonautas te informa que hoy se han lanzado nuevas convocatorias laborales que coinciden con tu
                    perfil profesional.
                </p>
                <p class="text">
                    Para que no pierdas ninguna oportunidad en tu misión hacia el éxito, te invitamos a ingresar ahora
                    mismo a tu <strong>Panel de Control</strong> y dirigirte a la sección de novedades. Allí encontrarás
                    el listado completo de las convocatorias que han entrado en órbita hoy y que aún no has explorado.
                </p>
                <p class="text">
                    No dejes pasar el tiempo: ¡tu próximo destino profesional podría estar más cerca de lo que imaginas!
                    Prepárate para el despegue 🚀
                </p>
                <div class="cta-box">
                    <a href="{{ url('/panel') }}" class="button">Ir al Panel de Control</a>
                </div>

                <p class="text" style="font-size: 14px;">
                    Prepárate para el despegue. El equipo de <strong>Trabajonautas.com</strong> te desea éxito en esta
                    misión.
                </p>
            </div>

            <div class="footer">
                <p style="margin-bottom: 10px;">&copy; {{ date('Y') }} Trabajonautas · Bolivia</p>
                <p>¿Recibiste esto por error? No te preocupes, puedes <a href="https://trabajonauas.com">ignorar este
                        mensaje</a>.</p>
            </div>
        </div>
    </div>
</body>

</html>
