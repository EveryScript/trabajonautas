<section>
    <div class="text-center">
        <h3 class="mt-5 title-font font-medium text-lg text-tbn-primary">
            Bienvenido a Trabajonautas
        </h3>
        <p class="leading-relaxed mt-2 text-sm text-gray-500">Vamos a completar tu información para brindarte las mejores
            convocatorias de
            Bolivia.</p>
    </div>
    <div x-data="data">
        <!-- Set profesions -->
        @if ($step == 1)
            <h4 class="mt-5 title-font font-bold text-2xl text-gray-700 text-center">
                ¿Cual es tu profesión(es)?
            </h4>
            <!-- Selected profesions -->
            <ul class="mx-auto max-w-2xl flex flex-row flex-wrap justify-center gap-1 mt-3">
                <template x-for="profesion in selected_profesions" class="h-full">
                    <li class="flex flex-row items-center px-3 py-2 rounded-full text-white text-sm bg-tbn-dark">
                        <span x-text="profesion.profesion_name"></span>
                        <button class="ml-2" @click="removeProfesion(profesion)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6">
                                <path fill-rule="evenodd"
                                    d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </li>
                </template>
            </ul>
            <!-- Form profesions -->
            <form wire:submit="saveProfesions(user_profesions)" class="max-w-[35rem] mx-auto text-center mt-4">
                <x-input type="search" id="search-profesion" x-model="search_profesion"
                    placeholder="Busca una profesion" />
                <x-input-error for="user_profesions" class="mt-2" />
                <!-- Profesions list (Alpine) -->
                <ul class="max-w-[35rem] mx-auto mt-2" x-show="search_profesion.length > 0">
                    <template x-for="profesion in filteredProfesions">
                        <li class="w-full px-5 py-3 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:text-gray-600 hover:bg-gray-100"
                            x-text="profesion.profesion_name" @click="addProfesion(profesion)"></li>
                    </template>
                </ul>
                <x-button class="inline-block text-xl mt-4"
                    x-bind:disabled="user_profesions.length == 0">Guardar</x-button>
            </form>
        @endif
        <!-- Set areas -->
        @if ($step == 2)
            <h4 class="mt-5 title-font font-bold text-2xl text-gray-700 text-center">
                ¿Cuales son tus areas de interés?
            </h4>
            <form wire:submit="saveAreas(user_areas)" class="max-w-4xl mx-auto text-center mt-4">
                <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto">
                    <template x-for="area in areas">
                        <li class="text-center">
                            <input type="checkbox" :id="'area-' + area.id" :value="area.id" x-model="user_areas"
                                x-bind:name="'area-' + area.id" class="hidden peer">
                            <label :for="'area-' + area.id"
                                class="flex justify-center items-center h-[6rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                <span x-text="area.area_name"></span>
                            </label>
                        </li>
                    </template>
                </ul>
                <x-button class="inline-block text-xl mt-4" x-bind:disabled="user_areas.length == 0">Guardar</x-button>
            </form>
        @endif
        <!-- Set location -->
        @if ($step == 3)
            <h4 class="mt-5 title-font font-bold text-2xl text-gray-700 text-center">
                ¿En qué departamento quieres encontrar trabajo?
            </h4>
            <form wire:submit="saveLocation(user_location)" class="max-w-4xl mx-auto text-center mt-4">
                <ul class="grid grid-cols-2 md:grid-cols-3 gap-1 mx-auto">
                    <template x-for="location in locations">
                        <li class="text-center">
                            <input type="radio" :id="'location-' + location.id" :value="location.id"
                                x-model="user_location" x-bind:name="'location-' + location.id" class="hidden peer">
                            <label :for="'location-' + location.id"
                                class="flex justify-center items-center h-[6rem] px-5 py-3 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                                <span x-text="location.location_name"></span>
                            </label>
                        </li>
                    </template>
                </ul>
                <x-button class="inline-block text-xl mt-4" x-bind:disabled="!user_location">Finalizar</x-button>
            </form>
        @endif
    </div>
    <script>
        function data() {
            const urlParams = new URLSearchParams(window.location.search)
            return {
                search_profesion: '',
                profesions: {!! $profesions !!},
                areas: {!! $areas !!},
                locations: {!! $locations !!},
                selected_profesions: [],
                user_profesions: [],
                user_areas: [],
                user_location: '',
                // Search profesions
                filteredProfesions() {
                    return this.profesions.filter(
                        profesion => profesion.profesion_name.toLowerCase().includes(this.search_profesion
                            .toLowerCase())
                    )
                },
                // Add profesion
                addProfesion(element) {
                    this.search_profesion = ''
                    this.selected_profesions.push(element)
                    this.user_profesions.push(element.id)
                    this.profesions = this.profesions.filter(
                        profesion => profesion.id != element.id
                    )
                },
                // Remove profesion
                removeProfesion(element) {
                    this.selected_profesions = this.selected_profesions.filter(profesion => profesion.id != element.id)
                    this.user_profesions = this.user_profesions.filter(id => id != element.id)
                    this.profesions.unshift(element)
                }
            }
        }
    </script>
</section>
