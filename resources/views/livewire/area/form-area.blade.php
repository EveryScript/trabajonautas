<section>
    <x-title-app>
        <x-slot name="title_page">{{ $id ? 'Editar area' : 'Nueva area' }}</x-slot>
        <x-slot name="description_page">
            {{ $id
                ? 'Actualiza la información de un area registrada'
                : 'Crea un area profesional para clasificar a los profesionales de Trabajonautas' }}
        </x-slot>
    </x-title-app>
    <div class="grid grid-cols-2 gap-4" x-data="content">
        <form class="tbn-form" wire:submit="{{ $id ? 'update(area_profesions)' : 'save(area_profesions)' }}">
            <div class="mb-4">
                <x-label for="area_name" value="{{ __('Nombre del area') }}" />
                <x-input wire:model="area.area_name" x-model="area_name" id="area_name" type="text"
                    class="mt-1 block w-full" placeholder="Ingeniería, Leyes, Arquitectura" />
                <x-input-error for="area.area_name" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="description" value="{{ __('Descripción (corta)') }}" />
                <x-input wire:model="area.description" x-model="area_description" id="area_name" type="text"
                    class="mt-1 block w-full" placeholder="Descripción corta" />
                <x-input-error for="area.description" class="mt-2" />
            </div>
            <!-- Search profesion -->
            <div class="mb-4">
                <div class="mb-3">
                    <x-label class="mb-2" for="profesions" value="{{ __('Profesiones relacionadas') }}" />
                    <x-input x-model='search' type="search" class="flex-grow mb-1"
                        placeholder="Buscar una profesion" />
                    <ul class="max-h-[12rem] overflow-y-scroll">
                        <template x-for="profesion in filteredProfesions">
                            <li @click="addProfesion(profesion)" x-text="profesion.profesion_name"
                                class="w-full px-5 py-3 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:text-gray-600 hover:bg-gray-100">
                            </li>
                        </template>
                    </ul>
                    <a href="{{ route('profesions') }}" wire:navigate class="text-sm text-tbn-primary underline">Crear
                        nueva profesion</a>
                </div>
            </div>
            <div class="mb-4">
                <x-button>{{ $id ? 'Acualizar area' : 'Crear area' }}</x-button>
            </div>
        </form>
        <div>
            <!-- Area view -->
            <div class="bg-white shadow-lg px-6 py-5 rounded-md">
                <span class="text-xs text-tbn-primary">Area profesional</span>
                <h5 class="text-lg font-bold" x-text="area_name">Nombre del area</h5>
                <p class="text-sm text-tbn-dark mb-3" x-text="area_description">Descripción corta del area</p>
                <span class="block mb-2 text-xs text-tbn-primary">Profesiones</span>
                <ul class="max-h-[20rem] overflow-y-scroll">
                    <template x-for="profesion in selected_profesions">
                        <li
                            class="w-full flex flex-row justify-between px-5 py-3 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:text-gray-600 hover:bg-gray-100">
                            <p x-text="profesion.profesion_name"></p>
                            <button @click="removeProfesion(profesion)" class="text-lg text-red-500">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                area_id: @json($id),
                search: '',
                profesions: {!! $profesions !!},
                selected_profesions: [],
                // Models
                area_name: '',
                area_description: '',
                area_profesions: [],
                // Functions
                init() {
                    console.log(this.area_id)
                    if (this.area_id) {
                        this.area_name = $wire.area.area_name.toString()
                        this.area_description = $wire.area.description.toString()
                        $wire.area.area_profesions.forEach(element => {
                            this.addProfesion(this.profesions.find(profesion => profesion.id == element))
                        })
                    }
                },
                filteredProfesions() {
                    return this.profesions.filter(
                        profesion => profesion.profesion_name.toLowerCase().includes(this.search.toLowerCase())
                    )
                },
                addProfesion(element) {
                    this.search = ''
                    this.selected_profesions.push(element)
                    this.area_profesions.push(element.id)
                    this.profesions = this.profesions.filter(
                        profesion => profesion.id != element.id
                    )
                },
                removeProfesion(element) {
                    this.selected_profesions = this.selected_profesions.filter(profesion => profesion.id != element
                        .id)
                    this.area_profesions = this.area_profesions.filter(id => id != element.id)
                    this.profesions.unshift(element)
                }
            }))
        </script>
    @endscript
</section>
