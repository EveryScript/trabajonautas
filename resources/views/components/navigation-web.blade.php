<header class="bg-white dark:bg-tbn-dark h-[5rem]">
    <div class="flex flex-row justify-between h-full max-w-6xl px-4 mx-auto align-middle sm:px-6 lg:px-8">
        <div class="mt-5">
            <x-nav-logo />
        </div>
        <nav class="flex flex-wrap items-center justify-center gap-4 text-sm md:ml-auto">
            @auth
                <a href="{{ route('dashboard') }}">
                    <x-button>
                        Mi Panel
                    </x-button>
                </a>
            @else
                <a href="{{ route('login') }}" class="px-2 py-1 transition-all duration-200 text-tbn-dark dark:text-tbn-light hover:text-tbn-primary">
                    Iniciar sesión</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 text-white transition-all duration-200 rounded-md bg-tbn-primary hover:bg-tbn-secondary">
                        Registrarse</a>
                @endif
            @endauth
        </nav>
    </div>
</header>
