<section class="max-w-6xl mt-5 mb-10 px-5 mx-auto">
    <div x-data="content">
        <!-- Search fields -->
        <div class="w-full flex flex-col md:flex-row gap-2 mb-5 tbn-form">
            <div class="w-full md:w-1/2">
                <x-label for="search_title" value="{{ __('¿Cuál es tu profesión?') }}" />
                <x-input x-model="search_title" class="w-full px-2 py-[.6rem]" type="search" wire:model='search_title'
                    wire:keydown.enter='searchAnnounces(search_title, search_location_id)'
                    placeholder="Arquitecto, ingeniero ..." />
            </div>
            <div class="w-full md:w-1/2 tbn-field" wire:ignore>
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
                <x-button wire:click="searchAnnounces(search_title, search_location_id)" class="h-[3rem] mt-1"
                    x-bind:disabled="!search_title && !search_location">Buscar</x-button>
            </div>
        </div>
        <!-- Search elements -->
        <div class="flex flex-row items-center gap-2">
            <template x-if="search_title">
                <div
                    class="flex flex-row bg-tbn-primary text-white text-sm font-normal px-4 py-2 rounded-full gap-2 mb-5 shadow-md">
                    <p x-text="search_title"></p>
                    <button class="cursor-pointer" @click="search_title = ''">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </template>
            <template x-if="search_location">
                <div
                    class="flex flex-row bg-tbn-primary text-white text-sm font-normal px-4 py-2 rounded-full gap-2 mb-5 shadow-md">
                    <p x-text="search_location"></p>
                    <button class="cursor-pointer" @click="setLocationName(0)">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </template>
        </div>
        <!-- Results numbers -->
        @if (count($announcements) > 0 && $search_title)
            <div class="text-tbn-high font-medium text-sm mb-5" wire:loading.remove>Resultados
                encontrados ({{ count($announcements) }})</div>
        @endif

        <!-- Announcements -->
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
            @forelse ($announcements as $announce)
                <div wire:loading.remove>
                    <a href="{{ $announce->pro && (!$client || !$pro_verified) ? route('purchase') : route('result', ['id' => $announce->id]) }}"
                        wire:navigate wire:key='announce-{{ $announce->id }}'>
                        <x-card-announce logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                            pro="{{ $announce->pro }}">
                            <x-slot name="area">
                                {{ $announce->area ? $announce->area->area_name : '' }}</x-slot>
                            <x-slot name="title">{{ $announce->announce_title }}</x-slot>
                            <x-slot name="company">
                                {{ $announce->company ? $announce->company->company_name : '' }}</x-slot>
                            <x-slot name="locations">
                                {{ $announce->locations[0]->location_name }}
                                @if ($announce->locations->count() > 1)
                                    <i class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                                @endif
                            </x-slot>
                        </x-card-announce>
                    </a>
                </div>
            @empty
                <x-section-empty class="col-span-2" title="No hay resultados"
                    description="No hemos encontrado coincidencias para tu busqueda" wire:loading.remove>
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
            new TomSelect('#locations', []);
        </script>
    @endscript
</section>
