<div class="bg-gray-100 min-h-screen flex items-start justify-center py-10">
    <div x-data="content" class="w-full max-w-xl md:max-w-4xl">
        <div class="p-6 md:p-10 bg-white rounded-lg shadow-lg mx-2">
            <div class="max-w-[15rem] mx-auto">
                <x-application-logo />
            </div>
            <!-- Progress Indicator -->
            <div class="flex flex-row justify-between items-center my-6 {{ $step == 0 ? 'hidden' : '' }}">
                <div class="flex items-center">
                    <div id="step1"
                        class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full bg-tbn-primary text-white text-sm md:text-base">
                        1</div>
                    <span class="ml-2 text-gray-700 text-sm hidden md:block">Información personal</span>
                </div>
                <div class="flex items-center">
                    <div id="step2"
                        class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full {{ $step == 2 || $step == 3 ? 'bg-tbn-primary text-white' : 'bg-gray-300 text-gray-500' }} text-sm md:text-base">
                        2</div>
                    <span class="ml-2 text-gray-700 text-sm hidden md:block">Información profesional</span>
                </div>
                <div class="flex items-center">
                    <div id="step3"
                        class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full {{ $step == 3 ? 'bg-tbn-primary text-white' : 'bg-gray-300 text-gray-500' }} text-sm md:text-base">
                        3</div>
                    <span class="ml-2 text-gray-700 text-sm hidden md:block">Confirmación</span>
                </div>
            </div>
            <!-- Terms and conditions (step) -->
            @if ($step == 0)
                <div class="step-content mt-4">
                    <h5 class="font-medium text-2xl mb-2">Términos y condiciones</h5>
                    <p class="text-sm text-tbn-dark mb-2">
                        Hola {{ auth()->user()->name }}, bienvenido a <strong
                            class="text-tbn-primary">trabajonautas.com</strong></p>
                    <p class="text-sm text-tbn-dark mb-2">Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                        Debitis
                        architecto, earum, ducimus exercitationem voluptates pariatur ipsam magnam a odio mollitia,
                        maiores quae corrupti. Magnam nobis eos obcaecati, iusto odit laborum!</p>
                    <p class="text-sm text-tbn-dark mb-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Pariatur
                        necessitatibus consectetur perferendis, maiores libero tempore laborum nemo iste, praesentium
                        quo quam recusandae. Nemo porro praesentium quos dolorum laudantium quibusdam ratione,
                        consequatur quidem facilis id doloremque ipsam nihil eaque tempora quod non sed molestias
                        incidunt molestiae veniam, ea eos? Tempore, placeat?</p>
                    <p class="text-sm text-tbn-dark mb-4">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quos
                        inventore autem minus non possimus totam ducimus eaque sit pariatur earum mollitia incidunt,
                        soluta voluptatem perferendis vel, quae accusamus eveniet in, rem tenetur recusandae hic quam
                        atque nam. Soluta, pariatur consequatur.</p>
                    <x-button wire:click="$set('step', 1)">Aceptar y continuar</x-button>
                </div>
                <!-- Steps -->
            @elseif ($step == 1)
                <form wire:submit='savePersonalData'>
                    <div id="stepContent1" class="step-content">
                        <h3 class="text-lg md:text-2xl font-semibold mb-2">Hola {{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2">
                            Ingresa tus para completar el registro en Trabajonautas.com</p>
                        <h5 class="text-md font-bold mb-2">¿Cuál es tu genero?</h5>
                        <ul class="grid grid-cols-2 gap-1 mx-auto mb-8">
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='gender' value="M"
                                    id="gender-1" name="gender" class="hidden peer">
                                <label for="gender-1"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span class="text-sm md:text-md">masculino</span>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='gender' value="F"
                                    id="gender-2" name="gender" class="hidden peer">
                                <label for="gender-2"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span class="text-sm md:text-md">femenino</span>
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
                                    <span class="text-sm md:text-md">de 18 a 25 años</span>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='age' value="2" id="age-2"
                                    name="age" class="hidden peer">
                                <label for="age-2"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span class="text-sm md:text-md">de 26-32 años</span>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='age' value="3" id="age-3"
                                    name="age" class="hidden peer">
                                <label for="age-3"
                                    class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <span class="text-sm md:text-md">de 33 en adelante</span>
                                </label>
                            </li>
                        </ul>
                        <h5 class="text-md font-bold mb-1">¿Cuál es tu ubicación actual?</h5>
                        <span class="block text-tbn-dark text-xs md:text-sm mb-2">
                            Esta información nos servirá para conocer en que departamento deseas trabajar.</span>
                        <x-select wire:model.live.debounce.200ms='location_id' id="location_id" class="mb-8">
                            <option value="">Selecciona tu ubicación</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endforeach
                        </x-select>
                        <h5 class="text-md font-bold mb-1">Celular (WhatsApp)</h5>
                        <span class="block text-tbn-dark text-xs md:text-sm mb-2">
                            Nos comunicaremos contigo utilizando este número.</span>
                        <x-input type="number" wire:model.live.debounce.200ms='phone' id="phone"
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
                        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1 mx-auto mb-8">
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="1" id="profile-1" name="grade-profile" class="hidden peer">
                                <label for="profile-1"
                                    class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Estudiante</span>
                                        <p class="text-xs">Bachiller o cursante de instituto o universidad</p>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="2" id="profile-2" name="grade-profile" class="hidden peer">
                                <label for="profile-2"
                                    class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Técnico Medio</span>
                                        <p class="text-xs">Profesional titulado a nivel tecnico medio</p>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="3" id="profile-3" name="grade-profile" class="hidden peer">
                                <label for="profile-3"
                                    class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Técnico Superior</span>
                                        <p class="text-xs">Profesional titulado a nivel tecnico superior</p>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="4" id="profile-4" name="grade-profile" class="hidden peer">
                                <label for="profile-4"
                                    class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Egresado</span>
                                        <p class="text-xs m-0">Aprobó todas las materias y solamente le
                                            falta la tesis.</p>
                                    </div>
                                </label>
                            </li>
                            <li class="text-center">
                                <input type="radio" wire:model.live.debounce.200ms='grade_profile_id'
                                    value="5" id="profile-5" name="grade-profile" class="hidden peer">
                                <label for="profile-5"
                                    class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span class="block font-bold uppercase">Titulado</span>
                                        <p class="text-xs">Actualmente con titulo en provisión nacional.</p>
                                    </div>
                                </label>
                            </li>
                        </ul>
                        <h5 class="font-bold">Area profesional</h5>
                        <span class="block text-tbn-dark text-xs md:text-sm mb-2">
                            Trabajonautas seleccionará las mejores convocatorias de trabajo para ti en base al area
                            profesional que elijas.</span>
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
                <form wire:submit='saveAccountData'>
                    <div class="step-content">
                        <h5 class="text-sm font-medium mb-2">Selecciona el tipo de cuenta que vas a utilizar</h5>
                        <div class="flex flex-wrap">
                            @forelse ($account_types as $account_type)
                                <div class="w-full sm:w-1/2 lg:w-1/3 px-1 mb-8">
                                    <input type="radio" wire:model.live.debounce.200ms='account_type_id'
                                        id="{{ 'account-' . $account_type->name }}" value="{{ $account_type->id }}"
                                        class="hidden peer" name="account_type">
                                    <label for="{{ 'account-' . $account_type->name }}"
                                        x-on:click="changeBtnLabel({{ $account_type->id }})"
                                        class="block bg-white p-6 rounded-lg shadow-lg border-2 border-gray-200 peer-checked:border-tbn-primary">
                                        <h2 class="text-xl md:text-2xl font-semibold text-tbn-primary capitalize">
                                            {{ $account_type->name }}</h2>
                                        <div class="mt-2 md:mt-2">
                                            <span class="text-3xl md:text-4xl font-bold text-gray-900">
                                                {{ $account_type->price }} Bs.</span>
                                            <span class="text-tbn-dark font-medium">
                                                {{ $account_type->duration_days == 0 ? 'Siempre' : '/ ' . $account_type->duration_days . ' dias' }}</span>
                                        </div>
                                        <ul class="mt-3 md:mt-6 space-y-2 text-xs md:text-sm">
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
                            {{ $account_type_id ? '' : 'disabled' }}
                            class="px-4 py-2 bg-tbn-primary text-white rounded disabled:opacity-50 disabled:cursor-not-allowed transition duration-300 hover:bg-tbn-primary"></button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                // Models
                btnAccountFinish: 'Finalizar registro',
                // Functions
                changeBtnLabel(accountId) {
                    this.btnAccountFinish = accountId == 1 ? 'Finalizar registro' : 'Siguiente'
                }
            }))
        </script>
    @endscript
</div>
