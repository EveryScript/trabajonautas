<header class="py-4 bg-white dark:bg-tbn-dark">
    <nav class="flex flex-row justify-between h-full max-w-6xl px-4 mx-auto align-middle sm:px-6 lg:px-8">
        <div x-data="{ open: false, dropdown: false }" @click.away="open = false, dropdown = false"
            class="relative flex flex-wrap items-center justify-between w-full mx-auto">
            @role(env('CLIENT_ROLE'))
                <x-nav-logo />
            @endrole
            @role([env('USER_ROLE'), env('ADMIN_ROLE')])
                <a href="{{ route('welcome') }}" wire:navigate>
                    <img class="inline-block dark:hidden max-w-12" src="{{ asset('storage/img/tbn-new-isologo.webp') }}"
                        alt="tbn-logo">
                    <img class="hidden dark:inline-block max-w-12" src="{{ asset('storage/img/tbn-white-isologo.webp') }}"
                        alt="tbn-logo">
                </a>
            @endrole
            <div class="relative flex items-center space-x-3 md:order-2 md:space-x-0 rtl:space-x-reverse">
                @auth
                    <!-- Dropdown button -->
                    <button x-on:click="open = !open" type="button" class="flex text-sm bg-gray-800 rounded-full md:me-0"
                        id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                        data-dropdown-placement="bottom">
                        <span class="px-3 py-2 font-semibold text-white rounded-md bg-tbn-primary focus:ring-2">
                            Mi Panel
                        </span>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open"
                        class="absolute right-0 z-50 my-4 text-base list-none bg-white border divide-y divide-gray-100 rounded-lg shadow top-6 dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary dark:divide-tbn-light"
                        id="user-dropdown">
                        <div class="px-4 py-3">
                            <span
                                class="block text-sm font-medium text-tbn-secondary dark:text-white">{{ Auth::user()->name }}</span>
                            <span
                                class="block text-sm truncate text-tbn-secondary dark:text-tbn-light">{{ Auth::user()->email }}</span>
                        </div>
                        <ul class="divide-y divide-tbn-light dark:divide-tbn-secondary" aria-labelledby="user-menu-button">
                            <li>
                                <a href="{{ route('dashboard') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-tbn-secondary dark:text-tbn-light hover:bg-tbn-light dark:hover:bg-neutral-900">
                                    Mi panel</a>
                            </li>
                            <li>
                                <a href="{{ route('profile.show') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-tbn-secondary dark:text-tbn-light hover:bg-tbn-light dark:hover:bg-neutral-900">
                                    Configuración</a>
                            </li>
                            <li>
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <a href="{{ route('logout') }}" @click.prevent="$root.submit();"
                                        class="block px-4 py-2 text-sm rounded-b-lg text-tbn-primary hover:bg-tbn-light dark:hover:bg-neutral-900">
                                        Cerrar sesión</a>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="px-2 py-1 mr-3 text-sm hover:text-tbn-primary">
                        Iniciar sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" wire:navigate
                            class="px-3 py-2 text-sm font-bold text-white rounded-lg bg-tbn-primary focus:ring-2 ring-tbn-primary">Registrarse</a>
                    @endif
                @endauth
                @auth
                    @role('USER|ADMIN')
                        <!-- Hamburger -->
                        <button x-on:click="dropdown = !dropdown" data-collapse-toggle="navbar-user" type="button"
                            class="inline-flex items-center justify-center w-10 h-10 p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
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
                    class="items-center justify-between w-full bg-white dark:bg-tbn-dark md:flex md:w-auto md:order-1"
                    id="navbar-user">
                    <ul x-transition:enter.duration.300ms x-transition:leave.duration.300ms
                        class="flex flex-col mt-4 text-sm font-medium rounded-lg text-tbn-secondary dark:text-tbn-light md:p-0 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0">
                        @role('USER|ADMIN')
                            <x-nav-link-responsive href="{{ route('dashboard') }}" wire:navigate :active="request()->routeIs('dashboard')">
                                <span class="sm:block md:hidden lg:block">{{ __('Dashboard') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-home text-md"></i>
                            </x-nav-link-responsive>
                            <x-nav-link-responsive href="{{ route('announcement') }}" wire:navigate :active="request()->routeIs('announcement')">
                                <span class="sm:block md:hidden lg:block">{{ __('Convocatorias') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-scroll text-md"></i>
                            </x-nav-link-responsive>
                            <x-nav-link-responsive href="{{ route('company') }}" wire:navigate :active="request()->routeIs('company')">
                                <span class="sm:block md:hidden lg:block">{{ __('Empresas') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-building text-md"></i>
                            </x-nav-link-responsive>
                            <x-nav-link-responsive href="{{ route('profesions') }}" wire:navigate :active="request()->routeIs('profesions')">
                                <span class="sm:block md:hidden lg:block">{{ __('Profesiones') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-graduation-cap text-md"></i>
                            </x-nav-link-responsive>
                            <x-nav-link-responsive href="{{ route('client') }}" wire:navigate :active="request()->routeIs('client')">
                                <span class="sm:block md:hidden lg:block">{{ __('Clientes') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-users text-md"></i>
                            </x-nav-link-responsive>
                        @endrole
                        @role('ADMIN')
                            <x-nav-link-responsive href="{{ route('area') }}" wire:navigate :active="request()->routeIs('area')">
                                <span class="sm:block md:hidden lg:block">{{ __('Areas') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-suitcase text-md"></i>
                            </x-nav-link-responsive>
                            <x-nav-link-responsive href="{{ route('report') }}" wire:navigate :active="request()->routeIs('report')">
                                <span class="sm:block md:hidden lg:block">{{ __('Reportes') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-clipboard-list text-md"></i>
                            </x-nav-link-responsive>
                            <x-nav-link-responsive href="{{ route('notice') }}" wire:navigate :active="request()->routeIs('notice')">
                                <span class="sm:block md:hidden lg:block">{{ __('Noticias') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-clipboard-list text-md"></i>
                            </x-nav-link-responsive>
                            <x-nav-link-responsive href="{{ route('user') }}" wire:navigate :active="request()->routeIs('user')">
                                <span class="sm:block md:hidden lg:block">{{ __('Usuarios') }}</span>
                                <i class="hidden md:block lg:hidden fas fa-users-cog text-md"></i>
                            </x-nav-link-responsive>
                        @endrole
                    </ul>
                </div>
            @endauth
        </div>
    </nav>
</header>
