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
