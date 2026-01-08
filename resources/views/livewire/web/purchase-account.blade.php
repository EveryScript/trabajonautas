<section class="bg-gray-50 dark:bg-neutral-700 min-h-screen flex items-start justify-center py-10">
    <div x-data="content" class="w-full max-w-xl md:max-w-3xl">
        <div class="p-6 md:p-10 bg-white dark:bg-tbn-dark dark:text-white rounded-lg shadow-lg mx-2">
            <div class="max-w-60 mb-3">
                <x-application-logo />
            </div>
            <h3 class="text-lg md:text-xl font-semibold mb-1">
                @if ($account_type_id == 2)
                    Todo listo para convertirte en <span class="text-tbn-primary">PRO</span>
                @endif
                @if ($account_type_id == 3)
                    Despega al infinito con tu cuenta <span class="text-tbn-primary">PRO-MAX</span>
                @endif
            </h3>
            <p class="text-sm text-tbn-secondary dark:text-tbn-light mb-4">
                Confirma tu información para adquirir el paquete seleccionado.</p>
            <!-- Step 1: Main view -->
            <div x-show="step === 1">
                <h5 class="text-md font-bold mb-1">Resumen de la compra</h5>
                <span class="block mb-2 text-xs text-tbn-dark">
                    Revisa tus datos y escanea el código QR para realizar tu depósito.</span>
                <div class="w-full flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-3/5 text-sm">
                        <div class="bg-gray-50 dark:bg-tbn-dark dark:text-bg-white border border-tbn-light dark:border-tbn-secondary rounded-md p-2 mb-4">
                            <table class="divide-y divide-gray-200 dark:divide-tbn-secondary">
                                <tbody class="divide-y divide-gray-200 dark:divide-tbn-secondary">
                                    <tr>
                                        <td class="w-1/2 p-2 whitespace-nowrap font-medium">Nombre</td>
                                        <td x-text="client.name" class="p-2 whitespace-wrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 whitespace-nowrap font-medium">Ubicación</td>
                                        <td class="p-2 whitespace-wrap">
                                            <p x-text="client.location.location_name" class="inline mr-1"></p>
                                            <button type="button" x-on:click="changeLocation"
                                                class="inline-block border border-tbn-primary hover:bg-tbn-primary transition-all duration-100 hover:text-white rounded-full px-2 py-1 text-xs text-tbn-primary">
                                                Cambiar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 whitespace-nowrap font-medium">Profesión</td>
                                        <td class="p-2 whitespace-wrap">
                                            <p x-text="client.profesion.profesion_name" class="inline mr-1"></p>
                                            <button type="button" x-on:click="changeProfesion"
                                                class="inline-block border border-tbn-primary hover:bg-tbn-primary transition-all duration-100 hover:text-white rounded-full px-2 py-1 text-xs text-tbn-primary">
                                                Cambiar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 whitespace-nowrap font-medium">Celular</td>
                                        <td x-text="client.phone" class="p-2 whitespace-nowrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 whitespace-nowrap font-medium">Tipo de cuenta</td>
                                        <td x-text="account_type.name" class="p-2 whitespace-nowrap uppercase">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 whitespace-nowrap font-medium">Costo</td>
                                        <td x-text="account_type.price +' Bs.'" class="p-2 whitespace-nowrap">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 whitespace-nowrap font-medium">Duración</td>
                                        <td x-text="account_type.duration_days +' días'" class="p-2 whitespace-nowrap">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="w-full md:w-2/5 text-sm">
                        <picture class="block max-w-[10rem] mx-auto mb-2">
                            <img class="w-full" src="{{ asset('storage/img/tbn-new-qr.webp') }}" alt="qr-code">
                        </picture>
                        <div class="text-center mb-8">
                            <button wire:click='downloadQR'
                                class="text-tbn-primary text-xs px-3 py-2 rounded-full border border-tbn-primary hover:bg-tbn-primary hover:text-white transition-all duration-200">
                                Descargar QR</button>
                        </div>
                        <div class="relative px-4 py-3 bg-gray-100 dark:bg-tbn-secondary mb-4 rounded-md">
                            <span class="absolute -top-3 text-xs text-tbn-primary bg-gray-100 dark:bg-tbn-secondary px-4 py-1 rounded-full">
                                Banco Bisa</span>
                            <span class="font-arial">36621-54481-29402-6598</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between gap-1 mt-4">
                    <x-secondary-button type="button" wire:click='backToDashboard'>
                        Volver al panel</x-secondary-button>
                    <x-button type="button" wire:click="confirmAndSave">
                        <span wire:loading.remove>Confirmar</span>
                        <span wire:loading><i class="fas fa-spinner text-sm animate-spin"></i></span>
                    </x-button>
                </div>
            </div>
            <!-- Step 2: Change location -->
            <div x-show="step === 2" x-cloak>
                <h5 class="text-md font-bold mb-2 dark:text-white">¿Cuál es tu nueva ubicación?</h5>
                <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto mb-8">
                    <template x-for="location in locations">
                        <li class="text-center" :key="'location-' + location.id">
                            <input type="radio" x-on:click="location_id = location.id" :value="location.id"
                                :id="'location-' + location.id" name="location" class="hidden peer">
                            <label :for="'location-' + location.id"
                                class="flex justify-center items-center h-12 sm:h-[4rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                                <div>
                                    <span x-text="location.location_name" class="block font-medium text-sm"></span>
                                </div>
                            </label>
                        </li>
                    </template>
                </ul>
                <div class="flex justify-between gap-1 mt-4">
                    <x-secondary-button type="button" x-on:click="step = 1">
                        Cancelar</x-secondary-button>
                    <x-button type="button" x-on:click="setLocation" x-bind:disabled="!location_id">
                        Guardar</x-button>
                </div>
            </div>
            <!-- Step 3: Change profesion -->
            <div x-show="step === 3" x-cloak>
                <h5 class="text-md font-bold mb-2 dark:text-white">¿Cuál es tu nueva profesión?</h5>
                <x-input type="search" name="profesion" x-model="searchProfesion" class="mb-2" id="searchProfesion"
                    class="w-full" placeholder="Busca una profesión" />
                <div class="h-[18rem] overflow-y-auto bg-white dark:bg-neutral-900 border border-gray-200 dark:border-tbn-secondary p-2 rounded-lg scrollbar-none">
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-1 mx-auto mb-8">
                        <template x-for="profesion in filteredProfesions">
                            <li class="text-center" :key="'profesion' + profesion.id">
                                <input type="radio" x-on:click="profesion_id = profesion.id" :value="profesion.id"
                                    :id="'profesion-' + profesion.id" name="profesion" class="hidden peer">
                                <label :for="'profesion-' + profesion.id"
                                    class="flex justify-center items-center h-12 sm:h-[5rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                                    <div>
                                        <span x-text="profesion.profesion_name"class="block font-medium text-sm"></span>
                                    </div>
                                </label>
                            </li>
                        </template>
                        <li x-show="filteredProfesions().length === 0 && searchProfesion.length > 0"
                            class="text-center text-xs text-tbn-secondary py-20 md:col-span-2">
                            No se encontraron resultados</li>
                    </ul>
                </div>
                <div class="flex justify-between gap-1 mt-4">
                    <x-secondary-button type="button" x-on:click="step = 1">
                        Cancelar</x-secondary-button>
                    <x-button type="button" x-on:click="setProfesion" x-bind:disabled="!profesion_id">
                        Guardar</x-button>
                </div>
            </div>
        </div>
    </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                // Models
                step: 1,
                profesion_id: null,
                location_id: null,
                searchProfesion: '',
                // Data
                client: @json($client),
                account_type: @json($account_type),
                profesions: @json($profesions),
                locations: @json($locations),
                // Functions
                setLocation() {
                    $wire.location_id = this.location_id
                    this.client.location = this.locations.find(item => item.id === this.location_id)
                    this.step = 1
                },
                setProfesion() {
                    $wire.profesion_id = this.profesion_id
                    this.client.profesion = this.profesions.find(item => item.id === this.profesion_id)
                    this.step = 1
                },
                changeProfesion(){
                    this.profesion_id = null
                    this.step = 3
                },
                changeLocation(){
                    this.location_id = null
                    this.step = 2
                },
                filteredProfesions() {
                    return this.profesions.filter(
                        profesion => profesion.profesion_name.toLowerCase().includes(this.searchProfesion
                            .toLowerCase())
                    )
                }
            }))
        </script>
    @endscript
</section>
