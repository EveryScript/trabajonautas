<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Reportes</x-slot>
            <x-slot name="description_page">
                Obtener información sobre las cuentas de los clientes.
            </x-slot>
            <x-slot name="search_field">
                <div class="h-full sm:h-10 flex flex-col sm:flex-row gap-1">
                    <x-input name="start" type="date" x-model="startDate" class="w-full" />
                    <x-input name="end" type="date" x-model="endDate" class="w-full" />
                    <x-button type="button" x-on:click="processData" x-bind:disabled="!startDate || !endDate">
                        <span wire:loading wire:target="searchData"><i class="fas fa-spinner text-sm text-white animate-spin"></i></span>
                        <span wire:loading.remove wire:target='searchData'>Procesar</span></x-button>
                </div>
            </x-slot>
        </x-title-app>
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="w-full lg:w-1/3">
                <div class="bg-white dark:bg-tbn-dark rounded-lg px-6 py-5 shadow-lg">
                    <h5 class="text-lg font-medium mb-2 text-tbn-primary">
                        Información general</h5>
                    <table class="min-w-full divide-y divide-gray-200 mb-4">
                        <tbody class="divide-y text-tbn-dark dark:text-tbn-light text-sm divide-tbn-light dark:divide-tbn-secondary">
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Fecha inicio</td>
                                <td class="py-1 whitespace-nowrap text-right">
                                    {{ date('d/M/Y', strtotime($start_date)) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Fecha final</td>
                                <td class="py-1 whitespace-nowrap text-right">
                                    {{ date('d/M/Y', strtotime($end_date)) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Cantidad de clientes</td>
                                <td class="py-1 whitespace-nowrap text-right">{{ count($clients) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Ganancia total</td>
                                <td class="py-1 whitespace-nowrap text-right">{{ $sum_prices }} Bs.</td>
                            </tr>
                        </tbody>
                    </table>
                    <x-button type="button" wire:click='exportData'
                        x-bind:disabled="{{ count($clients) === 0 ? 'true' : 'false' }}">
                        <span wire:loading.remove wire:target="exportData">Exportar</span>
                        <span wire:loading wire:target="exportData">Exportando...</span>
                    </x-button>
                </div>
            </div>
            <div class="w-full lg:w-2/3">
                <table
                    class="w-full bg-white dark:bg-tbn-dark rounded-md shadow-md mb-5 text-sm text-left rtl:text-right overflow-x-auto">
                    <thead class="text-xs uppercase text-tbn-dark dark:text-tbn-secondary">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Cliente
                            </th>
                            <th scope="col" class="px-6 py-3 hidden lg:table-cell">
                                Cuenta
                            </th>
                            <th scope="col" class="px-6 py-3 hidden lg:table-cell">
                                Celular
                            </th>
                            <th scope="col" class="px-6 py-3 hidden lg:table-cell">
                                Verificación
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Ganancia (Bs.)
                            </th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class="opacity-50">
                        @forelse ($clients as $client)
                            <tr wire:key='{{ $client->id }}' class="border-b dark:text-tbn-light hover:bg-gray-300 dark:border-b-tbn-secondary dark:hover:bg-neutral-900">
                                <th scope="row" class="px-6 py-4 font-medium text-tbn-dark dark:text-white whitespace-nowrap">
                                    <h5 class="text-md font-bold">{{ $client->name }}</h5>
                                </th>
                                <td class="px-6 py-4 uppercase hidden md:table-cell">
                                    {{ $client->account->accountType->name }}
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    {{ $client->phone }}
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    {{ date('d/M/Y - H:i', strtotime($client->account->updated_at)) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $client->account->accountType->price }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white dark:bg-tbn-dark border-b dark:border-b-tbn-secondary dark:hover:bg-neutral-900">
                                <td class="py-4 text-center font-italic text-tbn-dark dark:text-tbn-light" colspan="5">
                                    No se han encontrado datos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                startDate: '',
                endDate: '',
                processData() {
                    if (this.startDate && this.endDate)
                        $wire.searchData(this.startDate, this.endDate)
                }
            }))
        </script>
    @endscript
</section>
