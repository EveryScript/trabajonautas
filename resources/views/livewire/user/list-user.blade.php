<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Usuarios</x-slot>
            <x-slot name="description_page">
                Administra la información de todos los usuarios y clientes registrados en Trabajonautas.com
            </x-slot>
            <x-slot name="search_field">
                <div class="flex flex-row justify-end h-full gap-1 sm:h-10">
                    <x-secondary-button class="relative w-[10rem]" x-on:click="show_dropdown = true" type="button"
                        id="suggest-menu" aria-expanded="false" data-dropdown-toggle="suggest-dropdown"
                        data-dropdown-placement="bottom">
                        <span x-html="filter_text"></span>
                    </x-secondary-button>
                    <x-button type="button" href="{{ route('config-user') }}" wire:navigate>Nuevo</x-button>
                    <!-- Dropdown menu -->
                    <div class="relative">
                        <div x-show="show_dropdown" x-on:click.outside="show_dropdown = false"
                            class="absolute z-50 w-48 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg right-20 top-8 dark:text-tbn-dark dark:divide-tbn-secondary"
                            id="suggest-dropdown">
                            <ul class="bg-white dark:bg-tbn-dark" x-on:click="show_dropdown = false" class="py-2"
                                aria-labelledby="user-menu-button">
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterUser('all')"
                                        class="block px-4 py-2 text-sm text-gray-700 rounded-t-lg hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Todos los usuarios</a>
                                </li>
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterUser('admin')"
                                        class="block px-4 py-2 text-sm text-gray-700 rounded-t-lg hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Administradores</a>
                                </li>
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterUser('user')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Usuarios</a>
                                </li>
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterUser('inactived')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Usuarios deshabilitados</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-title-app>
        <table class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
            <thead class="text-xs uppercase text-tbn-secondary dark:text-tbn-light">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nombre
                    </th>
                    <th scope="col" class="hidden px-6 py-3 md:table-cell">
                        Email
                    </th>
                    <th scope="col" class="hidden px-6 py-3 md:table-cell">
                        Actualización
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody class="text-tbn-secondary dark:text-tbn-light">
                @forelse ($users as $user)
                    <tr wire:key='{{ $user->id }}' x-data="{
                        info: {
                            admin: {{ $user->actived && $user->hasRole('ADMIN') ? 'true' : 'false' }},
                            user: {{ $user->actived && $user->hasRole('USER') ? 'true' : 'false' }},
                            actived: {{ $user->actived ? 'true' : 'false' }}
                        }
                    }"
                        x-show="(filter_option === 'all' && info.actived) || (filter_option === 'admin' && info.admin) || (filter_option === 'user' && info.user) || (filter_option === 'inactived' && !info.actived)"
                        x-transition:enter.duration.300ms x-transition:leave.duration.300ms
                        class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-tbn-dark dark:text-white whitespace-nowrap">
                            <span class="mr-2" x-html="setUserRole({{ $user->getRoleNames() }})"></span>
                            <h5 class="inline font-medium text-md md:text-lg">{{ $user->name }}</h5>
                        </th>
                        <td class="hidden px-6 py-4 md:table-cell">
                            {{ $user->email }}
                        </td>
                        <td class="hidden px-6 py-4 md:table-cell">
                            {{ date('d/m/Y - H:i', strtotime($user->updated_at)) }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($user->actived)
                                <span class="px-2 py-1 text-xs text-white rounded-full bg-tbn-primary">
                                    Activo</span>
                            @else
                                <span class="px-2 py-1 text-xs text-white rounded-full bg-tbn-dark">
                                    Inactivo</span>
                            @endif
                        </td>
                        <td class="flex flex-row items-center justify-end px-6 py-4 text-xl h-15">
                            <a href="{{ route('config-user', ['id' => $user->id]) }}"
                                class="text-tbn-dark dark:text-tbn-light" wire:navigate><i class="fas fa-cog"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="py-4 text-center text-gray-600 font-italic" colspan="4">No se han encontrado
                            datos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                show_dropdown: false,
                filter_option: 'all',
                filter_text: 'Filtrar',
                // Functions
                setFilterUser(option) {
                    this.filter_option = option
                    switch (option) {
                        case 'all':
                            this.filter_text = 'Todos'
                            break;
                        case 'admin':
                            this.filter_text = 'Administradores'
                            break;
                        case 'user':
                            this.filter_text = 'Usuarios'
                            break;
                        case 'inactived':
                            this.filter_text = 'Deshabilitados'
                            break;
                        default:
                            this.filter_text = 'all'
                            break;
                    }
                    this.filter_text +=
                        ' <i class="text-xs translate-x-2 -translate-y-1 fa-solid fa-sort-down"></i>'
                },
                setUserRole(roles) {
                    switch (roles[0]) {
                        case 'ADMIN':
                            return '<i class="text-gray-500 fas fa-user-cog"></i>';
                            break;
                        case 'USER':
                            return '<i class="fas fa-user text-tbn-primary"></i>';
                            break;
                    }
                },
                confirmModal(id) {
                    console.log(id)
                }
            }))
        </script>
    @endscript
</section>
