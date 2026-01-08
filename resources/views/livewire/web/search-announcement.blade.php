<section class="max-w-6xl p-10 mx-auto">
    <div x-data="content">
        <!-- Search fields -->
        <div class="flex flex-col w-full gap-2 mb-4 md:flex-row tbn-form">
            <div class="w-full md:w-1/2">
                <x-label class="mb-1" for="search_title" value="{{ __('¿Cuál es tu profesión?') }}" />
                <x-input class="w-full" type="search" x-model="search_title" placeholder='Arquitecto, ingeniero ...' />
            </div>
            <div class="w-full md:w-1/2" wire:ignore>
                <x-label class="mb-1" for="search_location" value="{{ __('Departamento o región') }}" />
                <x-select class="w-full" id="locations" x-on:change="setLocation($event.target.value)">
                    <option value="0" selected>Cualquier lugar</option>
                    @forelse ($locations as $location)
                        <option wire:key='{{ $location->id }}' value="{{ $location->id }}">
                            {{ $location->location_name }}</option>
                    @empty
                        <option>Hay opciones disponibles</option>
                    @endforelse
                </x-select>
            </div>
            <div class="pt-1 md:pt-5">
                <x-button x-on:click="searchAnnouncements" class="w-full mt-1 md:w-auto"
                    x-bind:disabled="!search_title && !search_location_id">Buscar</x-button>
            </div>
        </div>
        <!-- Results numbers -->
        @if ($search_title || $search_location_id)
            <div class="p-5 mb-4 border rounded-md shadow-sm bg-tbn-light dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary"
                wire:loading.remove>
                <div class="flex flex-row align-center">
                    <div class="flex-1">
                        <h5 class="mb-1 text-sm font-semibold text-tbn-primary">
                            Resultados encontrados: {{ $count_results }}</h5>
                        @if ($search_title)
                            <p
                                class="inline-block px-2 py-1 text-sm bg-gray-200 rounded-md dark:bg-neutral-900 text-tbn-dark dark:text-tbn-light">
                                {{ $search_title }}</p>
                        @endif
                        <p x-text="search_location" x-show="search_location"
                            class="inline-block px-2 py-1 text-sm bg-gray-200 rounded-md dark:bg-neutral-900 text-tbn-dark dark:text-tbn-light">
                        </p>
                    </div>
                    <div class="flex flex-col justify-center">
                        <button
                            class="inline-block w-10 h-10 transition-colors duration-150 rounded-full bg-tbn-primary hover:bg-tbn-secondary"
                            type="button" wire:click='clearSearch' x-on:click="clearData">
                            <i class="text-sm text-white fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        @endif
        <!-- Tags and random profesions -->
        <div class="mb-5" wire:loading.remove>
            <div class="flex flex-row flex-wrap gap-2">
                @foreach ($random_profesions as $profesion)
                    <a href="{{ route('search', ['title' => $profesion->profesion_name]) }}" wire:navigate
                        class="px-4 py-2 text-xs transition-all duration-200 bg-white rounded-full shadow-md dark:bg-neutral-800 text-tbn-dark dark:text-tbn-light hover:bg-tbn-primary dark:hover:bg-neutral-900 hover:text-white">
                        {{ $profesion->profesion_name }}</a>
                @endforeach
            </div>
        </div>
        <!-- Announcements -->
        <div class="grid w-full grid-cols-1 gap-4 mb-5 lg:grid-cols-2">
            @forelse ($announcements as $announce)
                <div wire:loading.remove>
                    <a href="{{ $this->isAnnouncePro($announce->pro) ? route('result', ['id' => $announce->id]) : route('purchase-cards') }}"
                        wire:navigate wire:key='announce-{{ $announce->id }}'>
                        <x-card-announce logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                            title="{{ $announce->announce_title }}" pro="{{ $announce->pro }}">
                            <x-slot name="profesions">
                                @foreach ($announce->profesions as $profesion)
                                    {{ $profesion->profesion_name }}
                                @endforeach
                            </x-slot>
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
                    description="No hemos encontrado coincidencias para tu búsqueda" wire:loading.remove>
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
                search_title: null,
                search_location_id: null,
                search_location: null,
                locations: @json($locations),
                setLocation(id) {
                    this.search_location_id = id
                    this.search_location = this.locations.find(item => item.id == id).location_name
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
</section>
