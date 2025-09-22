<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trabajonautas') }}</title>
    <link rel="icon" href="{{ asset('storage/img/tbn-icon.ico') }}">

    <!-- Scripts & css -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=baloo-da-2:400,700" rel="stylesheet" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-figtree antialiased bg-gray-50">
    <!-- NavBar -->
    <x-navigation-web />
    {{-- <x-nav-responsive /> --}}
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    <!-- Footer Content -->
    <footer class="bg-tbn-primary body-font">
        <div
            class="max-w-6xl px-5 py-16 md:py-24 mx-auto flex items-start md:flex-row md:flex-nowrap flex-wrap flex-col">
            <div class="w-full md:w-64 px-5 flex-shrink-0 text-white">
                <picture class="block mb-4">
                    <img class="md:max-w-[12rem] max-w-24" src="{{ asset('storage/img/tbn-logo-white.webp') }}"
                        alt="tbn-logo">
                </picture>
                <p class="text-sm text-tbn-secondary my-2">&copy; {{ now()->year }} - Todos los derechos reservados
                </p>
                <h5 class="font-bold">Contactos</h5>
                <p class="text-sm my-1 font-normal">CEO Ricardo Carlos Oropeza Zárate - 76543210</p>
                <p class="text-sm my-1 font-normal">CFO Carla Ximena Vargas Soto de Oropeza - 76543210</p>
            </div>
            @livewire('web.footer-data')
        </div>
    </footer>
    @livewireScripts
</body>

</html>
