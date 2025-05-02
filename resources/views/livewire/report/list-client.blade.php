<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Reportes</x-slot>
            <x-slot name="description_page">
                Obtener información sobre las cuentas de los clientes.
            </x-slot>
            <x-slot name="search_field">
                <x-input type="date" x-model="startDate" />
                <x-input type="date" x-model="endDate" />
                <x-button type="button" x-on:click="processData"
                    x-bind:disabled="!startDate || !endDate">Procesar</x-button>
            </x-slot>
        </x-title-app>
        <div class="flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-1/3">
                <div class="bg-white rounded-lg px-6 py-5 shadow-lg">
                    <h5 class="text-lg font-medium mb-2 text-tbn-primary">
                        Información general</h5>
                    <table class="min-w-full divide-y divide-gray-200 mb-4">
                        <tbody class="bg-white divide-y text-tbn-dark text-sm divide-gray-200">
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
                    <x-button type="button" wire:click='exportData' x-bind:disabled="{{ count($clients) == 0 }}">
                        Exportar</x-button>
                </div>
            </div>
            <div class="w-full md:w-2/3">
                <table
                    class="w-full bg-white rounded-md shadow-md mb-5 text-sm text-left rtl:text-right overflow-x-auto">
                    <thead class="text-xs uppercase text-tbn-dark">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Nombre del cliente
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Tipo de cuenta
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Verificación
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Ganancia (Bs.)
                            </th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class="opacity-50">
                        @forelse ($clients as $client)
                            <tr class="border-b hover:bg-gray-300">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <h5 class="text-md font-bold">{{ $client->name }}</h5>
                                </th>
                                <td class="px-6 py-4 uppercase">
                                    {{ $client->account->accountType->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ date('d/M/Y - H:m', strtotime($client->account->updated_at)) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $client->account->accountType->price }}
                                </td>
                            </tr>
                        @empty
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="py-4 text-center font-italic text-gray-600" colspan="4">
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
