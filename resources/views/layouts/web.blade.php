<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trabajonautas') }}</title>
    <link rel="icon" href="{{ asset('storage/img/tbn-new-icon.webp') }}">

    <!-- Scripts & css -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Styles -->
    @livewireStyles
    <style>
        #nprogress .bar {
            background: #ff420a !important;
            border: none !important;
            box-shadow: none !important;
        }
    </style>

    <!-- Splide Carousel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <!-- AOS Animations -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            AOS.init();
        });
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', (el, component) => {
                AOS.refresh();
            });
        });
    </script>

</head>

<body class="antialiased font-figtree bg-gray-50">
    <!-- NavBar -->
    <x-navigation-web />
    {{-- <x-nav-responsive /> --}}
    <!-- Main Content -->
    <main class="bg-white dark:bg-tbn-dark">
        {{ $slot }}
    </main>
    <!-- Footer Content -->
    <footer class="px-5 py-20 bg-tbn-secondary body-font">
        <picture class="block max-w-6xl mx-auto mb-5">
            <img class="max-w-[16rem]" src="{{ asset('storage/img/tbn-white.webp') }}" alt="tbn-logo">
        </picture>
        @livewire('web.footer-data')
    </footer>
    @livewireScripts
</body>

</html>
