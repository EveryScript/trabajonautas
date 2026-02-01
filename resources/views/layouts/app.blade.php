<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trabajonautas') }}</title>
    <link rel="icon" href="{{ asset('storage/img/tbn-icon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyBKn9I--sruLzQFQVgAayPMZuTt6tKU8A8",
            authDomain: "trabajonautas-notifications.firebaseapp.com",
            projectId: "trabajonautas-notifications",
            storageBucket: "trabajonautas-notifications.firebasestorage.app",
            messagingSenderId: "888362496290",
            appId: "1:888362496290:web:04394616572da440fef57b",
            measurementId: "G-FTYMBGYPTP"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
    </script>

    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Quill Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <!-- Flatpickr (datetime inputs) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_orange.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Mask (Alpine plugin) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    @livewireStyles
    <style>
        #nprogress .bar {
            background: #ff420a !important;
            border: none !important;
            box-shadow: none !important;
        }
    </style>
</head>

<body class="antialiased font-figtree">
    <x-banner />

    <div class="min-h-screen bg-gray-50 dark:bg-[#333333]">
        {{-- @livewire('navigation-menu') --}}
        <x-nav-responsive />

        <!-- Page Content -->
        <main class="max-w-6xl px-3 py-6 mx-auto sm:px-6">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>

</html>
