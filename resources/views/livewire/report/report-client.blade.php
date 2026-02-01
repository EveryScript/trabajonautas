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
                                    <td class="py-1 text-right whitespace-nowrap">{{ $total_price }} Bs.</td>
                                </tr>
                            </tbody>
                        </table>
                        <x-button type="button" wire:click='exportData'
                            x-bind:disabled="{{ count($subscriptions) === 0 ? 'true' : 'false' }}">
                            <span wire:loading.remove wire:target="exportData">Exportar</span>
                            <span wire:loading wire:target="exportData">Exportando...</span>
                        </x-button>
                        <x-secondary-button type="button" x-on:click="modalForm = true">
                            <span>Cambiar QR</span>
                        </x-secondary-button>
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
                        @forelse ($subscriptions as $sub)
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
        <div x-show="modalForm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 dark:bg-opacity-80">
            <form wire:submit='saveQRImage'
                class="max-w-lg px-6 py-5 mx-4 bg-white rounded-lg shadow-lg dark:bg-tbn-dark">
                @if ($qr_new_image)
                    <div class="mb-2">
                        <picture class="block w-full mx-auto sm:w-1/2">
                            <img src="{{ $qr_new_image->temporaryUrl() }}" alt="qr-image" class="rounded">
                        </picture>
                    </div>
                @else
                    <div class="mb-2">
                        <picture class="block w-full mx-auto sm:w-1/2">
                            <img wire:target='qr_new_image' src="{{ asset('storage/' . $qr_image->value) }}"
                                alt="qr-image" class="rounded">
                        </picture>
                    </div>
                @endif
                <div class="mb-2">
                    <x-label for="image">Imagen / Código QR</x-label>
                    <input type="file" wire:model.live="qr_new_image" id="image"
                        class="w-full mt-2 text-tbn-dark font-medium text-sm bg-white dark:bg-tbn-dark dark:text-white file:cursor-pointer cursor-pointer file:border-0 file:py-2.5 file:px-4 file:mr-4 file:bg-tbn-primary file:hover:bg-tbn-secondary file:text-white rounded-lg file:transition-all file:duration-300"
                        accept="image/png, image/jpeg, image/jpg" x-on:change="previewQRImage" />
                    <x-input-error for="qr_new_image" class="mt-2" />
                    <small wire:loading wire:target='qr_new_image'
                        class="text-xs text-tbn-secondary dark:text-tbn-light">Subiendo imagen...</small>
                </div>
                <x-button class="mb-4" type="submit">
                    <span wire:loading.remove wire:target="saveQRImage">Publicar</span>
                    <span wire:loading wire:target="saveQRImage">
                        <i class="text-sm fas fa-spinner animate-spin"></i></span>
                </x-button>
                <x-secondary-button type="button" class="mb-4"
                    x-on:click="modalForm = false">Cancelar</x-secondary-button>
                <div class="text-xs text-tbn-dark dark:text-tbn-light">
                    ATENCIÓN. Una vez guardada, la imagen del código QR se actualizará en el formulario de registro de
                    clientes de Trabajonautas.com
                </div>
            </form>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                startDate: '',
                endDate: '',
                modalForm: false,
                previewImage: null,
                // Functions
                init() {
                    $wire.on('qr-image-saved', () => {
                        this.modalForm = false
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
                processData() {
                    if (this.startDate && this.endDate)
                        $wire.searchData(this.startDate, this.endDate)
                },
                previewQRImage() {

                }
            }))
        </script>
    @endscript
</section>
