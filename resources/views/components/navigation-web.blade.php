<header class="bg-white h-[5rem]">
    <div class="h-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-row justify-between align-middle">
        <div class="mt-5">
            <x-nav-logo />
        </div>
        <nav class="md:ml-auto flex flex-wrap items-center justify-center gap-4 text-sm">
            @auth
                <a href="{{ route('dashboard') }}" class="px-3 py-2 font-semibold text-white bg-tbn-primary rounded-md focus:ring-2">
                    Mi Panel
                </a>
            @else
                <a href="{{ route('login') }}" class=" hover:text-tbn-primary transition-all duration-200 px-2 py-1">
                    Iniciar sesión</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="text-white bg-tbn-primary hover:bg-tbn-dark transition-all duration-200 px-4 py-2 rounded-md">
                        Registrarse</a>
                @endif
            @endauth
        </nav>
    </div>
</header>
