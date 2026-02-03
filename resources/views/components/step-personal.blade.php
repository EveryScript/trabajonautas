<div x-show="step === 1" x-transition:enter.duration.300ms>
    <h5 class="mb-2 font-bold dark:text-white text-md">¿Cuál es tu genero?</h5>
    <ul class="grid grid-cols-2 gap-1 mx-auto mb-8">
        <li class="text-center">
            <input type="radio" x-model='gender' value="M" id="gender-1" name="gender" class="hidden peer">
            <label for="gender-1"
                class="flex justify-center items-center uppercase h-[5rem] px-5 py-4 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-gray-50 dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                <span class="text-sm md:text-md">masculino</span>
            </label>

        </li>
        <li class="text-center">
            <input type="radio" x-model='gender' value="F" id="gender-2" name="gender" class="hidden peer">
            <label for="gender-2"
                class="flex justify-center items-center uppercase h-[5rem] px-5 py-4 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-gray-50 dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                <span class="text-sm md:text-md">femenino</span>
            </label>
        </li>
    </ul>
    <h5 class="mb-2 font-bold dark:text-white text-md">¿Cuál es tu edad?</h5>
    <ul class="grid grid-cols-2 gap-1 mx-auto mb-8 md:grid-cols-3">
        <li class="text-center">
            <input type="radio" x-model='age' value="1" id="age-1" name="age" class="hidden peer">
            <label for="age-1"
                class="flex justify-center items-center uppercase h-[5rem] px-5 py-4 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-gray-50 dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                <span class="text-sm md:text-md">de 18 a 25 años</span>
            </label>
        </li>
        <li class="text-center">
            <input type="radio" x-model='age' value="2" id="age-2" name="age" class="hidden peer">
            <label for="age-2"
                class="flex justify-center items-center uppercase h-[5rem] px-5 py-4 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-gray-50 dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                <span class="text-sm md:text-md">de 26 a 32 años</span>
            </label>
        </li>
        <li class="text-center">
            <input type="radio" x-model='age' value="3" id="age-3" name="age" class="hidden peer">
            <label for="age-3"
                class="flex justify-center items-center uppercase h-[5rem] px-5 py-4 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-gray-50 dark:hover:text-tbn-light dark:hover:bg-neutral-900">
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
        <x-input type="tel" x-model="phone" inputmode="numeric" class="w-full" id="phone" pattern="[0-9]*"
            placeholder="67891011" />
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
