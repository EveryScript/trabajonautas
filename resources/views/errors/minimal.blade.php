<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trabajonautas</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="relative min-h-screen">
        <x-navigation-web />
        <div class="w-full flex flex-col justify-center items-center gap-1 mt-24">
            <x-nav-logo class="mb-1" />
            <p class="text-center text-sm font-normal mb-5">La mejor plataforma de oportunidades de empleo para toda
                Bolivia.</p>
            <div class="text-tbn-dark text-center">
                <p class="text-6xl font-bold mb-4">@yield('code')</p>
                <p class="text-lg">@yield('message')</p>
            </div>
        </div>
    </div>
</body>

</html>
