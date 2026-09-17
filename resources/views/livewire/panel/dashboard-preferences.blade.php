<section x-data="subcontent" class="flex flex-col gap-2">
    <header class="flex flex-col justify-between gap-4 lg:flex-row">
        <div class="flex-1">
            <h3 class="text-lg font-medium text-tbn-dark dark:text-white">{{ $title }}</h3>
            <p class="text-xs text-tbn-secondary dark:text-tbn-light">{{ $description }}</p>
        </div>
    </header>
    <main>
        <div class="space-y-4">
            @foreach ($types as $type)
                <x-input-checkbox-block wire:model="excluded" value="{{ $type->id }}" name="excluded[]"
                    id="excluded-type-{{ $type->id }}">
                    <div class="divide-y divide-tbn-secondary">
                        <div class="w-full mb-2">
                            <p class="font-medium text-black text-md dark:text-tbn-primary">Ocultar convocatorias de
                                tipo "{{ $type->name }}"</p>
                            <p class="text-xs text-tbn-dark dark:text-white">
                                Dejar de ver y recibirás notificaciones de convocatorias de tipo "{{ $type->name }}"
                            </p>
                        </div>
                    </div>
                </x-input-checkbox-block>
            @endforeach
            <x-button type="button" wire:click="save" wire:loading.attr="disabled" class="w-full lg:w-auto">
                <span wire:loading.remove wire:target='save'>Guardar cambios</span>
                <span wire:loading wire:target='save'><i class="mr-2 fa-solid fa-spinner animate-spin"></i>
                    Guardando...
                </span>
            </x-button>
        </div>

        <div class="mt-2 text-sm text-green-600" x-data="{ show: false }"
            x-on:preferences-updated.window="show = true; setTimeout(() => show = false, 2000)" x-show="show" x-cloak>
            Preferencias guardadas
        </div>
    </main>
</section>
@script
    <script>
        Alpine.data('subcontent', () => ({
            filter_text: 'Filtrar',
            // Functions
            setFilterAnnounce(option) {
                this.filter_option = option
                switch (option) {
                    case 'all':
                        this.filter_text = 'Todas'
                        break;
                    case 'today':
                        this.filter_text = 'Hoy'
                        break;
                    case 'week':
                        this.filter_text = 'Esta semana'
                        break;
                    case 'month':
                        this.filter_text = 'Este mes'
                        break;
                    default:
                        this.filter_text = 'all'
                        break;
                }
            }
        }))
    </script>
@endscript
