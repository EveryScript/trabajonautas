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
    <div class="relative h-screen w-full flex items-center justify-center bg-cover bg-center text-center px-5"
        style="background-image:url({{ asset('storage/img/tbn-space-reverse.webp') }});">
        <div class="absolute top-0 right-0 bottom-0 left-0 bg-gray-900 opacity-75"></div>
        <div class="z-50 flex flex-col justify-center text-white w-full h-screen">
            <h1 class="text-3xl md:text-5xl mb-4">Estamos listos para despegar al futuro</h1>
            <p class="mb-8">Próximamente, las mejores convocatorias de empleo para toda Bolivia.</p>
            <picture class="mx-auto max-w-64 mb-4">
                <img class="w-full" src="{{ asset('storage/img/tbn-white-logo.webp') }}" alt="logo-white">
            </picture>
            <div class="mb-6">
                <div class="shadow w-full bg-white mt-2 max-w-2xl mx-auto rounded-full">
                    <div class="rounded-full bg-tbn-primary text-xs leading-none text-center text-white py-1"
                        style="width: 85%">85%</div>
                </div>
            </div>
            <div class="flex flex-row gap-4 text-tbn-primary mx-auto text-2xl">
                <a href="#">
                    <i class="fa-brands fa-facebook"></i>
                </a>
                <a href="#">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="#">
                    <i class="fa-brands fa-youtube"></i>
                </a>
            </div>
        </div>
    </div>
</body>
