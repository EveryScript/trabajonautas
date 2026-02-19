<section x-data="subcontent" class="flex flex-col gap-2">
    <header class="flex flex-col justify-between gap-4 lg:flex-row">
        <div class="flex-1">
            <h3 class="text-lg font-medium text-tbn-dark dark:text-white">{{ $title }}</h3>
            <small class="text-xs text-tbn-secondary dark:text-tbn-light">{{ $description }}</small>
        </div>
        <!-- Filter suggests -->
        <div class="relative">
            <div class="flex flex-row justify-end gap-1">
                <x-secondary-button type="button" id="search" href="{{ route('search') }}" wire:navigate>
                    <i class="mr-2 text-xs fa-solid fa-search"></i> Búsqueda avanzada
                </x-secondary-button>
                <x-secondary-button x-on:click="show_dropdown = true" type="button" id="suggest-menu"
                    aria-expanded="false" data-dropdown-toggle="suggest-dropdown" data-dropdown-placement="bottom">
                    <span x-text="filter_text"></span><i
                        class="ml-2 text-xs fa-solid fa-sort-down translate-y-[-2px]"></i>
                </x-secondary-button>
            </div>
            <div x-show="show_dropdown" x-on:click.outside="show_dropdown = false"
                class="absolute right-0 z-50 w-40 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg top-8 dark:text-tbn-dark dark:divide-tbn-secondary"
                id="suggest-dropdown">
                <ul class="bg-white dark:bg-tbn-dark" x-on:click="show_dropdown = false" class="py-2"
                    aria-labelledby="user-menu-button">
                    <li class="cursor-pointer ">
                        <a x-on:click="setFilterAnnounce('all')"
                            class="block px-4 py-2 text-sm text-gray-700 rounded-t-lg hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                            Todas</a>
                    </li>
                    <li class="cursor-pointer">
                        <a x-on:click="setFilterAnnounce('today')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                            Publicado hoy</a>
                    </li>
                    <li class="cursor-pointer">
                        <a x-on:click="setFilterAnnounce('week')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                            Publicado esta semana</a>
                    </li>
                    <li class="cursor-pointer">
                        <a x-on:click="setFilterAnnounce('month')"
                            class="block px-4 py-2 text-sm text-gray-700 rounded-t-lg hover:bg-gray-100 dark:bg-tbn-dark dark:text-tbn-light dark:hover:bg-neutral-900">
                            Publicado este mes</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    @forelse ($announces as $priority => $group)
        <div>
            <h4 class="mb-3 text-sm font-bold tracking-wider uppercase text-tbn-primary dark:text-tbn-light">
                @if ($hasRecommends)
                    <h4 class="mb-4 text-sm font-bold tracking-wider uppercase text-tbn-primary dark:text-tbn-light">
                        @if ($priority == 1)
                            Ideal para ti
                        @else
                            Otras convocatorias recientes
                        @endif
                    </h4>
                @endif
            </h4>
            <div class="flex flex-col gap-4 mb-4">
                @foreach ($group as $announce)
                    <div wire:key='announce-{{ $announce->id }}'
                        x-show="filter_option === 'all' || (filter_option === 'today' && {{ $announce->is_today ? 'true' : 'false' }}) || (filter_option === 'week' && {{ $announce->is_week ? 'true' : 'false' }}) || (filter_option === 'month' && {{ $announce->is_month ? 'true' : 'false' }})"
                        x-transition:enter.duration.300ms x-transition:leave.duration.300ms>
                        <x-card-announce :announce="$announce" :client="$client_pro_authorized" />
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <x-section-empty class="col-span-2"
            title="{{ $my_announces_mode ? 'Aún no has guardado convocatorias' : 'No hay sugerencias disponibles' }}"
            description="{{ $my_announces_mode ? 'Tus convocatorias favoritas estarán aqui. Inicia la búsqueda ahora.' : 'Las sugerencias de convocatorias de trabajo estarán visibles en esta sección.' }}">
            <x-button type="button" href="{{ route('search') }}" class="inline-block mt-5 bg-tbn-primary"
                wire:navigate>
                Buscar convocatorias</x-button>
        </x-section-empty>
    @endforelse
    @if ($announces->flatten()->count() >= $per_page)
        <div class="flex justify-center">
            <x-secondary-button wire:click="loadMore" wire:loading.attr="disabled" class="w-full lg:w-auto">
                <span wire:loading.remove wire:target='loadMore'>Ver más</span>
                <span wire:loading wire:target='loadMore'><i class="mr-2 fa-solid fa-spinner animate-spin"></i>
                    Cargando...
                </span>
            </x-secondary-button>
        </div>
    @endif
</section>
@script
    <script>
        Alpine.data('subcontent', () => ({
            show_dropdown: false,
            filter_option: 'all',
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
