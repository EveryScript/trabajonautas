<div x-show="step === 3" x-cloak x-transition:enter.duration.300ms>
    <h5 class="mb-2 font-bold text-md dark:text-white">¿Cuál es tu profesión?</h5>
    <x-input type="search" x-model="searchProfesion" class="mb-2" id="searchProfesion" class="w-full"
        placeholder="Busca una profesión" />
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
                            <span x-text="profesion.profesion_name"class="block text-sm font-medium"></span>
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
