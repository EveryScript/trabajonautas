<section class="bg-gray-50 min-h-screen flex items-start justify-center py-10">
    <div x-data="content" class="w-full max-w-xl md:max-w-3xl">
        <div class="p-6 md:p-10 bg-white rounded-lg shadow-lg mx-2">
            <div class="max-w-60 mb-3">
                <x-application-logo />
            </div>
            <h3 class="text-lg md:text-xl font-semibold mb-1">
                Hola {{ auth()->user()->name }}</h3>
            <p class="text-sm text-tbn-secondary mb-4">Estamos listos para despegar contigo. Ingresa tu información para
                completar tu registro.</p>
            <!-- Step 1 -->
            <div x-show="step === 1">
                <h5 class="text-md font-bold mb-2">¿Cuál es tu genero?</h5>
                <ul class="grid grid-cols-2 gap-1 mx-auto mb-8">
                    <li class="text-center">
                        <input type="radio" x-model='gender' value="M" id="gender-1" name="gender"
                            class="hidden peer">
                        <label for="gender-1"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <span class="text-sm md:text-md">masculino</span>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio" x-model='gender' value="F" id="gender-2" name="gender"
                            class="hidden peer">
                        <label for="gender-2"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <span class="text-sm md:text-md">femenino</span>
                        </label>
                    </li>
                </ul>
                <h5 class="text-md font-bold mb-2">¿Cuál es tu edad?</h5>
                <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto mb-8">
                    <li class="text-center">
                        <input type="radio" x-model='age' value="1" id="age-1" name="age"
                            class="hidden peer">
                        <label for="age-1"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <span class="text-sm md:text-md">de 18 a 25 años</span>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio" x-model='age' value="2" id="age-2" name="age"
                            class="hidden peer">
                        <label for="age-2"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <span class="text-sm md:text-md">de 26-32 años</span>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio" x-model='age' value="3" id="age-3" name="age"
                            class="hidden peer">
                        <label for="age-3"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <span class="text-sm md:text-md">de 33 en adelante</span>
                        </label>
                    </li>
                </ul>
                <h5 class="text-md font-bold mb-1">Celular (WhatsApp)</h5>
                <div class="flex items-center">
                    <select id="country-code" name="country-code" disabled
                        class="ps-3 pe-8 py-2 border border-gray-300 rounded-l-md focus:outline-none text-gray-700 bg-white disabled:cursor-not-allowed">
                        <option value="+591" selected>+591</option>
                    </select>
                    <x-input type="number" x-model="phone" inputmode="numeric" id="phone" placeholder="67891011" />
                </div>
                </label>
                <div class="flex justify-between mt-4">
                    <x-btn-secondary type="button" disabled>Anterior</x-btn-secondary>
                    <x-btn-primary type="button" x-on:click="step = 2"
                        x-bind:disabled="!gender || !age || !isValidPhone()">
                        Siguiente</x-btn-primary>
                </div>
            </div>
            <!-- Step 2 -->
            <div x-show="step === 2" x-cloak>
                <h5 class="text-md font-bold mb-2">¿Cuál es tu grado académico?</h5>
                <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1 mx-auto mb-8">
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="1" id="profile-1"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-1"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <div>
                                <span class="block font-bold uppercase">Estudiante</span>
                                <p class="text-xs">Bachiller o cursante de instituto o universidad</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="2" id="profile-2"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-2"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <div>
                                <span class="block font-bold uppercase">Técnico Medio</span>
                                <p class="text-xs">Profesional titulado a nivel tecnico medio</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="3" id="profile-3"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-3"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <div>
                                <span class="block font-bold uppercase">Técnico Superior</span>
                                <p class="text-xs">Profesional titulado a nivel tecnico superior</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="4" id="profile-4"
                            name="grade-profile" class="hidden peer">
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
                        <input type="radio"x-model='grade_profile_id' value="5" id="profile-5"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-5"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                            <div>
                                <span class="block font-bold uppercase">Titulado</span>
                                <p class="text-xs">Actualmente con titulo en provisión nacional.</p>
                            </div>
                        </label>
                    </li>
                </ul>
                <div class="flex justify-between mt-4">
                    <x-btn-secondary type="button" x-on:click="step = 1"> Anterior</x-btn-secondary>
                    <x-btn-primary type="button" x-on:click="step = 3" x-bind:disabled="!grade_profile_id">
                        Siguiente</x-btn-primary>
                </div>
            </div>
            <!-- Step 3 -->
            <div x-show="step === 3" x-cloak>
                <h5 class="text-md font-bold mb-2">¿Cuál es tu profesión?</h5>
                <x-input type="search" x-model="searchProfesion" class="mb-2" id="searchProfesion"
                    placeholder="Busca una profesión" />
                <div class="h-[18rem] overflow-y-auto bg-white border border-gray-200 p-2 rounded-lg scrollbar-none">
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-1 mx-auto mb-8">
                        <template x-for="profesion in filteredProfesions">
                            <li class="text-center" :key="'profesion' + profesion.id">
                                <input type="radio" x-on:click="profesion_id = profesion.id" :value="profesion.id"
                                    :id="'profesion-' + profesion.id" name="profesion" class="hidden peer">
                                <label :for="'profesion-' + profesion.id"
                                    class="flex justify-center items-center h-12 sm:h-[5rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                    <div>
                                        <span
                                            x-text="profesion.profesion_name"class="block font-medium text-sm"></span>
                                    </div>
                                </label>
                            </li>
                        </template>
                        <li x-show="filteredProfesions().length === 0 && searchProfesion.length > 0"
                            class="text-center text-xs text-tbn-secondary py-20 md:col-span-2">
                            No se encontraron resultados</li>
                    </ul>
                </div>
                <div class="flex justify-between mt-4">
                    <x-btn-secondary type="button" x-on:click="step = 2">
                        Anterior</x-btn-secondary>
                    <x-btn-primary type="button" x-on:click="step = 4" x-bind:disabled="!profesion_id">
                        Siguiente</x-btn-primary>
                </div>
            </div>
            <!-- Step 4 -->
            <div x-show="step === 4" x-cloak>
                <h5 class="text-md font-bold mb-2">¿Cuál es tu ubicación actual?</h5>
                <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto mb-8">
                    <template x-for="location in locations">
                        <li class="text-center" :key="'location-' + location.id">
                            <input type="radio" x-on:click="location_id = location.id" :value="location.id"
                                :id="'location-' + location.id" name="location" class="hidden peer">
                            <label :for="'location-' + location.id"
                                class="flex justify-center items-center h-12 sm:h-[4rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                <div>
                                    <span x-text="location.location_name" class="block font-medium text-sm"></span>
                                </div>
                            </label>
                        </li>
                    </template>
                </ul>
                <div class="flex justify-between mt-4">
                    <x-btn-secondary type="button" x-on:click="step = 3">
                        Anterior</x-btn-secondary>
                    <x-btn-primary type="button" x-on:click="step = 5" x-bind:disabled="!location_id">
                        Siguiente</x-btn-primary>
                </div>
            </div>
            <!-- Step 5 -->
            <div x-show="step === 5" x-cloak>
                <h5 class="text-md font-bold mb-2">Elige una cuenta</h5>
                <ul class=" grid grid-cols-1 md:grid-cols-3 gap-1">
                    <template x-for="accountType in accountTypes">
                        <li :key="'account-' + accountType.id">
                            <input type="radio" x-on:click="account_type_id = accountType.id"
                                :id="'account-' + accountType.id" :value="accountType.id" class="hidden peer"
                                name="account_type">
                            <label :for="'account-' + accountType.id"
                                class="block cursor-pointer bg-white p-6 rounded-lg shadow-lg border-2 border-gray-200 peer-checked:border-tbn-primary">
                                <h2 x-text="accountType.name"
                                    class="text-xl font-semibold text-tbn-primary capitalize"></h2>
                                <div class="mt-2 md:mt-2">
                                    <span x-text="accountType.price+' Bs.'"
                                        class="text-3xl md:text-4xl font-bold text-gray-900"></span>
                                    <span
                                        x-text="accountType.duration_days == 0 ? '/ Siempre' : '/ '+accountType.duration_days+' dias'"
                                        class="text-tbn-dark font-medium"></span>
                                </div>
                                <ul class="mt-3 md:mt-6 space-y-2 text-xs md:text-sm">
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        Convocatorias estandar
                                    </li>
                                    <li class="flex items-center">
                                        <i x-show="accountType.id == 1" class="fas fa-times text-red-500 mr-2"></i>
                                        <i x-show="accountType.id == 2 || accountType.id == 3"
                                            class="fas fa-check text-green-500 mr-2"></i>
                                        Convocatorias PRO
                                    </li>
                                    <li class="flex items-center">
                                        <i x-show="accountType.id == 1 || accountType.id == 2"
                                            class="fas fa-times text-red-500 mr-2"></i>
                                        <i x-show="accountType.id == 3" class="fas fa-check text-green-500 mr-2"></i>
                                        Notificaciones en tiempo real
                                    </li>
                                </ul>
                            </label>
                        </li>
                    </template>
                </ul>
                <div class="flex justify-between mt-4">
                    <x-btn-secondary type="button" x-on:click="step = 4">
                        Anterior</x-btn-secondary>
                    <x-btn-primary type="submit" x-on:click="isProAccountSelected"
                        x-bind:disabled="!account_type_id">
                        <span wire:loading.remove>Siguiente</span>
                        <span wire:loading><i class="fas fa-spinner text-sm animate-spin"></i></span>
                    </x-btn-primary>
                </div>
            </div>
            <!-- Step 6 -->
            <div x-show="step === 6" x-cloak>
                <h5 class="text-md font-bold mb-1">Resumen de la compra</h5>
                <span class="block mb-2 text-xs text-tbn-dark">
                    Revisa tus datos y escanea el código QR para realizar tu depósito.</span>
                <div class="w-full flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-3/5 text-sm">
                        <div class="bg-gray-50 rounded-md p-2 mb-4">
                            <table class="divide-y divide-gray-200">
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="p-2 whitespace-nowrap font-medium">Nombre</td>
                                        <td x-text="user.name" class="p-2 whitespace-wrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 whitespace-nowrap font-medium">Celular</td>
                                        <td x-text="user.phone" class="p-2 whitespace-nowrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 whitespace-nowrap font-medium">Tipo de cuenta</td>
                                        <td x-text="user.account_name" class="p-2 whitespace-nowrap uppercase">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 whitespace-nowrap font-medium">Costo</td>
                                        <td x-text="user.account_price +' Bs.'" class="p-2 whitespace-nowrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 whitespace-nowrap font-medium">Duración</td>
                                        <td x-text="user.account_duration +' días'" class="p-2 whitespace-nowrap">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-tbn-dark text-justify">
                            Una vez realizado el depósito nuestros operadores se comunicarán contigo para confirmar
                            el
                            depósito y habilitar tu cuenta.</p>
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
                        <div class="relative px-4 py-3 bg-gray-100 mb-4 rounded-md">
                            <span class="absolute -top-3 text-xs text-tbn-primary bg-gray-100 px-4 py-1 rounded-full">
                                Banco Bisa</span>
                            <span class="font-arial">36621-54481-29402-6598</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <x-btn-secondary type="button" x-on:click="step = 5">
                        Anterior</x-btn-secondary>
                    <x-btn-primary type="button" wire:click='confirmAndSave'>Finalizar</x-btn-primary>
                </div>
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                // Models
                step: 1,
                searchProfesion: '',
                // Propeties
                gender: @entangle('gender'),
                age: @entangle('age'),
                phone: @entangle('phone'),
                grade_profile_id: @entangle('grade_profile_id'),
                profesion_id: @entangle('profesion_id'),
                location_id: @entangle('location_id'),
                account_type_id: @entangle('account_type_id'),
                // Data
                user: @json($user),
                profesions: @json($profesions),
                locations: @json($locations),
                accountTypes: @json($account_types),
                // Functions
                isProAccountSelected() {
                    if (this.account_type_id == 2 || this.account_type_id == 3) {
                        this.step = 6
                        this.accountTypes.find(account => {
                            if (account.id == this.account_type_id) {
                                this.user.phone = this.phone
                                this.user.account_name = account.name
                                this.user.account_price = account.price
                                this.user.account_duration = account.duration_days
                            }
                        })
                    } else {
                        $wire.confirmAndSave()
                    }
                },
                isValidPhone() {
                    return /^[67]\d{7,}$/.test(this.phone)
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
