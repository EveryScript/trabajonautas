<section class="max-w-6xl mx-auto my-10">
    <div class="text-center mb-4">
        <i class="fas fa-crown text-[3rem] text-orange-500 mb-4"></i>
        <h5 class="font-medium text-xl">Adquiere tu cuenta de <span class="text-tbn-primary">Trabajonautas
                PRO</span> ahora mismo</h5>
        <p class="text-tbn-dark text-sm">Convocatorías exclusivas y nuevas opciones de búsqueda cada día al
            alcance de ti. </p>
    </div>
    <div x-data="content">
        {{-- Option cards --}}
        <div class="grid gap-8 mb-12 lg:grid-cols-3 p-4 md:p-8 mt-4" x-show="!showForm">
            <div class="relative">
                <div class="flex flex-col justify-between h-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-gray-900">Gratis</h3>
                        <p class="mt-2 text-sm text-gray-600">La mejor opción para comenzar</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold">0 Bs.</span>
                            <span class="ml-2 text-gray-600">(siempre)</span>
                        </div>
                        <ul class="my-6 space-y-2 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Tiempo de uso: Siempre
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias estandar
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-2"></i> Convocatorias Premium
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-2"></i> Notificaciones en tiempo
                                real
                            </li>
                        </ul>
                    </div>
                    <div class="p-6 border-t border-gray-200 rounded-b-lg bg-gray-50">
                        <a href="{{ !auth()->check() ? route('register') : route('dashboard') }}"
                            class="block w-full px-4 py-2 font-medium text-center text-white transition-colors bg-gray-900 rounded-lg hover:bg-gray-800">
                            Iniciar ahora
                            <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute left-0 right-0 flex justify-center -top-4">
                    <span
                        class="flex items-center gap-1 px-4 py-1 text-sm font-medium text-white rounded-full bg-gradient-to-r from-tbn-primary to-blue-800">
                        <i class="fas fa-star"></i> Mejor opción
                    </span>
                </div>
                <div
                    class="flex flex-col justify-between h-full bg-white border-2 border-tbn-primary rounded-lg shadow-lg">
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-gray-900">Pro</h3>
                        <p class="mt-2 text-gray-600 text-sm">Convocatorias exclusivas y beneficios al instante</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold">10 Bs.</span>
                            <span class="ml-2 text-gray-600">/mes</span>
                        </div>
                        <ul class="my-6 space-y-2 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Tiempo de uso: 30 dias
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias estandar
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias Premium
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-2"></i> Notificaciones en tiempo
                                real
                            </li>
                        </ul>
                    </div>
                    <div class="p-6 border-t border-gray-200 rounded-b-lg bg-gray-50">
                        @if (auth()->check())
                            <a @click="purchase.account = 'Pro'; purchase.price = 10; purchase.duration = 30; showForm = true"
                                class="block w-full px-4 py-2 font-medium text-center text-white transition-colors rounded-lg bg-gradient-to-r from-tbn-primary to-blue-800 hover:from-blue-800 hover:to-blue-900">
                                Comprar ahora
                                <i class="fas fa-arrow-right ml-2"></i></a>
                        @else
                            <a href="{{ route('register') }}" wire:navigate
                                class="block w-full px-4 py-2 font-medium text-center text-white transition-colors rounded-lg bg-gradient-to-r from-tbn-primary to-blue-800 hover:from-blue-800 hover:to-blue-900">
                                Comprar ahora
                                <i class="fas fa-arrow-right ml-2"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="flex flex-col justify-between h-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-gray-900">Full</h3>
                        <p class="mt-2 text-gray-600 text-sm">Navega sin límites en nuestra plataforma Trabajonautas.
                        </p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold">20 Bs.</span>
                            <span class="ml-2 text-gray-600">/mes</span>
                        </div>
                        <ul class="my-6 space-y-2 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Tiempo de uso: 60 dias
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias estandar
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias Premium
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i> Notificaciones en
                                tiempo
                                real
                            </li>
                        </ul>
                    </div>
                    <div class="p-6 border-t border-gray-200 rounded-b-lg bg-gray-50">
                        @if (auth()->check())
                            <a @click="purchase.account = 'Full'; purchase.price = 20; purchase.duration = 60; showForm = true"
                                class="block w-full px-4 py-2 font-medium text-center text-white transition-colors bg-gray-900 rounded-lg hover:bg-gray-800">
                                Comprar ahora
                                <i class="fas fa-arrow-right ml-2"></i></a>
                        @else
                            <a href="{{ route('register') }}" wire:navigate
                                class="block w-full px-4 py-2 font-medium text-center text-white transition-colors bg-gray-900 rounded-lg hover:bg-gray-800">
                                Comprar ahora
                                <i class="fas fa-arrow-right ml-2"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Payment section --}}
        <div class="max-w-4xl p-4 md:p-8 bg-white rounded-lg shadow-md mx-auto" x-show="showForm">
            <div class="flex flex-row gap-8">
                <div class="w-2/5">
                    <p class="text-sm text-gray-500 mb-4 text-center">Escanea el código QR para realizar el
                        pago</p>
                    <picture class="block max-w-[10rem] mx-auto mb-6">
                        <img class="w-full" src="{{ asset('storage/img/qr.png') }}" alt="qr-code">
                    </picture>
                    {{-- Payment options --}}
                    <p class="text-sm text-gray-500 mt-2 mb-4 text-center">o selecciona una alternativa de pago</p>
                    <div class="relative px-4 py-3 bg-gray-200 mb-6">
                        <span class="absolute -top-2 text-xs text-tbn-primary bg-gray-200 px-3 rounded-md">Banco
                            Bisa</span>
                        36621-54481-29402-6598
                    </div>
                    <div class="relative px-4 py-3 bg-gray-200 mb-6">
                        <span class="absolute -top-2 text-xs text-tbn-primary bg-gray-200 px-3 rounded-md">Banco
                            Bisa</span>
                        36621-54481-29402-6598
                    </div>
                </div>
                <div class="flex-1">
                    <h5 class="font-bold text-lg">¿Cual es tu profesión(es) actual?</h5>
                    <span class="block mb-2 text-xs text-tbn-dark">Te enviaremos información de acuerdo con
                        las
                        profesiones que selecciones acontinuación. Estos datos se pueden cambiar más
                        adelante.</span>
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
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Nombre</td>
                                <td class="py-1 whitespace-nowrap">{{ auth()->check() ? auth()->user()->name : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Celular</td>
                                <td class="py-1 whitespace-nowrap">{{ auth()->check() ? auth()->user()->phone : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Tipo de cuenta</td>
                                <td class="py-1 whitespace-nowrap uppercase" x-text="purchase.account"></td>
                            </tr>
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Costo</td>
                                <td class="py-1 whitespace-nowrap" x-text="purchase.price + ' Bs.'"></td>
                            </tr>
                            <tr>
                                <td class="py-1 whitespace-nowrap font-medium">Duración</td>
                                <td class="py-1 whitespace-nowrap" x-text="purchase.duration + ' dias'"></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="text-center mb-6">
                <x-button type="button" @click="saveChanges" x-bind:disabled="user_profesions.length == 0">Confirmar
                    pago</x-button>
                <x-secondary-button type="button" @click="showForm = false">Elegir otro
                    paquete</x-secondary-button>
            </div>
            <p class="text-xs text-tbn-dark text-justify">Una vez realizado el depósito nuestros
                operadores se
                contactarán contigo para confirmar el depósito y habilitar tu cuenta.</p>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                // Data
                showForm: false,
                profesions: {!! $profesions !!},
                // Models
                user_profesions: [],
                selected_profesions: [],
                searchProfesion: '',
                purchase: {
                    account: '',
                    price: 0,
                    duration: ''
                },
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
                        $wire.saveChanges(this.purchase.duration, this.user_profesions)
                    }
                }
            }))
        </script>
    @endscript
</section>
