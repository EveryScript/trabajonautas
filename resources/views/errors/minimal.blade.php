<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trabajonautas</title>
    <link rel="icon" href="{{ asset('storage/img/tbn-icon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="relative min-h-screen bg-gray-100 dark:bg-neutral-700 text-tbn-dark dark:text-tbn-light">
        <x-navigation-web />
        <div class="container flex flex-col items-center justify-center flex-1 px-4 py-32 mx-auto">
            <div class="w-full max-w-md text-center">
                <div class="mb-8">
                    <img src="{{ asset('storage/img/tbn-empty.webp') }}" alt="empty"
                        class="max-w-[5rem] mx-auto mb-2">
                </div>
                <h1 class="mb-4 text-6xl font-bold text-tbn-primary">@yield('code')</h1>
                <h2 class="mb-4 text-2xl font-semibold text-tbn-dark dark:text-white">@yield('title')</h2>
                <p class="mb-8 text-tbn-dark dark:text-tbn-light">
                    @yield('message')
                </p>

                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    <x-button-link class="bg-tbn-primary" href="{{ route('welcome') }}" wire:navigate>Volver a
                        inicio</x-button-link>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
