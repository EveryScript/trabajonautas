<section>
    <x-title-app>
        <x-slot name="title_page">{{ $id ? 'Editar area' : 'Nueva area' }}</x-slot>
        <x-slot name="description_page">
            {{ $id
                ? 'Actualiza la información de un area registrada'
                : 'Crea un area profesional para presentar determinadas convocatorias a los usuarios de Trabajonautas' }}
        </x-slot>
    </x-title-app>
    <div class="grid grid-cols-2 gap-4" x-data="content">
        <form class="tbn-form" wire:submit="{{ $id ? 'update' : 'save' }}">
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
            <div class="mb-4">
                <x-button type="submit">{{ $id ? 'Acualizar area' : 'Crear area' }}</x-button>
                <x-secondary-button type="button" href="{{ route('area') }}"
                    wire:navigate>Cancelar</x-secondary-button>
            </div>
        </form>
        <div>
            <!-- Area view -->
            <div class="bg-white shadow-lg px-6 py-5 rounded-md" x-show="area_name">
                <span class="text-xs text-tbn-primary">Area profesional</span>
                <h5 class="text-lg font-bold" x-text="area_name">Nombre del area</h5>
                <p class="text-sm text-tbn-dark mb-3" x-text="area_description">Descripción corta del area</p>
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                area_id: @json($id),
                // Models
                area_name: '',
                area_description: '',
                // Functions
                init() {
                    if (this.area_id) {
                        this.area_name = $wire.area.area_name.toString()
                        this.area_description = $wire.area.description.toString()
                    }
                }
            }))
        </script>
    @endscript
</section>
