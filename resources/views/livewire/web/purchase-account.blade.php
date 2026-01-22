<section class="flex items-start justify-center min-h-screen py-10 bg-gray-50 dark:bg-neutral-700">
    <div x-data="content" class="w-full max-w-xl md:max-w-4xl">
        <div class="p-6 mx-2 bg-white rounded-lg shadow-lg md:p-10 dark:bg-tbn-dark dark:text-white">
            <div class="mb-3 max-w-60">
                <x-application-logo />
            </div>
            <h3 class="mb-1 text-lg font-semibold md:text-xl">
                @if ($account_type_id == 2)
                    Todo listo para convertirte en <span class="text-tbn-primary">PRO</span>
                @endif
                @if ($account_type_id == 3)
                    Despega al infinito con tu cuenta <span class="text-tbn-primary">PRO-MAX</span>
                @endif
            </h3>
            <p class="mb-4 text-sm text-tbn-secondary dark:text-tbn-light">
                Confirma tu información para adquirir el paquete seleccionado.</p>
            <!-- Step 1: Main view -->
            <div x-show="step === 1">
                <h5 class="mb-1 font-bold text-md">Resumen de la compra</h5>
                <span class="block mb-2 text-xs text-tbn-dark dark:text-tbn-light">
                    Revisa tus datos y escanea el código QR para realizar tu depósito.</span>
                <div class="flex flex-col w-full gap-6 md:flex-row">
                    <div class="w-full text-sm md:w-3/5">
                        <div
                            class="p-2 mb-4 border rounded-md bg-gray-50 dark:bg-tbn-dark dark:text-bg-white border-tbn-light dark:border-tbn-secondary">
                            <table class="divide-y divide-gray-200 dark:divide-tbn-secondary">
                                <tbody class="divide-y divide-gray-200 dark:divide-tbn-secondary">
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium whitespace-nowrap">Nombre</td>
                                        <td x-text="client.name" class="p-2 whitespace-wrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium whitespace-nowrap">Ubicación</td>
                                        <td class="p-2 whitespace-wrap">
                                            <p x-text="client.location.location_name" class="inline mr-1"></p>
                                            <button type="button" x-on:click="changeLocation"
                                                class="inline-block px-2 py-1 text-xs transition-all duration-100 border rounded-full border-tbn-primary hover:bg-tbn-primary hover:text-white text-tbn-primary">
                                                Cambiar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium whitespace-nowrap">Profesión</td>
                                        <td class="p-2 whitespace-wrap">
                                            <p x-text="client.profesion.profesion_name" class="inline mr-1"></p>
                                            <button type="button" x-on:click="changeProfesion"
                                                class="inline-block px-2 py-1 text-xs transition-all duration-100 border rounded-full border-tbn-primary hover:bg-tbn-primary hover:text-white text-tbn-primary">
                                                Cambiar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium whitespace-nowrap">Celular</td>
                                        <td x-text="client.phone.substr(4,10)" class="p-2 whitespace-nowrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium whitespace-nowrap">Tipo de cuenta</td>
                                        <td x-text="account_type.name" class="p-2 uppercase whitespace-nowrap">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium whitespace-nowrap">Costo</td>
                                        <td x-text="account_type.price +' Bs.'" class="p-2 whitespace-nowrap">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium whitespace-nowrap">Duración</td>
                                        <td x-text="account_type.duration_days +' días'" class="p-2 whitespace-nowrap">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-tbn-dark dark:text-tbn-light">
                            Una vez realizado el depósito nuestros operadores se comunicarán contigo para confirmar el
                            depósito y habilitar tu cuenta.</p>
                    </div>
                    <div class="w-full text-sm md:w-2/5">
                        <picture class="block max-w-[10rem] mx-auto mb-2">
                            <img class="w-full" src="{{ asset('storage/' . $qr_image->value) }}" alt="qr-code">
                        </picture>
                        <div class="mb-6 text-center">
                            <a href="{{ asset('storage/' . $qr_image->value) }}" download
                                class="inline-block px-3 py-2 text-xs transition-all duration-200 border rounded-full text-tbn-primary border-tbn-primary hover:bg-tbn-primary hover:text-white">
                                Descargar QR</a>
                        </div>
                        <!-- Bank account -->
                        <div
                            class="flex items-center justify-between max-w-sm p-4 transition-colors bg-white border shadow-sm dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary rounded-xl">
                            <div class="flex items-center gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-tbn-secondary dark:text-tbn-light">
                                        Banco Mercantil Santa Cruz</h4>
                                    <p x-text="bankAccount"
                                        class="font-mono text-xl tracking-wider text-tbn-dark dark:text-white"></p>
                                </div>
                            </div>
                            <button x-on:click="copyClipboardBankAccount"
                                class="p-2 rounded-full hover:text-tbn-primary text-tbn-secondary">
                                <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <svg x-show="copied" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between gap-1 mt-4">
                    <x-secondary-button type="button" wire:click='backToDashboard'>
                        Volver al panel</x-secondary-button>
                    <x-button type="button" wire:click="confirmAndSave">
                        <span wire:loading.remove>Confirmar</span>
                        <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
                    </x-button>
                </div>
            </div>
            <!-- Step 2: Change location -->
            <div x-show="step === 2" x-cloak>
                <h5 class="mb-2 font-bold text-md dark:text-white">¿Cuál es tu nueva ubicación?</h5>
                <ul class="grid grid-cols-2 gap-1 mx-auto mb-8 md:grid-cols-3">
                    <template x-for="location in locations">
                        <li class="text-center" :key="'location-' + location.id">
                            <input type="radio" x-on:click="location_id = location.id" :value="location.id"
                                :id="'location-' + location.id" name="location" class="hidden peer">
                            <label :for="'location-' + location.id"
                                class="flex justify-center items-center h-12 sm:h-[4rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                                <div>
                                    <span x-text="location.location_name" class="block text-sm font-medium"></span>
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
                <h5 class="mb-2 font-bold text-md dark:text-white">¿Cuál es tu nueva profesión?</h5>
                <x-input type="search" name="profesion" x-model="searchProfesion" class="mb-2"
                    id="searchProfesion" class="w-full" placeholder="Busca una profesión" />
                <div
                    class="h-[18rem] overflow-y-auto bg-white dark:bg-neutral-900 border border-gray-200 dark:border-tbn-secondary p-2 rounded-lg scrollbar-none">
                    <ul class="grid grid-cols-1 gap-1 mx-auto mb-8 md:grid-cols-2">
                        <template x-for="profesion in filteredProfesions">
                            <li class="text-center" :key="'profesion' + profesion.id">
                                <input type="radio" x-on:click="profesion_id = profesion.id" :value="profesion.id"
                                    :id="'profesion-' + profesion.id" name="profesion" class="hidden peer">
                                <label :for="'profesion-' + profesion.id"
                                    class="flex justify-center items-center h-12 sm:h-[5rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                                    <div>
                                        <span
                                            x-text="profesion.profesion_name"class="block text-sm font-medium"></span>
                                    </div>
                                </label>
                            </li>
                        </template>
                        <li x-show="filteredProfesions().length === 0 && searchProfesion.length > 0"
                            class="py-20 text-xs text-center text-tbn-secondary md:col-span-2">
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
                // Bank Account
                bankAccount: '4077070681',
                copied: false,
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
                changeProfesion() {
                    this.profesion_id = null
                    this.step = 3
                },
                changeLocation() {
                    this.location_id = null
                    this.step = 2
                },
                filteredProfesions() {
                    return this.profesions.filter(
                        profesion => profesion.profesion_name.toLowerCase().includes(this.searchProfesion
                            .toLowerCase())
                    )
                },
                copyClipboardBankAccount() {
                    navigator.clipboard.writeText(this.bankAccount)
                    this.copied = true
                    setTimeout(() => this.copied = false, 2000);
                }
            }))
        </script>
    @endscript
</section>
