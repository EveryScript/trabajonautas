<section>
    <x-title-app>
        <x-slot name="title_page">{{ $id ? 'Editar area' : 'Nueva area' }}</x-slot>
        <x-slot name="description_page">
            {{ $id
                ? 'Actualiza la información de un area registrada'
                : 'Crea un area profesional para presentar determinadas convocatorias a los usuarios de Trabajonautas' }}
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <form class="w-full md:w-1/2 gap-4" wire:submit="{{ $id ? 'update' : 'save' }}">
            <div>
                <div class="mb-4">
                    <x-label for="area_name" value="{{ __('Nombre del area') }}" />
                    <x-input wire:model="area.area_name" x-model="area_name" id="area_name" type="text"
                        class="mt-1 block w-full" placeholder="Ingeniería, Leyes, Arquitectura" />
                    <x-input-error for="area.area_name" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-label for="description" value="{{ __('Descripción (corta)') }}" />
                    <x-textarea x-model="area_description" wire:model="area.description" rows="4"
                        class="w-full resize-none" name="description" placeholder="Descripción corta" />
                    <x-input-error for="area.description" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-button type="submit">{{ $id ? 'Acualizar area' : 'Crear area' }}</x-button>
                    <x-secondary-button type="button" href="{{ route('area') }}"
                        wire:navigate>Cancelar</x-secondary-button>
                </div>
            </div>
        </form>
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
