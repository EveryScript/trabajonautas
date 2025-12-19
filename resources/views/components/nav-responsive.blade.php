<header class="bg-white h-[5rem]">
<nav class="max-w-6xl h-full mx-auto px-4 sm:px-6 lg:px-8 flex flex-row justify-between align-middle">
    <div x-data="{ open: false, dropdown: false }" @click.away="open = false, dropdown = false"
        class="w-full flex flex-wrap items-center justify-between mx-auto relative">
        @role(env('CLIENT_ROLE'))
            <x-nav-logo />
        @endrole
        @role([env('USER_ROLE'), env('ADMIN_ROLE')])
            <a href="{{ route('welcome') }}" wire:navigate>
                <img class="inline-block max-w-12" src="{{ asset('storage/img/tbn-new-isologo.webp') }}" alt="tbn-logo">
            </a>
        @endrole
        <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse relative">
            @auth
                <!-- Dropdown button -->
                <button @click="open = !open" type="button" class="flex text-sm bg-gray-800 rounded-full md:me-0"
                    id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                    data-dropdown-placement="bottom">
                    <span class="px-3 py-2 font-semibold text-white bg-tbn-primary rounded-md focus:ring-2">
                        Mi Panel
                    </span>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open"
                    class="absolute top-6 right-0 z-50 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow"
                    id="user-dropdown">
                    <div class="px-4 py-3">
                        <span class="block text-sm text-gray-900">{{ Auth::user()->name }}</span>
                        <span class="block text-sm  text-gray-500 truncate">{{ Auth::user()->email }}</span>
                    </div>
                    <ul class="py-2" aria-labelledby="user-menu-button">
                        <li>
                            <a href="{{ route('dashboard') }}" wire:navigate
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Mi panel</a>
                        </li>
                        <li class="hidden">
                            <a href="{{ route('profile.show') }}" wire:navigate
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Configuración</a>
                        </li>
                        <li>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <a href="{{ route('logout') }}" @click.prevent="$root.submit();"
                                    class="block px-4 py-2 text-sm text-red-500 hover:bg-gray-100">
                                    Cerrar sesión</a>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" wire:navigate class="text-sm hover:text-tbn-primary px-2 py-1 mr-3">
                    Iniciar sesión</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" wire:navigate
                        class="text-sm px-3 py-2 font-bold text-white bg-tbn-primary rounded-lg focus:ring-2 ring-tbn-primary">Registrarse</a>
                @endif
            @endauth
            @auth
                @role('USER|ADMIN')
                    <!-- Hamburger -->
                    <button @click="dropdown = !dropdown" data-collapse-toggle="navbar-user" type="button"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                        aria-controls="navbar-user" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 17 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1h15M1 7h15M1 13h15" />
                        </svg>
                    </button>
                @endrole
            @endauth
        </div>
        @auth
            <div x-bind:class="dropdown ? '' : 'hidden'"
                class="items-center justify-between w-full md:flex md:w-auto md:order-1" id="navbar-user">
                <ul
                    class="flex flex-col font-medium text-sm p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white">
                    @role('USER|ADMIN')
                        <x-nav-link-responsive href="{{ route('dashboard') }}" wire:navigate :active="request()->routeIs('dashboard')">
                            <span class="sm:block md:hidden lg:block">{{ __('Dashboard') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-home text-md"></i>
                        </x-nav-link-responsive>
                        <x-nav-link-responsive href="{{ route('announcement') }}" wire:navigate :active="request()->routeIs('announcement')">
                            <span class="sm:block md:hidden lg:block">{{ __('Convocatorias') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-scroll text-md"></i>
                        </x-nav-link-responsive>
                        <x-nav-link-responsive href="{{ route('company') }}" wire:navigate :active="request()->routeIs('company')">
                            <span class="sm:block md:hidden lg:block">{{ __('Empresas') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-building text-md"></i>
                        </x-nav-link-responsive>
                        <x-nav-link-responsive href="{{ route('profesions') }}" wire:navigate :active="request()->routeIs('profesions')">
                            <span class="sm:block md:hidden lg:block">{{ __('Profesiones') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-graduation-cap text-md"></i>
                        </x-nav-link-responsive>
                        <x-nav-link-responsive href="{{ route('client') }}" wire:navigate :active="request()->routeIs('client')">
                            <span class="sm:block md:hidden lg:block">{{ __('Clientes') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-users text-md"></i>
                        </x-nav-link-responsive>
                    @endrole
                    @role('ADMIN')
                        <x-nav-link-responsive href="{{ route('area') }}" wire:navigate :active="request()->routeIs('area')">
                            <span class="sm:block md:hidden lg:block">{{ __('Areas') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-suitcase text-md"></i>
                        </x-nav-link-responsive>
                        <x-nav-link-responsive href="{{ route('report') }}" wire:navigate :active="request()->routeIs('report')">
                            <span class="sm:block md:hidden lg:block">{{ __('Reportes') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-clipboard-list text-md"></i>
                        </x-nav-link-responsive>
                        <x-nav-link-responsive href="{{ route('user') }}" wire:navigate :active="request()->routeIs('user')">
                            <span class="sm:block md:hidden lg:block">{{ __('Usuarios') }}</span>
                            <i class="sm:hidden md:block lg:hidden fas fa-users-cog text-md"></i>
                        </x-nav-link-responsive>
                    @endrole
                </ul>
            </div>
        @endauth
    </div>
</nav>
</header>