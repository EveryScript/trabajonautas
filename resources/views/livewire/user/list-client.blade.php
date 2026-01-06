<section>
    <x-title-app>
        <x-slot name="title_page">Clientes</x-slot>
        <x-slot name="description_page">
            Administra la información de todos los clientes de Trabajonautas
        </x-slot>
        <x-slot name="search_field">
            <div>
                <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)" class="w-full"
                placeholder="Buscar cliente" />
            </div>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <table class="w-full bg-white dark:bg-tbn-dark rounded-md shadow-md mb-5 text-sm text-left rtl:text-right">
            <thead class="text-xs uppercase text-tbn-secondary">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3 hidden md:table-cell">
                        Ubicación
                    </th>
                    <th scope="col" class="px-6 py-3 hidden md:table-cell">
                        Profesión
                    </th>
                    <th scope="col" class="px-6 py-3 hidden md:table-cell">
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
                    <tr class="text-center text-tbn-dark text-sm bg-gray-200">
                        <td class="px-6 py-2" colspan="6">
                            <div class="flex flex-row justify-between items-center">
                                <div>
                                    <span class="font-bold">"{{ $search }}"</span>
                                    <i class="fas fa-arrow-right text-xs px-2"></i>
                                    {{ $count_results }} Resultados encontrados
                                </div>
                                <button type="button" wire:click="$set('search', null)">
                                    <i class="fas fa-times text-tbn-primary text-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                @endif
                @forelse ($clients as $client)
                    <tr wire:key='{{ $client->id }}'
                        class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            <h5
                                class="inline text-md md:text-lg font-medium {{ !$client->actived ? 'opacity-50' : '' }}">
                                {{ $client->name }}</h5>
                        </th>
                        <td
                            class="px-6 py-4 dark:text-tbn-light hidden md:table-cell {{ !$client->actived ? 'opacity-50' : '' }}">
                            {{ $client->location->location_name }}
                        </td>
                        <td
                            class="px-6 py-4 dark:text-tbn-light hidden md:table-cell {{ !$client->actived ? 'opacity-50' : '' }}">
                            {{ $client->profesion->profesion_name }}
                        </td>
                        <td
                            class="px-6 py-4 dark:text-tbn-light hidden md:table-cell {{ !$client->actived ? 'opacity-50' : '' }}">
                            {{ \Carbon\Carbon::parse($client->created_at)->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($client->account)
                                @if (intval($client->account->account_type_id) === 1)
                                    <span
                                        class="inline-block bg-green-500 text-white text-xs px-2 py-1 rounded-full whitespace-nowrap">
                                        {{ $client->account->accountType->name }}</span>
                                @else
                                    @if ($client->account->verified_payment)
                                        <span
                                            class="inline-block bg-tbn-primary text-white text-xs px-2 py-1 rounded-full whitespace-nowrap">
                                            {{ $client->account->accountType->name }}</span>
                                    @else
                                        <span
                                            class="bg-tbn-secondary text-white animate-pulse text-xs px-2 py-1 rounded-full">Pendiente</span>
                                    @endif
                                @endif
                            @endif
                        </td>
                        <td class="flex flex-row justify-end items-center h-15 px-6 py-4 text-xl">
                            <a href="{{ route('config-client', ['id' => $client->id]) }}" wire:navigate
                                class="text-tbn-dark dark:text-tbn-light"><i class="fas fa-cog"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="py-4 text-center font-italic text-gray-600" colspan="5">
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
                confirmModal(id) {
                    console.log(id)
                }
            }))
        </script>
    @endscript
</section>
