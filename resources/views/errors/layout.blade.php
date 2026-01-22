<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Trabajonautas @yield('title')</title>
        <link rel="icon" href="{{ asset('storage/img/tbn-icon.ico') }}">
    </head>
    <body>
        <div class="bg-gray-100 flex-center position-ref full-height dark:bg-neutral-800 text-tbn-dark dark:text-tbn-light">
            <div class="content">
                <div class="title">
                    @yield('message')
                </div>
            </div>
        </div>
    </body>
</html>
