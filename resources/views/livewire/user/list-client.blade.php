<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Clientes</x-slot>
            <x-slot name="description_page">
                Administra la información de todos los clientes de Trabajonautas
            </x-slot>
            <x-slot name="search_field" class="relative">
                <div class="flex flex-row justify-end h-full gap-1 sm:h-10">
                    <x-input id="search" type="search" wire:keydown.enter="$set('search', $event.target.value)"
                        class="w-full" placeholder="Buscar cliente" />
                    <x-secondary-button class="relative w-[14rem]" x-on:click="show_dropdown = true" type="button"
                        id="suggest-menu" aria-expanded="false" data-dropdown-toggle="suggest-dropdown"
                        data-dropdown-placement="bottom">
                        <span x-html="filter_text"></span>
                    </x-secondary-button>
                    <!-- Dropdown menu -->
                    <div class="relative">
                        <div x-show="show_dropdown" x-on:click.outside="show_dropdown = false"
                            class="absolute right-0 z-50 w-40 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg top-8 dark:text-tbn-dark dark:divide-tbn-secondary"
                            id="suggest-dropdown">
                            <ul class="bg-white dark:bg-tbn-dark" x-on:click="show_dropdown = false" class="py-2"
                                aria-labelledby="user-menu-button">
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterAnnounce('all')"
                                        class="block px-4 py-2 text-sm text-gray-700 rounded-t-lg hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Todos los clientes</a>
                                </li>
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterAnnounce('free')"
                                        class="block px-4 py-2 text-sm text-gray-700 rounded-t-lg hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Clientes FREE</a>
                                </li>
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterAnnounce('pro')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Clientes PRO</a>
                                </li>
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterAnnounce('max')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Clientes PRO-MAX</a>
                                </li>
                                <li class="cursor-pointer">
                                    <a x-on:click="setFilterAnnounce('inactived')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                                        Clientes deshabilitados</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-title-app>
        <table class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
            <thead class="text-xs uppercase text-tbn-secondary">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nombre
                    </th>
                    <th scope="col" class="hidden px-6 py-3 md:table-cell">
                        Ubicación
                    </th>
                    <th scope="col" class="hidden px-6 py-3 md:table-cell">
                        Profesión
                    </th>
                    <th scope="col" class="hidden px-6 py-3 md:table-cell">
                        Registro
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Cuenta
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @if ($search)
                    <tr class="text-sm text-center bg-gray-200 text-tbn-dark">
                        <td class="px-6 py-2" colspan="6">
                            <div class="flex flex-row items-center justify-between">
                                <div>
                                    <span class="font-bold">"{{ $search }}"</span>
                                    <i class="px-2 text-xs fas fa-arrow-right"></i>
                                    {{ $count_results }} Resultados encontrados
                                </div>
                                <button type="button" wire:click="$set('search', null)">
                                    <i class="text-lg fas fa-times text-tbn-primary"></i></button>
                            </div>
                        </td>
                    </tr>
                @endif
                @forelse ($clients as $client)
                    <tr wire:key='{{ $client->id }}' x-data="{
                        info: {
                            free: {{ $client->actived && intval($client->account->account_type_id) === 1 ? 'true' : 'false' }},
                            pro: {{ $client->actived && intval($client->account->account_type_id) === 2 ? 'true' : 'false' }},
                            max: {{ $client->actived && intval($client->account->account_type_id) === 3 ? 'true' : 'false' }},
                            actived: {{ $client->actived ? 'true' : 'false' }}
                        }
                    }"
                        x-show="(filter_option === 'all' && info.actived) || (filter_option === 'free' && info.free) || (filter_option === 'pro' && info.pro) || (filter_option === 'max' && info.max) || (filter_option === 'inactived' && !info.actived)"
                        x-transition:enter.duration.300ms x-transition:leave.duration.300ms
                        class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            <h5
                                class="inline font-medium text-md md:text-lg">
                                {{ $client->name }}</h5>
                        </th>
                        <td
                            class="hidden px-6 py-4 dark:text-tbn-light md:table-cell">
                            {{ $client->location->location_name }}
                        </td>
                        <td
                            class="hidden px-6 py-4 dark:text-tbn-light md:table-cell">
                            {{ $client->profesion->profesion_name }}
                        </td>
                        <td
                            class="hidden px-6 py-4 dark:text-tbn-light md:table-cell">
                            {{ \Carbon\Carbon::parse($client->created_at)->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($client->account)
                                @if (intval($client->account->account_type_id) === 1)
                                    <span
                                        class="inline-block px-2 py-1 text-xs text-white {{ $client->actived ? 'bg-green-500' : 'bg-neutral-700' }} rounded-full whitespace-nowrap">
                                        {{ $client->account->accountType->name }}</span>
                                @else
                                    @if ($client->account->verified_payment)
                                        <span
                                            class="inline-block px-2 py-1 text-xs text-white rounded-full {{ $client->actived ? 'bg-tbn-primary' : 'bg-neutral-700' }} whitespace-nowrap">
                                            {{ $client->account->accountType->name }}</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs text-white rounded-full bg-tbn-secondary animate-pulse">Pendiente</span>
                                    @endif
                                @endif
                            @endif
                        </td>
                        <td class="flex flex-row items-center justify-end px-6 py-4 text-xl h-15">
                            <a href="{{ route('config-client', ['id' => $client->id]) }}" wire:navigate
                                class="text-tbn-dark dark:text-tbn-light"><i class="fas fa-cog"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="py-4 text-center text-gray-600 font-italic" colspan="5">
                            No se han encontrado datos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div> {{ $clients->links() }} </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                show_dropdown: false,
                filter_option: 'all',
                filter_text: 'Filtrar',
                // Functions
                setFilterAnnounce(option) {
                    this.filter_option = option
                    switch (option) {
                        case 'all':
                            this.filter_text = 'Todos'
                            break;
                        case 'free':
                            this.filter_text = 'FREE'
                            break;
                        case 'pro':
                            this.filter_text = 'PRO'
                            break;
                        case 'max':
                            this.filter_text = 'PRO-MAX'
                            break;
                        case 'inactived':
                            this.filter_text = 'Deshabilitados'
                            break;
                        default:
                            this.filter_text = 'all'
                            break;
                    }
                    this.filter_text += ' <i class="text-xs translate-x-2 -translate-y-1 fa-solid fa-sort-down"></i>'
                },
                confirmModal(id) {
                    console.log(id)
                }
            }))
        </script>
    @endscript
</section>
