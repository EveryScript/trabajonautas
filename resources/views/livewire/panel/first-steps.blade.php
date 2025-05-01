<div class="bg-gray-100 min-h-screen flex items-start justify-center py-10">
    <div x-data="content" class="w-full max-w-md md:max-w-5xl">
        <div class="p-6 md:p-10 bg-white rounded-lg shadow-lg">
            <x-application-logo class="{{ $step == 4 ? 'hidden' : '' }}" />
            <!-- Progress Indicator -->
            <div class="flex flex-row justify-between items-center my-6 {{ $step == 4 ? 'hidden' : '' }}">
                <div class="flex items-center">
                    <div id="step1"
                        class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full bg-tbn-primary text-white text-sm md:text-base">
                        1</div>
                    <span class="ml-2 text-gray-700 text-sm md:text-base hidden md:block">Información personal</span>
                </div>
                <div class="flex items-center">
                    <div id="step2"
                        class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full {{ $step == 2 || $step == 3 ? 'bg-tbn-primary text-white' : 'bg-gray-300 text-gray-500' }} text-sm md:text-base">
                        2</div>
                    <span class="ml-2 text-gray-700 text-sm md:text-base hidden md:block">Información profesional</span>
                </div>
                <div class="flex items-center">
                    <div id="step3"
                        class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full {{ $step == 3 ? 'bg-tbn-primary text-white' : 'bg-gray-300 text-gray-500' }} text-sm md:text-base">
                        3</div>
                    <span class="ml-2 text-gray-700 text-sm md:text-base hidden md:block">Confirmación</span>
                </div>
            </div>
            <!-- Steps -->
            @if ($step == 1)
                <form wire:submit='savePersonalData'>
                    <div id="stepContent1" class="step-content">
                        <h3 class="text-lg md:text-2xl font-semibold mb-2">Hola {{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2">Ingresa tus para completar el registro en
                            Trabajonautas.com</p>
                        <h5 class="text-md font-bold mb-2">¿Cuál es tu genero?</h5>
                        <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto mb-8">
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='gender' value="M"
                                    id="gender-1" name="gender" class="hidden peer">
                                <label for="gender-1"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span>masculino</span>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='gender' value="F"
                                    id="gender-2" name="gender" class="hidden peer">
                                <label for="gender-2"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span>femenino</span>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='gender' value="0"
                                    id="gender-3" name="gender" class="hidden peer">
                                <label for="gender-3"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span>prefiero no decirlo</span>
                                </label>
                            </li>
                        </ul>
                        <h5 class="text-md font-bold mb-2">¿Cuál es tu edad?</h5>
                        <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto mb-8">
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='age' value="1" id="age-1"
                                    name="age" class="hidden peer">
                                <label for="age-1"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span>de 18 a 25 años</span>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='age' value="2" id="age-2"
                                    name="age" class="hidden peer">
                                <label for="age-2"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span>de 26-32 años</span>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='age' value="3" id="age-3"
                                    name="age" class="hidden peer">
                                <label for="age-3"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span>de 33 en adelante</span>
                                </label>
                            </li>
                        </ul>
                        <h5 class="text-md font-bold mb-2">¿Cuál es tu ubicación actual?</h5>
                        <x-select wire:model.live.debounce.200ms='location_id' id="location_id" class="mb-8">
                            <option value="">Selecciona tu ubicación</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endforeach
                        </x-select>
                        <h5 class="text-md font-bold mb-2">Celular (WhatsApp)</h5>
                        <x-input type="text" wire:model.live.debounce.200ms='phone' id="phone"
                            placeholder="Ej. 71234567" />
                    </div>
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6">
                        <button id="prevButton" type="button"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded disabled:opacity-50 disabled:cursor-not-allowed transition duration-300"
                            disabled>Anterior</button>
                        <button id="nextButton" type="submit"
                            {{ $gender && $age && $location_id && $phone ? '' : 'disabled' }}
                            class="px-4 py-2 bg-tbn-primary text-white rounded transition duration-300 hover:bg-tbn-primary disabled:opacity-50 disabled:cursor-not-allowed">Siguiente</button>
                    </div>
                </form>
            @elseif($step == 2)
                <form wire:submit='saveProfesionalData'>
                    <div class="step-content">
                        <h3 class="text-lg md:text-2xl font-semibold mb-2">Información profesional</h3>
                        <p class="text-sm mb-2">Selecciona tu grado académico actual</p>
                        <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto mb-8">
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="1" id="profile-1" name="grade-profile" class="hidden peer">
                                <label for="profile-1"
                                    class="flex justify-center items-center h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Estudiante</span>
                                        <span class="text-xs">Bachiller o en Unidad Educativa actualmente</span>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="2" id="profile-2" name="grade-profile" class="hidden peer">
                                <label for="profile-2"
                                    class="flex justify-center items-center h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Técnico Medio</span>
                                        <span class="text-xs">En carrera o con certificado obtenido</span>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="3" id="profile-3" name="grade-profile" class="hidden peer">
                                <label for="profile-3"
                                    class="flex justify-center items-center h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Técnico Superior</span>
                                        <span class="text-xs">En carrera o con certificado obtenido</span>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="4" id="profile-4" name="grade-profile" class="hidden peer">
                                <label for="profile-4"
                                    class="flex justify-center items-center h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Egresado</span>
                                        <span class="text-xs leading-none m-0">Terminó todas las materias y solamente
                                            le falta la tesis</span>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="5" id="profile-5" name="grade-profile" class="hidden peer">
                                <label for="profile-5"
                                    class="flex justify-center items-center h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Titulado</span>
                                        <span class="text-xs">Actualmente con un título universitario</span>
                                    </div>
                                </label>
                            </li>
                        </ul>
                        <h5 class="font-bold">Area profesional</h5>
                        <span class="block text-tbn-dark text-sm mb-2">Trabajonautas seleccionará las mejores
                            convocatorias de
                            trabajo para ti en base al area profesional que elijas.</span>
                        <x-select id="area" wire:model.live.debounce.200ms='area_id' class="mb-4">
                            <option value="">Selecciona un area</option>
                            @forelse ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                            @empty
                                <option>Empty</option>
                            @endforelse
                        </x-select>
                    </div>
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6">
                        <button id="prevButton" type="button" wire:click="stepBack"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded disabled:opacity-50 disabled:cursor-not-allowed transition duration-300">Anterior</button>
                        <button id="nextButton" type="submit" {{ $grade_profile_id && $area_id ? '' : 'disabled' }}
                            class="px-4 py-2 bg-tbn-primary text-white rounded disabled:opacity-50 disabled:cursor-not-allowed transition duration-300 hover:bg-tbn-primary">Siguiente</button>
                    </div>
                </form>
            @elseif($step == 3)
                <form wire:submit='savePurchaseData'>
                    <div class="step-content">
                        <h3 class="text-lg md:text-2xl font-semibold mb-4">Cuenta</h3>
                        <h5 class="text-md font-bold mb-2">Selecciona el tipo de cuenta que vas a utilizar</h5>
                        <div class="flex flex-wrap">
                            @forelse ($account_types as $account_type)
                                <div class="w-full sm:w-1/2 lg:w-1/3 px-2 mb-8">
                                    <input type="radio" wire:model.live.debounce.200ms='account_type_id'
                                        id="{{ 'account-' . $account_type->name }}" value="{{ $account_type->id }}"
                                        class="hidden peer" name="account_type">
                                    <label for="{{ 'account-' . $account_type->name }}"
                                        x-on:click="changeAccountData({{ $account_type->id }}, '{{ $account_type->name }}', '{{ $account_type->price }}', '{{ $account_type->duration_days }}')"
                                        class="block bg-white p-6 rounded-lg shadow-lg border-2 border-gray-200 peer-checked:border-tbn-primary">
                                        <h2 class="text-2xl font-semibold text-gray-800 capitalize">
                                            {{ $account_type->name }}</h2>
                                        <div class="mt-4">
                                            <span class="text-5xl font-bold text-gray-900">{{ $account_type->price }}
                                                Bs.</span>
                                            <span class="text-gray-600">
                                                {{ $account_type->duration_days == 0 ? 'Siempre' : '/ ' . $account_type->duration_days . ' dias' }}</span>
                                        </div>
                                        <ul class="mt-6 space-y-2 text-sm">
                                            <li class="flex items-center">
                                                <i class="fas fa-check text-green-500 mr-2"></i> Tiempo de uso: 30 dias
                                            </li>
                                            <li class="flex items-center">
                                                <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias estandar
                                            </li>
                                            <li class="flex items-center">
                                                @if ($account_type->id == 1)
                                                    <i class="fas fa-times text-red-500 mr-2"></i>
                                                @else
                                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                                @endif
                                                Convocatorias Premium
                                            </li>
                                            <li class="flex items-center">
                                                @if ($account_type->id == 1 || $account_type->id == 2)
                                                    <i class="fas fa-times text-red-500 mr-2"></i>
                                                @else
                                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                                @endif
                                                Notificaciones en tiempo real
                                            </li>
                                        </ul>
                                    </label>
                                </div>
                            @empty
                                <p class="px-4 py-3 text-center">No hay nada para mostrar</p>
                            @endforelse
                        </div>
                    </div>
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6">
                        <button id="prevButton" wire:click="stepBack"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded disabled:opacity-50 disabled:cursor-not-allowed transition duration-300">Anterior</button>

                        <button id="nextButton" type="submit" x-text="btnAccountFinish"
                            class="px-4 py-2 bg-tbn-primary text-white rounded transition duration-300 hover:bg-tbn-primary"></button>
                    </div>
                </form>
            @elseif($step == 4)
                <form wire:submit='saveProAccountData(user_profesions, user_area)'>
                    <div class="step-content">
                        <div class="flex flex-row gap-12">
                            <div class="flex-1">
                                <h3 class="text-lg md:text-2xl text-tbn-primary font-semibold mb-2">
                                    Trabajonautas PRO</h3>
                                <p class="text-sm text-gray-500 mb-4">Estás listo para disfrutar de todos los
                                    beneficios de
                                    Trabajonautas. Simplemente sigue las instrucciones para tener tu cuenta PRO desde
                                    ahora.</p>
                                <h5 class="font-bold">¿Cual es tu profesión(es) actual?</h5>
                                <span class="block mb-2 text-xs text-tbn-dark">Te enviaremos información de acuerdo con
                                    las
                                    profesiones que selecciones acontinuación. Estos datos se pueden cambiar más
                                    adelante.</span>
                                <x-input type="search" x-model="searchProfesion" id="searchProfesion"
                                    placeholder="Arquitecto, ingeniero, ..." />
                                <!-- Profesion list -->
                                <div class="relative">
                                    <ul class="absolute w-full mx-auto max-h-[10rem] overflow-y-auto"
                                        x-show="searchProfesion.length > 0">
                                        <template x-for="profesion in filteredProfesions">
                                            <li x-text="profesion.profesion_name" @click="addProfesion(profesion)"
                                                class="bg-white text-sm border border-gray-200 px-4 py-2 rounded-sm hover:bg-gray-200">
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <!-- Profesion selected -->
                                <ul class="mx-auto max-w-2xl flex flex-row flex-wrap gap-1 mt-3 mb-4">
                                    <template x-for="profesion in selected_profesions" class="h-full">
                                        <li
                                            class="flex flex-row px-3 py-2 rounded-full text-white text-xs bg-tbn-dark">
                                            <span x-text="profesion.profesion_name"></span>
                                            <button type="button" class="ml-2 text-xs"
                                                @click="removeProfesion(profesion)"><i
                                                    class="fas fa-times"></i></button>
                                        </li>
                                    </template>
                                </ul>

                            </div>
                            <div class="w-2/5">
                                <p class="text-sm text-gray-500 mb-4 text-center">Escanea el código QR para realizar el
                                    pago</p>
                                <picture class="block max-w-[10rem] mx-auto mb-4">
                                    <img class="w-full" src="{{ asset('storage/img/qr.png') }}" alt="qr-code">
                                </picture>
                                {{-- Description table --}}
                                <table class="min-w-full divide-y divide-gray-200 mb-2">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="py-1 whitespace-nowrap font-bold">Tipo de cuenta</td>
                                            <td class="py-1 whitespace-nowrap uppercase" x-text="labelAccountName">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 whitespace-nowrap font-bold">Costo</td>
                                            <td class="py-1 whitespace-nowrap" x-text="labelAccountPrice + ' Bs.'">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 whitespace-nowrap font-bold">Duración</td>
                                            <td class="py-1 whitespace-nowrap"
                                                x-text="labelAccountDuration + ' dias'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                {{-- Payment options --}}
                                <p class="text-sm text-gray-500 mt-2 mb-4 text-center">Alternativas de pago</p>
                                <div class="relative px-4 py-3 bg-gray-300 mb-4">
                                    <span
                                        class="absolute -top-3 text-xs text-tbn-primary bg-gray-300 px-3 rounded-md">Banco
                                        Bisa</span>
                                    36621-54481-29402-6598
                                </div>
                                <p class="text-xs text-tbn-dark text-justify">Una vez realizado el depósito nuestros
                                    operadores se
                                    contactarán contigo para confirmar el depósito y habilitar tu cuenta.</p>
                            </div>
                        </div>

                    </div>
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6">
                        <button id="prevButton" type="button" wire:click="stepBack"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded disabled:opacity-50 disabled:cursor-not-allowed transition duration-300">Anterior</button>
                        <button id="nextButton" type="submit" x-bind:disabled="selected_profesions.length == 0"
                            class="px-4 py-2 bg-tbn-primary text-white rounded transition duration-300 hover:bg-tbn-primary disabled:opacity-50 disabled:cursor-not-allowed">Finalizar
                            registro</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                // Data
                profesions: {!! $profesions !!},
                // Models
                user_profesions: [],
                user_area: '',
                selected_profesions: [],
                btnAccountFinish: 'Finalizar registro',
                labelAccountName: '',
                labelAccountPrice: '',
                labelAccountDuration: '',
                searchProfesion: '',
                // Functions
                changeAccountData(accountId, accountName, accountPrice, accountDuration) {
                    this.btnAccountFinish = accountId == 1 ? 'Finalizar registro' : 'Pagar ahora (QR)'
                    this.labelAccountName = accountName
                    this.labelAccountPrice = accountPrice
                    this.labelAccountDuration = accountDuration
                },
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
                }
            }))
        </script>
    @endscript
</div>
