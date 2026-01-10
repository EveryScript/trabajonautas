<section x-data="subcontent" class="flex flex-col gap-4">
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
    @forelse ($announces as $announce)
        <a x-data="{
            info: {
                today: {{ $announce->created_at->isToday() ? 'true' : 'false' }},
                week: {{ $announce->created_at->isCurrentWeek() ? 'true' : 'false' }},
                month: {{ $announce->created_at->isCurrentMonth() ? 'true' : 'false' }}
            }
        }"
            x-show="filter_option === 'all' || 
            (filter_option === 'today' && info.today)
||
            (filter_option === 'week' && info.week) ||
            (filter_option === 'month' && info.month)"
            x-transition:enter.duration.300ms x-transition:leave.duration.300ms
            href="{{ $announce->pro && !$client_pro_authorized ? route('purchase-cards') : route('result', ['id' => $announce->id]) }}"
            wire:navigate wire:key='announce-{{ $announce->id }}'>
            <x-card-announce logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                created_at="{{ $announce->created_at }}" title="{{ $announce->announce_title }}"
                pro="{{ $announce->pro }}">
                @if ($announce->company)
                    <x-slot name="company">{{ $announce->company->company_name }}</x-slot>
                @endif
                <x-slot name="locations">
                    {{ $announce->locations[0]->location_name }}
                    @if ($announce->locations->count() > 1)
                        <span class="text-xs text-gray-400">
                            ({{ $announce->locations->count() - 1 }} más)
                        </span>
                    @endif
                </x-slot>
            </x-card-announce>
        </a>
    @empty
        <x-section-empty class="col-span-2" title="No hay sugerencias disponibles"
            description="Las sugerencias de convocatorias de trabajo estarán visibles en esta sección.">
            <x-button type="button" href="{{ route('search') }}" class="inline-block mt-5 bg-tbn-primary"
                wire:navigate>
                Buscar convocatorias</x-button>
        </x-section-empty>
    @endforelse
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
