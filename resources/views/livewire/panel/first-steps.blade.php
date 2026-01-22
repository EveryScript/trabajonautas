<section class="flex items-start justify-center min-h-screen py-10">
    <div x-data="content" class="w-full max-w-xl md:max-w-4xl">
        <div class="p-6 mx-2 bg-white rounded-lg shadow-lg md:p-10 dark:bg-tbn-dark">
            <div class="mb-3 max-w-60">
                <x-application-logo />
            </div>
            <h3 class="mb-1 text-lg font-semibold dark:text-white md:text-xl">
                Hola {{ auth()->user()->name }}</h3>
            <p class="mb-4 text-sm text-tbn-secondary dark:text-tbn-light">Estamos listos para despegar contigo. Ingresa
                tu información para
                completar tu registro.</p>
            <!-- Step 1 : Gender, Age, Phone -->
            <div x-show="step === 1" x-transition:enter.duration.300ms>
                <h5 class="mb-2 font-bold dark:text-white text-md">¿Cuál es tu genero?</h5>
                <ul class="grid grid-cols-2 gap-1 mx-auto mb-8">
                    <li class="text-center">
                        <input type="radio" x-model='gender' value="M" id="gender-1" name="gender"
                            class="hidden peer">
                        <label for="gender-1"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <span class="text-sm md:text-md">masculino</span>
                        </label>

                    </li>
                    <li class="text-center">
                        <input type="radio" x-model='gender' value="F" id="gender-2" name="gender"
                            class="hidden peer">
                        <label for="gender-2"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <span class="text-sm md:text-md">femenino</span>
                        </label>
                    </li>
                </ul>
                <h5 class="mb-2 font-bold dark:text-white text-md">¿Cuál es tu edad?</h5>
                <ul class="grid grid-cols-2 gap-1 mx-auto mb-8 md:grid-cols-3">
                    <li class="text-center">
                        <input type="radio" x-model='age' value="1" id="age-1" name="age"
                            class="hidden peer">
                        <label for="age-1"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <span class="text-sm md:text-md">de 18 a 25 años</span>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio" x-model='age' value="2" id="age-2" name="age"
                            class="hidden peer">
                        <label for="age-2"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <span class="text-sm md:text-md">de 26-32 años</span>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio" x-model='age' value="3" id="age-3" name="age"
                            class="hidden peer">
                        <label for="age-3"
                            class="flex justify-center items-center uppercase h-[4rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <span class="text-sm md:text-md">de 33 en adelante</span>
                        </label>
                    </li>
                </ul>
                <h5 class="mb-1 font-bold dark:text-white text-md">Celular (WhatsApp)</h5>
                <div class="flex items-center gap-1 mb-1">
                    <select id="country-code" name="country-code" disabled
                        class="py-2 bg-white border border-gray-300 rounded-md ps-3 pe-8 focus:outline-none text-tbn-secondary disabled:cursor-not-allowed">
                        <option value="+591" selected>+591</option>
                    </select>
                    <x-input type="tel" x-model="phone" inputmode="numeric" class="w-full" id="phone"
                        pattern="[0-9]*" placeholder="67891011" />
                </div>
                <p x-show="!isValidPhone()" class="mb-1 text-xs text-tbn-secondary dark:text-tbn-primary">
                    <i class="fa-solid fa-circle-info"></i> Asegúrate de ingresar tu número de celular con
                    WhatsApp.
                </p>
                <p x-show="isValidPhone()" class="mb-1 text-xs text-green-500">
                    <i class="fa-solid fa-check"></i> El número de WhatsApp es válido.
                    <a :href="url_whatsapp" target="_blank" class="underline cursor-pointer">Verificar</a>
                </p>
                <div class="flex justify-between mt-4">
                    <x-secondary-button type="button" disabled>Anterior</x-secondary-button>
                    <x-button type="button" x-on:click="step = 2" x-bind:disabled="!gender || !age || !isValidPhone()">
                        Siguiente</x-button>
                </div>
            </div>
            <!-- Step 2 : Grade profile -->
            <div x-show="step === 2" x-cloak x-transition:enter.duration.300ms>
                <h5 class="mb-2 font-bold text-md dark:text-white">¿Cuál es tu grado académico?</h5>
                <ul class="grid grid-cols-1 gap-1 mx-auto mb-8 md:grid-cols-2 lg:grid-cols-3">
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="1" id="profile-1"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-1"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Estudiante</span>
                                <p class="text-xs dark:text-tbn-light">Bachiller o en instituto o universidad</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="2" id="profile-2"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-2"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Técnico Medio</span>
                                <p class="text-xs dark:text-tbn-light">Profesional titulado a nivel técnico medio</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="3" id="profile-3"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-3"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Técnico Superior</span>
                                <p class="text-xs dark:text-tbn-light">Profesional titulado a nivel técnico superior
                                </p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="4" id="profile-4"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-4"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Egresado</span>
                                <p class="m-0 text-xs dark:text-tbn-light">Aprobó todas las materias y solamente le
                                    falta la tesis.</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="5" id="profile-5"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-5"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Titulado</span>
                                <p class="text-xs dark:text-tbn-light">Actualmente con titulo en provisión nacional.
                                </p>
                            </div>
                        </label>
                    </li>
                </ul>
                <div class="flex justify-between mt-4">
                    <x-secondary-button type="button" x-on:click="step = 1"> Anterior</x-secondary-button>
                    <x-button type="button" x-on:click="step = 3" x-bind:disabled="!grade_profile_id">
                        Siguiente</x-button>
                </div>
            </div>
            <!-- Step 3 : Profesion -->
            <div x-show="step === 3" x-cloak x-transition:enter.duration.300ms>
                <h5 class="mb-2 font-bold text-md dark:text-white">¿Cuál es tu profesión?</h5>
                <x-input type="search" x-model="searchProfesion" class="mb-2" id="searchProfesion"
                    class="w-full" placeholder="Busca una profesión" />
                <div
                    class="h-[18rem] overflow-y-auto bg-white dark:bg-neutral-900 border border-gray-200 dark:border-tbn-secondary p-2 rounded-lg scrollbar-none">
                    <ul class="grid grid-cols-1 gap-1 mx-auto mb-8 md:grid-cols-2">
                        <template x-for="profesion in filteredProfesions">
                            <li class="text-center" :key="'profesion' + profesion.id">
                                <input type="radio" x-on:click="profesion_id = profesion.id" :value="profesion.id"
                                    :id="'profesion-' + profesion.id" name="profesion" class="hidden peer">
                                <label :for="'profesion-' + profesion.id"
                                    class="flex justify-center items-center h-14 sm:h-[5rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
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
                <div class="flex justify-between mt-4">
                    <x-secondary-button type="button" x-on:click="step = 2">
                        Anterior</x-secondary-button>
                    <x-button type="button" x-on:click="step = 4" x-bind:disabled="!profesion_id">
                        Siguiente</x-button>
                </div>
            </div>
            <!-- Step 4 : Locations -->
            <div x-show="step === 4" x-cloak x-transition:enter.duration.300ms>
                <h5 class="mb-2 font-bold text-md dark:text-white">¿Cuál es tu ubicación actual?</h5>
                <ul class="grid grid-cols-2 gap-1 mx-auto mb-8 md:grid-cols-3">
                    <template x-for="location in locations">
                        <li class="text-center" :key="'location-' + location.id">
                            <input type="radio" x-on:click="setLocation(location.id)" :value="location.id"
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
                <div class="flex justify-between mt-4">
                    <x-secondary-button type="button" x-on:click="step = 3">
                        Anterior</x-secondary-button>
                    <x-button type="button" x-on:click="step = 5" x-bind:disabled="!location_id">
                        Siguiente</x-button>
                </div>
            </div>
            <!-- Step 5 : Select your account -->
            <div x-show="step === 5" x-cloak x-transition:enter.duration.300ms>
                <h5 class="mb-2 font-bold text-md dark:text-white">Elige una cuenta</h5>
                <ul class="grid grid-cols-1 gap-1 md:grid-cols-3">
                    <template x-for="accountType in accountTypes">
                        <li :key="'account-' + accountType.id">
                            <input type="radio" x-on:click="account_type_id = accountType.id"
                                :id="'account-' + accountType.id" :value="accountType.id" class="hidden peer"
                                name="account_type">
                            <label :for="'account-' + accountType.id"
                                class="block p-6 bg-white border-2 border-gray-200 rounded-lg shadow-lg cursor-pointer dark:bg-tbn-dark dark:text-white dark:border-tbn-secondary dark:hover:bg-neutral-900 peer-checked:border-tbn-primary">
                                <h2 x-text="accountType.name"
                                    class="text-xl font-semibold capitalize text-tbn-secondary hover:bg-tbn-dark dark:text-tbn-primary">
                                </h2>
                                <div class="mt-2 md:mt-2">
                                    <span x-text="accountType.price+' Bs.'"
                                        class="text-3xl font-bold md:text-4xl text-tbn-secondary dark:text-tbn-light"></span>
                                    <span
                                        x-text="accountType.duration_days == 0 ? '/ Siempre' : '/ '+accountType.duration_days+' dias'"
                                        class="font-medium text-tbn-dark dark:text-tbn-light"></span>
                                </div>
                                <ul class="mt-3 space-y-2 text-xs md:mt-6 md:text-sm">
                                    <li class="flex items-center">
                                        <i class="mr-2 text-green-500 fas fa-check"></i>
                                        Convocatorias estándar
                                    </li>
                                    <li class="flex items-center">
                                        <i x-show="accountType.id == 1" class="mr-2 text-red-500 fas fa-times"></i>
                                        <i x-show="accountType.id == 2 || accountType.id == 3"
                                            class="mr-2 text-green-500 fas fa-check"></i>
                                        Convocatorias PRO
                                    </li>
                                    <li class="flex items-center">
                                        <i x-show="accountType.id == 1 || accountType.id == 2"
                                            class="mr-2 text-red-500 fas fa-times"></i>
                                        <i x-show="accountType.id == 3" class="mr-2 text-green-500 fas fa-check"></i>
                                        Notificaciones en tiempo real
                                    </li>
                                </ul>
                            </label>
                        </li>
                    </template>
                </ul>
                <div class="flex justify-between mt-4">
                    <x-secondary-button type="button" x-on:click="step = 4">
                        Anterior</x-secondary-button>
                    <x-button type="submit" x-on:click="isProAccountSelected" x-bind:disabled="!account_type_id">
                        <span wire:loading.remove>Siguiente</span>
                        <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
                    </x-button>
                </div>
            </div>
            <!-- Step 6 : Purchase review -->
            <div x-show="step === 6" x-cloak x-transition:enter.duration.300ms>
                <h5 class="mb-1 font-bold text-md dark:text-white">Resumen de la compra</h5>
                <span class="block mb-2 text-xs text-tbn-dark dark:text-tbn-light">
                    Revisa tus datos y escanea el código QR para realizar tu depósito.</span>
                <div class="flex flex-col w-full gap-6 md:flex-row">
                    <div class="w-full text-sm md:w-3/5">
                        <div
                            class="p-2 mb-4 border rounded-md bg-gray-50 dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary dark:text-tbn-light">
                            <table class="divide-y divide-gray-200 dark:divide-tbn-secondary">
                                <tbody class="divide-y divide-gray-200 dark:divide-tbn-secondary">
                                    <tr>
                                        <td class="p-2 font-medium whitespace-nowrap">Nombre</td>
                                        <td x-text="user.name" class="p-2 whitespace-wrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-medium whitespace-nowrap">Celular</td>
                                        <td x-text="user.phone" class="p-2 whitespace-nowrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-medium whitespace-nowrap">Ubicación</td>
                                        <td x-text="location_name" class="p-2 whitespace-nowrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-medium whitespace-nowrap">Tipo de cuenta</td>
                                        <td x-text="user.account_name" class="p-2 uppercase whitespace-nowrap">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-medium whitespace-nowrap">Costo</td>
                                        <td x-text="user.account_price +' Bs.'" class="p-2 whitespace-nowrap"></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-medium whitespace-nowrap">Duración</td>
                                        <td x-text="user.account_duration +' días'" class="p-2 whitespace-nowrap">
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
                        <div class="mb-8 text-center">
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
                <div class="flex justify-between mt-4">
                    <x-secondary-button type="button" x-on:click="step = 5">
                        Anterior</x-secondary-button>
                    <x-button type="button" wire:click='confirmAndSave'>
                        <span wire:loading.remove>Finalizar</span>
                        <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
                    </x-button>
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
                location_name: '',
                url_whatsapp: '',
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
                // Bank Account
                bankAccount: '4077070681',
                copied: false,
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
                    this.url_whatsapp = 'https://wa.me/591' + this.phone
                    return /^[67]\d{7}$/.test(this.phone)
                },
                filteredProfesions() {
                    return this.profesions.filter(
                        profesion => profesion.profesion_name.toLowerCase().includes(this.searchProfesion
                            .toLowerCase())
                    )
                },
                setLocation(id) {
                    this.location_id = id
                    this.location_name = this.locations.find(location => location.id == id).location_name
                },
                verifyWhatsappNumber() {
                    window.open(this.url_whatsapp)
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
