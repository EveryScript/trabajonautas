<section class="min-h-screen flex items-start justify-center py-10">
    <div x-data="content">
        {{-- Payment section --}}
        <div class="max-w-4xl p-4 md:p-8 bg-white rounded-lg shadow-md mx-auto">
            <div class="mb-8">
                <div class="mb-4">
                    <x-application-logo />
                </div>
                <h5 class="font-medium text-2xl">
                    @if ($account_type_id == 3)
                        Despega al infinito con tu cuenta
                    @else
                        Impulsa tus oportunidades con tu cuenta
                    @endif
                    <span class="text-tbn-primary">{{ $account_type->name }}</span>
                </h5>
                <p class="text-tbn-dark text-sm">Pero antes, necesitamos algunos datos para finalizar tu registro.</p>
            </div>
            <div class="flex flex-col md:flex-row gap-12 mb-6">
                <div class="w-full md:w-3/5">
                    <h5 class="font-bold text-lg">¿Cual es tu profesión(es) actual?</h5>
                    <span class="block mb-2 text-xs text-tbn-dark">Te enviaremos información de acuerdo con las
                        profesiones que selecciones acontinuación. Estos datos se pueden cambiar más adelante.</span>
                    <x-input type="search" x-model="searchProfesion" id="searchProfesion"
                        placeholder="Arquitecto, ingeniero, ..." autofocus />
                    <!-- Profesion list -->
                    <div class="relative">
                        <ul class="absolute w-full mx-auto max-h-[10rem] overflow-y-auto bg-white"
                            x-show="searchProfesion.length > 0">
                            <template x-for="profesion in filteredProfesions">
                                <li x-text="profesion.profesion_name" @click="addProfesion(profesion)"
                                    class="bg-white text-sm border border-gray-200 px-4 py-2 rounded-sm hover:bg-gray-200">
                                </li>
                            </template>
                        </ul>
                    </div>
                    <!-- Profesion selected -->
                    <ul class="mx-auto max-w-2xl flex flex-row flex-wrap gap-1 mt-3 mb-8">
                        <template x-for="profesion in selected_profesions" class="h-full">
                            <li class="flex flex-row px-3 py-2 rounded-full text-white text-xs bg-tbn-dark">
                                <span x-text="profesion.profesion_name"></span>
                                <button type="button" class="ml-2 text-xs" @click="removeProfesion(profesion)"><i
                                        class="fas fa-times"></i></button>
                            </li>
                        </template>
                    </ul>
                    <h5 class="font-bold text-lg">Resumen de la compra</h5>
                    <span class="block mb-2 text-xs text-tbn-dark">Revisa tus datos antes de enviarnos tu
                        depósito.</span>
                    <div class="bg-gray-50 rounded-md p-6 mb-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-1 whitespace-nowrap font-medium">Nombre</td>
                                    <td class="py-1 whitespace-nowrap">{{ auth()->check() ? auth()->user()->name : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1 whitespace-nowrap font-medium">Celular</td>
                                    <td class="py-1 whitespace-nowrap">
                                        {{ auth()->check() ? auth()->user()->phone : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1 whitespace-nowrap font-medium">Tipo de cuenta</td>
                                    <td class="py-1 whitespace-nowrap uppercase">{{ $account_type->name }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 whitespace-nowrap font-medium">Costo</td>
                                    <td class="py-1 whitespace-nowrap">{{ $account_type->price }} Bs.</td>
                                </tr>
                                <tr>
                                    <td class="py-1 whitespace-nowrap font-medium">Duración</td>
                                    <td class="py-1 whitespace-nowrap">{{ $account_type->duration_days }} días</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="w-full md:w-2/5">
                    <p class="text-sm text-gray-500 mb-6 text-center">
                        Escanea el código QR para realizar el pago</p>
                    <picture class="block max-w-[10rem] mx-auto mb-2">
                        <img class="w-full" src="{{ asset('storage/img/tbn-qr.png') }}" alt="qr-code">
                    </picture>
                    <div class="text-center mb-10">
                        <button wire:click='downloadQR'
                            class="text-tbn-primary text-xs px-3 py-2 rounded-full border border-tbn-primary hover:bg-tbn-primary hover:text-white transition-all duration-200">
                            Descargar QR</button>
                    </div>
                    <p class="text-sm text-gray-500 mb-6 text-center">o selecciona una alternativa de pago</p>
                    <div class="relative px-4 py-3 bg-gray-100 mb-4 rounded-md">
                        <span class="absolute -top-3 text-xs text-tbn-primary bg-gray-100 px-4 rounded-md">
                            Banco Bisa</span>
                        <span class="font-arial">36621-54481-29402-6598</span>
                    </div>
                    <p class="text-xs text-tbn-dark text-justify">
                        Una vez realizado el depósito nuestros operadores se comunicarán contigo para confirmar el
                        depósito y habilitar tu cuenta.</p>
                </div>
            </div>
            <div class="text-center mb-6">
                <x-button class="w-full sm:w-auto mb-2" type="button" @click="saveChanges"
                    x-bind:disabled="user_profesions.length == 0">
                    Confirmar pago</x-button>
                <x-secondary-button href="{{ route('purchase-cards') }}" class="w-full sm:w-auto" wire:navigate>
                    Elegir otro paquete</x-secondary-button>
            </div>

        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                // Data
                profesions: {!! $profesions !!},
                // Models
                user_profesions: [],
                selected_profesions: [],
                searchProfesion: '',
                // Functions
                filteredProfesions() {
                    return this.profesions.filter(
                        profesion => profesion.profesion_name.toLowerCase().includes(this.searchProfesion
                            .toLowerCase())
                    )
                },
                addProfesion(element) {
                    this.searchProfesion = ''
                    this.selected_profesions.push(element)
                    this.user_profesions.push(element.id)
                    this.profesions = this.profesions.filter(profesion => profesion.id != element.id)
                },
                removeProfesion(element) {
                    this.selected_profesions = this.selected_profesions.filter(profesion => profesion.id != element
                        .id)
                    this.user_profesions = this.user_profesions.filter(id => id != element.id)
                    this.profesions.unshift(element)
                },
                saveChanges() {
                    if (this.user_profesions.length != 0) {
                        $wire.saveChanges(this.user_profesions)
                    }
                }
            }))
        </script>
    @endscript
</section>
