<section>
    <div x-data="content">
        <x-title-app class="mb-2">
            <x-slot name="title_page">Clientes</x-slot>
            <x-slot name="description_page">
                Administra la información de todos los clientes de Trabajonautas
            </x-slot>
        </x-title-app>
        <div class="flex flex-col gap-1 mb-2 -mt-6 sm:flex-row sm:h-10">
            <x-input id="search" type="search" wire:keydown.enter="$set('search', $event.target.value)"
                class="w-full md:w-3/12" placeholder="Nombre, email, celular" />
            <x-select wire:model.live="profesion_id" class="w-full md:w-5/12">
                @foreach ($profesions as $profesion)
                    <option value="{{ $profesion->id }}">{{ $profesion->profesion_name }}</option>
                @endforeach
            </x-select>
            <x-select wire:model.live="location_id" class="w-full md:w-2/12">
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                @endforeach
            </x-select>
            <x-select wire:model.live="filter_client" class="w-full md:w-2/12">
                <option value="">Todos los clientes</option>
                <option value="pending">
                    Clientes pendientes
                </option>
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
        <div class="flex flex-col gap-4 mt-4 lg:flex-row">
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
                        <!-- Review Filters -->
                        @if ($search || $profesion_id || $location_id || $filter_client)
                            <tr class="text-sm bg-gray-100 dark:bg-neutral-800 text-tbn-dark dark:text-white">
                                <td class="px-6 py-3" colspan="4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">
                                            Filtros:
                                        </span>

                                        @if ($search)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-tbn-primary/10 text-tbn-primary">
                                                Texto: "{{ $search }}"
                                                <button type="button" wire:click="$set('search', null)"
                                                    class="hover:text-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </span>
                                        @endif

                                        @if ($profesion_id)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                Profesión:
                                                {{ $profesions->firstWhere('id', $profesion_id)?->profesion_name ?? 'Seleccionada' }}
                                                <button type="button" wire:click="$set('profesion_id', null)"
                                                    class="hover:text-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </span>
                                        @endif

                                        @if ($location_id)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                Ubicación:
                                                {{ $locations->firstWhere('id', $location_id)?->location_name ?? 'Seleccionada' }}
                                                <button type="button" wire:click="$set('location_id', null)"
                                                    class="hover:text-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </span>
                                        @endif

                                        @if ($filter_client)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                                Estado/Tipo: {{ $this->getFilterClientLabel() }}
                                                <button type="button" wire:click="$set('filter_client', '')"
                                                    class="hover:text-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </span>
                                        @endif

                                        <button type="button" wire:click="resetAllFilters"
                                            class="ml-auto text-xs text-red-500 underline hover:text-red-700">
                                            Limpiar todos
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @forelse ($clients as $client)
                            <tr wire:key='client-{{ $client->id }}'
                                class="hover:bg-gray-300 dark:hover:bg-neutral-900">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-wrap">
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
                                            class="inline-block text-xs {{ $client->account->account_type_id == 1 ? 'text-green-600' : 'text-tbn-primary' }} tracking-wide">
                                            <i
                                                class="hidden mr-1 fas {{ $client->account->account_type_id == 1 ? 'fa-leaf' : 'fa-crown' }}"></i>
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
