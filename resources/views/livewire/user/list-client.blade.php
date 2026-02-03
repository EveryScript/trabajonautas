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
                    <x-select wire:model.live='filter_client' class="pr-10">
                        <option value="">Todos los clientes</option>
                        <optgroup label="Por tipo de cuenta">
                            <option value="1">Clientes FREE</option>
                            <option value="2">Clientes PRO</option>
                            <option value="3">Clientes PRO-MAX</option>
                        </optgroup>
                        <optgroup label="Por estado">
                            <option value="active">Solo Activos</option>
                            <option value="inactive">Solo Inactivos</option>
                        </optgroup>
                        <optgroup label="Otros">
                            <option value="unaccount">Sin cuenta</option>
                            <option value="deleted">Eliminados</option>
                        </optgroup>
                    </x-select>
                </div>
            </x-slot>
        </x-title-app>
        <div class="flex flex-col gap-4 lg:flex-row">
            <!--- Clients List -->
            <div class="w-full overflow-x-auto lg:w-3/5">
                <table
                    class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
                    <thead class="text-xs uppercase text-tbn-secondary">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Nombre
                            </th>
                            <th scope="col" class="hidden px-6 py-3 md:table-cell">
                                Ubicación
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Cuenta
                            </th>
                            <th scope="col" class="px-6 py-3 text-right">
                                Opciones
                            </th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class='opacity-20' class="divide-y divide-tbn-light dark:divide-tbn-secondary">
                        @if ($search)
                            <tr class="text-sm text-center bg-gray-200 text-tbn-dark">
                                <td class="px-6 py-2" colspan="4">
                                    <div class="flex flex-row items-center justify-between">
                                        <div>
                                            <span class="font-bold">"{{ $search }}"</span>
                                            <i class="px-2 text-xs fas fa-arrow-right"></i>
                                            {{ $clients->count() }} Resultados encontrados
                                        </div>
                                        <button type="button" wire:click="$set('search', null)">
                                            <i class="text-lg fas fa-times text-tbn-primary"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @forelse ($clients as $client)
                            <tr wire:key='client-{{ $client->id }}'
                                class="hover:bg-gray-300 dark:hover:bg-neutral-900">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    <h5 class="inline font-medium text-md md:text-lg">
                                        {{ $client->name }}</h5>
                                </th>
                                <td class="hidden px-6 py-4 dark:text-tbn-light md:table-cell">
                                    {{ $client->location ? $client->location->location_name : '(sin datos)' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($client->trashed())
                                        <span class="px-2 py-1 text-xs rounded-full text-tbn-primary bg-neutral-900">
                                            <i class="mr-1 fa-solid fa-ban"></i> Eliminado
                                        </span>
                                    @elseif (!$client->actived)
                                        <span class="px-2 py-1 text-xs text-white rounded-full bg-neutral-900">
                                            <i class="mr-1 fas fa-user-slash"></i> Desactivado
                                        </span>
                                    @elseif ($client->latestPendingSubscription)
                                        <span
                                            class="px-2 py-1 text-xs text-white rounded-full bg-tbn-secondary animate-pulse">
                                            Pendiente
                                        </span>
                                    @elseif($client->account)
                                        <span
                                            class="inline-block px-2 py-1 text-xs text-white {{ $client->account->account_type_id == 1 ? 'bg-green-600' : 'bg-tbn-primary' }} rounded-full tracking-wider">
                                            <i
                                                class="mr-1 fas {{ $client->account->account_type_id == 1 ? 'fa-leaf' : 'fa-crown' }}"></i>
                                            {{ $client->account->type->name }}
                                        </span>
                                    @else
                                        <span class="text-xs italic text-tbn-secondary">(sin cuenta)</span>
                                    @endif
                                </td>
                                <td class="flex flex-row items-center justify-end px-6 py-4 text-xl h-15">
                                    <button wire:click='$dispatch("load-client", { id: "{{ $client->id }}" })'
                                        x-on:click="loading_client = true"
                                        class="transition-colors duration-300 text-tbn-dark dark:text-tbn-light hover:text-tbn-primary">
                                        <i class="fa-solid fa-right-to-bracket"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white dark:bg-tbn-dark">
                                <td class="py-4 text-center text-gray-600 font-italic" colspan="4">
                                    No hay datos para mostrar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div wire:loading.remove> {{ $clients->links() }} </div>
            </div>
            <!-- Clients Detail -->
            <div class="w-full lg:w-2/5">
                <livewire:user.config-client />
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                show_dropdown: false,
                loading_client: false,
                filter_option: 'all',
                filter_text: 'Filtrar',
                // Functions
                init() {
                    // Config client component
                    $wire.on('client-loaded', () => {
                        this.loading_client = false
                    })
                    $wire.on('client-saved', (data) => {
                        Swal.fire({
                            title: "Guardado",
                            text: "Los datos del cliente se han guardado correctamente.",
                            showDenyButton: true,
                            confirmButtonColor: '#ff420a',
                            confirmButtonText: "Enviar Whatsapp",
                            denyButtonColor: '#484848',
                            denyButtonText: "Salir",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let url = 'https://wa.me/591' + data[0].phone +
                                    '?text=Hola%20' + data[0].name +
                                    '%2C%20*Trabajonautas.com*%20te%20informa%20que%20tu%20cuenta%20' +
                                    data[0].type +
                                    '%20ya%20está%20disponible.%20Ingresa%20a%20trabajonautas.com/panel%20ahora%20mismo'
                                window.open(url, '_blank')
                            }
                        });
                    })
                    $wire.on('client-error', () => {
                        Swal.fire({
                            title: "Error",
                            text: 'Ha ocurrido un error al guardar al cliente',
                            confirmButtonColor: '#ff420a'
                        })
                    })
                },
                confirmModal(id) {
                    console.log(id)
                }
            }))
        </script>
    @endscript
</section>
