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
        <div class="container mx-auto px-4 py-32 flex-1 flex flex-col items-center justify-center">
            <div class="max-w-md w-full text-center">
                <div class="mb-8">
                    <img src="{{ asset('storage/img/tbn-empty.webp') }}" alt="empty"
                        class="max-w-[5rem] mx-auto mb-2">
                </div>
                <h1 class="text-5xl font-bold mb-4 text-tbn-primary">@yield('code')</h1>
                <h2 class="text-2xl font-semibold mb-4">@yield('title')</h2>
                <p class="mb-8 text-gray-600 dark:text-gray-400">
                    @yield('message')
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <x-button-link href="{{ route('welcome') }}" wire:navigate>Volver a inicio</x-button-link>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
