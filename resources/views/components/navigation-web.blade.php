<header class="bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-row justify-between align-middle">
        <div class="mt-4">
            <x-nav-logo />
        </div>
        <nav class="md:ml-auto flex flex-wrap items-center justify-center gap-4 text-sm h-16">
            @auth
                <a href="{{ route('dashboard') }}" class="text-tbn-dark px-3 py-2 rounded-md bg-gray-200">
                    <i class="fas fa-user pr-1 text-xs"></i> {{ Auth::user()->name }}
                </a>
            @else
                <a href="{{ route('login') }}" class=" hover:text-tbn-primary px-2 py-1">
                    Iniciar sesión</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="text-white bg-tbn-primary hover:bg-tbn-dark px-4 py-2 rounded-md">
                        Registrarse</a>
                @endif
            @endauth
        </nav>
    </div>
</header>
