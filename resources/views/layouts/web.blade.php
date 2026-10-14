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
    <!-- Firebase Foreground Notifications -->
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>
    <script src="{{ asset('js/firebase-notification.js') }}"></script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', {{ config('services.google_analytics.id') }});
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Flatpickr (datetime inputs) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_orange.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

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
            // Google Analytics in Livewire (navigate)
            if (typeof gtag === 'function') {
                gtag('config', '{{ config('services.google_analytics.id') }}', {
                    'page_path': window.location.pathname
                });
            }
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
    <!-- Scroll to up button -->
    <button id="scrollTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'});"
        class="fixed bottom-6 right-6 z-50 p-3 bg-white dark:bg-[#242424] text-[#ff420a] border border-slate-200 dark:border-neutral-700 rounded-2xl shadow-xl opacity-0 translate-y-4 pointer-events-none transition-all duration-300 hover:bg-[#ff420a] hover:text-white dark:hover:bg-[#ff420a] dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-[#ff420a]"
        aria-label="Volver arriba">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
        </svg>
    </button>
    <!-- Footer Content -->
    <livewire:web.footer-data />
    <!-- Scripts -->
    @livewireScripts
</body>
<script>
    // Scroll to up arrow
    const scrollBtn = document.getElementById('scrollTopBtn');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 200) {
                scrollBtn.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
                scrollBtn.classList.add('opacity-100', 'translate-y-0');
            } else {
                scrollBtn.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                scrollBtn.classList.remove('opacity-100', 'translate-y-0');
            }
        });
    }
</script>

</html>
