<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Reportes</x-slot>
            <x-slot name="description_page">
                Obtener información sobre las cuentas de los clientes.
            </x-slot>
            <x-slot name="search_field">
                <div class="flex flex-col h-full gap-1 sm:h-10 sm:flex-row">
                    <x-input id="dateRange" class="w-full dark:bg-tbn-dark dark:text-white" type="text"
                        placeholder="Rango de fechas" />
                    <x-button type="button" x-on:click="processData" x-bind:disabled="!startDate || !endDate">
                        <span wire:loading wire:target="searchData"><i
                                class="text-sm text-white fas fa-spinner animate-spin"></i></span>
                        <span wire:loading.remove wire:target='searchData'>Procesar</span></x-button>
                </div>
            </x-slot>
        </x-title-app>
        <div class="flex flex-col gap-4 lg:flex-row">
            <div class="w-full lg:w-4/12">
                <!-- Sidebar -->
                <div class="flex flex-col gap-4">
                    <!-- Info card -->
                    <div class="px-6 py-5 bg-white rounded-lg shadow-lg dark:bg-tbn-dark">
                        <h5 class="mb-2 text-lg font-medium text-tbn-primary">
                            Información general</h5>
                        <table class="min-w-full mb-4 divide-y divide-gray-200">
                            <tbody wire:loading.class="opacity-50"
                                class="text-sm divide-y text-tbn-dark dark:text-tbn-light divide-tbn-light dark:divide-tbn-secondary">
                                <tr>
                                    <td class="py-1 font-medium whitespace-nowrap">Fecha inicio</td>
                                    <td class="py-1 text-right whitespace-nowrap">
                                        {{ date('d/M/Y', strtotime($start_date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 font-medium whitespace-nowrap">Fecha final</td>
                                    <td class="py-1 text-right whitespace-nowrap">
                                        {{ date('d/M/Y', strtotime($end_date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 font-medium whitespace-nowrap">Ganancia total</td>
                                    <td class="py-1 text-right whitespace-nowrap">{{ $this->total_price }} Bs.</td>
                                </tr>
                            </tbody>
                        </table>
                        <x-button type="button" wire:click='exportData' wire:loading.attr='disabled'
                            wire:target="exportData"
                            x-bind:disabled="{{ count($this->subscriptions) === 0 ? 'true' : 'false' }}">
                            <span wire:loading.remove wire:target="exportData">Exportar</span>
                            <span wire:loading wire:target="exportData">Exportando...</span>
                        </x-button>
                    </div>
                    <!-- Change QR Card -->
                    <div class="px-6 py-5 bg-white rounded-lg shadow-lg dark:bg-tbn-dark">
                        <h5 class="mb-2 text-lg font-medium text-tbn-primary">
                            Código QR</h5>
                        <p class="mb-4 text-sm text-tbn-secondary dark:text-tbn-light">
                            Configuración de las imágenes de los códigos QR en el registro de nuevos clientes</p>
                        <x-button type="button" x-on:click="openModal">
                            Cambiar QR </x-button>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-8/12">
                <!-- Data to export -->
                <table
                    class="w-full mb-5 overflow-x-auto text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
                    <thead class="text-xs uppercase text-tbn-dark dark:text-tbn-secondary">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Cliente
                            </th>
                            <th scope="col" class="hidden px-6 py-3 md:table-cell">
                                Cuenta
                            </th>
                            <th scope="col" class="hidden px-6 py-3 md:table-cell">
                                Celular
                            </th>
                            <th scope="col" class="hidden px-6 py-3 md:table-cell">
                                Verificación
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Ganancia (Bs.)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-tbn-light dark:divide-tbn-secondary" wire:loading.class="opacity-50">
                        @forelse ($this->subscriptions as $sub)
                            <tr wire:key='sub-{{ $sub->id }}'
                                class="dark:text-tbn-light hover:bg-gray-300 dark:hover:bg-neutral-900">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-tbn-dark dark:text-white whitespace-nowrap">
                                    <h5 class="font-bold text-md">{{ $sub->user->name }}</h5>
                                </th>
                                <td class="hidden px-6 py-4 uppercase md:table-cell">
                                    {{ $sub->type->name }}
                                </td>
                                <td class="hidden px-6 py-4 md:table-cell">
                                    {{ $sub->user->phone }}
                                </td>
                                <td class="hidden px-6 py-4 md:table-cell">
                                    {{ $sub->updated_at->translatedFormat('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $sub->type->price }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white dark:bg-tbn-dark dark:border-b-tbn-secondary dark:hover:bg-neutral-900">
                                <td class="py-4 text-center font-italic text-tbn-dark dark:text-tbn-light"
                                    colspan="5">
                                    No se han encontrado datos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Change QR form -->
        <template x-if="modalForm">
            <livewire:report.config-qr />
        </template>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                startDate: '',
                endDate: '',
                modalForm: false,
                // Functions
                init() {
                    $wire.on('close-modal', () => {
                        this.modalForm = false
                        Swal.fire({
                            title: "¡Éxito!",
                            text: "Las imágenes de los códigos QR se han actualizado correctamente",
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: '#ff420a',
                        })
                    })
                    flatpickr("#dateRange", {
                        mode: "range",
                        dateFormat: "d/m/Y",
                        "locale": "es",
                        onClose: (selectedDates, dateStr, instance) => {
                            this.startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                            this.endDate = instance.formatDate(selectedDates[1], 'Y-m-d');
                        }
                    });
                },
                openModal() {
                    this.modalForm = true
                },
                closeModal() {
                    this.modalForm = false
                },
                processData() {
                    if (this.startDate && this.endDate)
                        $wire.searchData(this.startDate, this.endDate)
                }
            }))
        </script>
    @endscript
</section>
