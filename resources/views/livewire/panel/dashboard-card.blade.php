<section x-data="subcontent" class="flex flex-col gap-4">
    <header class="flex flex-row">
        <div class="flex-1">
            <h3 class="text-lg font-medium">{{ $title }}</h3>
            <small class="text-tbn-secondary text-xs">{{ $description }}</small>
        </div>
        <!-- Filter suggests -->
        <div class="relative">
            <button x-on:click="show_dropdown = true" type="button"
                class="flex px-3 py-2 text-sm font-medium text-tbn-secondary border border-gray-300 bg-white rounded-md"
                id="suggest-menu" aria-expanded="false" data-dropdown-toggle="suggest-dropdown"
                data-dropdown-placement="bottom">
                <span x-text="filter_text"></span><i class="fa-solid fa-sort-down ml-2 text-xs"></i>
            </button>
            <div x-show="show_dropdown" x-on:click.outside="show_dropdown = false"
                class="absolute w-40 shadow-lg top-6 right-0 z-50 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg"
                id="suggest-dropdown">
                <ul x-on:click="show_dropdown = false" class="py-2" aria-labelledby="user-menu-button">
                    <li class="cursor-pointer">
                        <a x-on:click="setFilterAnnounce('all')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Todas</a>
                    </li>
                    <li class="cursor-pointer">
                        <a x-on:click="setFilterAnnounce('today')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Publicado hoy</a>
                    </li>
                    <li class="cursor-pointer">
                        <a x-on:click="setFilterAnnounce('week')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Publicado esta semana</a>
                    </li>
                    <li class="cursor-pointer">
                        <a x-on:click="setFilterAnnounce('month')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
        }" x-show="filter_option === 'all' || 
            (filter_option === 'today' && info.today) ||
            (filter_option === 'week' && info.week) ||
            (filter_option === 'month' && info.month)" x-transition:enter.duration.300ms x-transition:leave.duration.300ms
            href="{{ $this->isAnnouncePro($announce->pro) ? route('result', ['id' => $announce->id]) : route('purchase-cards') }}"
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
            <x-button-link href="{{ route('search') }}" class="bg-tbn-primary inline-block mt-5" wire:navigate>
                Buscar convocatorias</x-button-link>
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
