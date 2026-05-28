{{-- @extends('errors::minimal')
@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable')) --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trabajonautas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="antialiased">
    <div class="relative flex items-center justify-center w-full h-screen px-5 text-center bg-center bg-cover"
        style="background-image:url({{ asset('storage/img/tbn-space-reverse.webp') }});">
        <div class="absolute top-0 bottom-0 left-0 right-0 bg-gray-900 opacity-75"></div>
        <div class="z-50 flex flex-col justify-center w-full h-screen text-white">
            <h1 class="mb-4 text-3xl font-semibold md:text-5xl">¿Todo listo?</h1>
            <p class="mb-8">El despegue ha sido demorado por unas horas. Nuestros ingenieros están trabajando en los últimos detalles.</p>
            <picture class="mx-auto mb-4 max-w-64">
                <img class="w-full" src="{{ asset('storage/img/tbn-white-logo.webp') }}" alt="logo-white">
            </picture>
            <div class="mb-6" hidden>
                <div class="w-full max-w-2xl mx-auto mt-2 bg-white rounded-full shadow">
                    <div class="rounded-full bg-[#ff420a] text-xs leading-none text-center text-white py-1"
                        style="width: 85%">85%</div>
                </div>
            </div>
            <div class="flex flex-row gap-4 mx-auto text-2xl" hidden>
                <a href="#" class="hover:text-[#ff420a]">
                    <i class="fa-brands fa-facebook"></i>
                </a>
                <a href="#" class="hover:text-[#ff420a]">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="#" class="hover:text-[#ff420a]">
                    <i class="fa-brands fa-youtube"></i>
                </a>
            </div>
        </div>
    </div>
</body>
