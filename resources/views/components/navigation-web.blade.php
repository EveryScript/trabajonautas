<header class="bg-white h-[5rem]">
    <div class="h-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-row justify-between align-middle">
        <div class="mt-5">
            <x-nav-logo />
        </div>
        <nav class="md:ml-auto flex flex-wrap items-center justify-center gap-4 text-sm">
            @auth
                <a href="{{ route('dashboard') }}" class="text-tbn-dark px-3 py-2 rounded-md bg-gray-200">
                    @role('CLIENT')
                        @switch(Auth::user()->account->account_type_id)
                            @case(1)
                                <i class="fas fa-leaf text-green-500 mr-1 text-xs"></i>
                            @break

                            @case(2)
                                @if (Auth::user()->account->verified_payment)
                                    <i class="fas fa-crown text-tbn-secondary mr-1 text-xs"></i>
                                @else
                                    <i class="fas fa-hourglass-end text-tbn-dark mr-1 text-xs"></i>
                                @endif
                            @break

                            @case(3)
                                @if (Auth::user()->account->verified_payment)
                                    <i class="fas fa-crown text-tbn-secondary mr-1 text-xs"></i>
                                @else
                                    <i class="fas fa-hourglass-end text-tbn-dark mr-1 text-xs"></i>
                                @endif
                            @break
                        @endswitch
                        {{ Auth::user()->name }}
                    @endrole
                    @role('ADMIN')
                        <i class="fas fa-user-cog text-tbn-dark mr-1 text-xs"></i> {{ Auth::user()->name }}
                    @endrole
                    @role('USER')
                        <i class="fas fa-user text-tbn-dark mr-1 text-xs"></i> {{ Auth::user()->name }}
                    @endrole
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
