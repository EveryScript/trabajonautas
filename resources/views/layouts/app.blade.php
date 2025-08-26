<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trabajonautas') }}</title>
    <link rel="icon" href="{{ asset('storage/img/tbn-icon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=baloo-da-2:400,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google, don't index this page -->
    {{-- <meta name="robots" content="noindex, nofollow"> --}}

    {{-- Firebase SDK --}}
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

        // if ('serviceWorker' in navigator) {
        //     navigator.serviceWorker.register('/firebase-messaging-sw.js')
        //         .then(function(registration) {
        //             console.log('Service Worker registrado:', registration);
        //         });
        // }
    </script>

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-figtree antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-50">
        {{-- @livewire('navigation-menu') --}}
        <x-nav-responsive />

        <!-- Page Content -->
        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>

</html>
