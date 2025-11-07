<section class="max-w-6xl mt-5 mb-10 px-5 mx-auto">
    <div x-data="content">
        <!-- Search fields -->
        <div class="w-full flex flex-col md:flex-row gap-2 mb-4 tbn-form">
            <div class="w-full md:w-1/2">
                <x-label for="search_title" value="{{ __('¿Cuál es tu profesión?') }}" />
                <x-input class="px-[12px] py-[6px]" type="search" wire:model='search_title' x-model="search_title"
                    @keyup.enter="searchAnnouncements" placeholder='Arquitecto, ingeniero ...' />
            </div>
            <div class="w-full md:w-1/2" wire:ignore>
                <x-label for="search_location" value="{{ __('Departamento o región') }}" />
                <x-select @change="setLocationName($event.target.value)" id="locations" wire:model="search_location">
                    <option value="0" selected>Cualquier lugar</option>
                    @forelse ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                    @empty
                        <option>Hay opciones disponibles</option>
                    @endforelse
                </x-select>
            </div>
            <div class="pt-1 md:pt-5">
                <x-button @click="searchAnnouncements" class="md:w-auto w-full mt-1"
                    x-bind:disabled="!search_title && !search_location">Buscar</x-button>
            </div>
        </div>
        <!-- Results numbers -->
        @if ($search_title || $search_location)
            <div class="p-5 rounded-md bg-gray-100 mb-4" wire:loading.remove>
                <div class="flex flex-row align-center">
                    <div class="flex-1">
                        <h5 class="text-tbn-primary text-sm font-semibold mb-1">
                            Resultados encontrados: {{ $count_results }}</h5>
                        @if ($search_title)
                            <p class="px-2 py-1 text-sm rounded-md bg-gray-200 inline-block text-gray-900">
                                {{ $search_title }}</p>
                        @endif
                        <p x-text="search_location" x-show="search_location"
                            class="px-2 py-1 text-sm rounded-md bg-gray-200 inline-block text-gray-900"></p>
                    </div>
                    <div class="flex flex-col justify-center">
                        <button class="inline-block w-10 h-10 rounded-full bg-tbn-primary" type="button"
                            wire:click='clearSearch' @click="clearData">
                            <i class="fas fa-trash text-sm text-white"></i></button>
                    </div>
                </div>
            </div>
        @endif
        <!-- Tags and random profesions -->
        <div class="mb-5" wire:loading.remove>
            <div class="flex flex-row flex-wrap gap-2">
                @foreach ($random_profesions as $profesion)
                    <a href="{{ route('search', ['title' => $profesion->profesion_name]) }}" wire:navigate
                        class="px-4 py-2 text-tbn-dark text-xs bg-white shadow-md rounded-full hover:bg-tbn-primary hover:text-white transition-all duration-200">
                        {{ $profesion->profesion_name }}</a>
                @endforeach
            </div>
        </div>
        <!-- Announcements -->
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
            @forelse ($announcements as $announce)
                <div wire:loading.remove>
                    <a href="{{ $this->isAnnouncePro($announce->pro) ? route('result', ['id' => $announce->id]) : route('purchase-cards') }}"
                        wire:navigate wire:key='announce-{{ $announce->id }}'>
                        <x-card-announce logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                            area="{{ $announce->area ? $announce->area->area_name : '(sin area)' }}"
                            title="{{ $announce->announce_title }}" pro="{{ $announce->pro }}">
                            @if ($announce->company)
                                <x-slot name="company">{{ $announce->company->company_name }}</x-slot>
                            @endif
                            <x-slot name="locations">
                                {{ $announce->locations[0]->location_name }}
                                @if ($announce->locations->count() > 1)
                                    <span class="text-xs text-gray-400">({{ $announce->locations->count() - 1 }}
                                        más)</span>
                                @endif
                            </x-slot>
                        </x-card-announce>
                    </a>
                </div>
            @empty
                <x-section-empty class="col-span-2" title="No hay resultados"
                    description="No hemos encontrado coincidencias para tu busqueda" wire:loading.remove>
                    <x-button type="button" wire:click='clearSearch' class="mt-4">Aceptar</x-button>
                </x-section-empty>
            @endforelse
        </div>
    </div>
    <div wire:loading.remove> {{ $announcements->links() }} </div>
    <div class="w-full" wire:loading><x-cards-loading /></div>

    @script
        <script>
            Alpine.data('content', () => ({
                search_title: '',
                search_location: '',
                search_location_id: '',
                locations: {!! $locations !!},
                init() {
                    this.search_title = $wire.search_title
                },
                setLocationName(id) {
                    if (id == 0) {
                        this.search_location = null
                        this.search_location_id = null
                    } else {
                        this.search_location_id = id
                        this.search_location = this.locations.find(item => item.id == id).location_name
                    }
                },
                searchAnnouncements() {
                    $wire.searchAnnounces(this.search_title, this.search_location_id)
                },
                clearData() {
                    this.search_title = ''
                    this.search_location = ''
                    this.search_location_id = ''
                }
            }));
        </script>
    @endscript
    @script
        <script>
            new TomSelect('#locations', {
                controlInput: null
            });
        </script>
    @endscript
</section>
