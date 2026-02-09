<!DOCTYPE html>
<html>

<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@8..144,400;700&display=swap');

        body {
            font-family: 'Google Sans Flex', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .badge {
            display: inline-block;
            background-color: {{ $account_type_details['color'] }}15;
            color: {{ $account_type_details['color'] }};
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: bold;
            margin-top: 20px;
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
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('storage/img/tbn-mail-logo.png')) }}" alt="Trabajonautas"
                style="width: 180px;">
        </div>
        <div class="content">
            <h1>¡Bienvenido {{ $user->name }} 👋</h1>
            <p>Gracias por finalizar el registro de tus datos. Han sido guardados correctamente en nuestro sistema. Todo
                esta listo para mostrarte un universo de convocatorias de empleo actualizadas para profesionales de Toda
                Bolivia.</p>
            <div class="badge">{{ $account_type_details['label'] }}</div>
            <p style="font-size: 14px; font-weight: medium; color: #555555;">
                {{ $account_type_details['feature'] }}
            </p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/panel') }}" class="button">Ir al Panel</a>
            </div>
        </div>
    </div>
</body>

</html>
