<section class="max-w-6xl mt-5 mb-10 px-5 mx-auto">
    <div x-data="content">
        <!-- Search fields -->
        <div class="w-full flex flex-row gap-2 mb-5 tbn-form">
            <div class="w-1/2">
                <x-label for="search_title" value="{{ __('Profesion actual') }}" />
                <x-input x-model="search_title" class="w-full px-2 py-[.6rem]" type="search" wire:model='search_title'
                    wire:keydown.enter='searchAnnounces(search_title, search_location_id)'
                    placeholder="Arquitecto, ingeniero ..." />
            </div>
            <div class="w-1/2 tbn-field" wire:ignore>
                <x-label for="search_location" value="{{ __('Departamento o región') }}" />
                <x-select @change="setCompanyName($event.target.value)" id="locations" wire:model="search_location">
                    <option value="0" selected>Cualquier lugar</option>
                    @forelse ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                    @empty
                        <option>No option avaliable</option>
                    @endforelse
                </x-select>
            </div>
            <div class="pt-5">
                <x-button wire:click="searchAnnounces(search_title, search_location_id)" class="h-full"
                    x-bind:disabled="!search_title && !search_location">Buscar</x-button>
            </div>
        </div>
        <!-- Search results -->
        <div class="flex flex-row items-center gap-2">
            <template x-if="search_title">
                <div
                    class="flex flex-row bg-tbn-primary text-white text-sm font-normal px-4 py-2 rounded-full gap-2 mb-5 shadow-md">
                    <p x-text="search_title"></p>
                    <button class="cursor-pointer" @click="search_title = ''">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                            <path
                                d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                        </svg>
                    </button>
                </div>
            </template>
            <template x-if="search_location">
                <div
                    class="flex flex-row bg-tbn-primary text-white text-sm font-normal px-4 py-2 rounded-full gap-2 mb-5 shadow-md">
                    <p x-text="search_location"></p>
                    <button class="cursor-pointer" @click="setCompanyName(0)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                            <path
                                d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>
        <!-- Results numbers -->
        @if (count($announcements) > 0)
            <div class="text-tbn-high font-light text-lg mb-5" wire:loading.remove>Resultados
                encontrados({{ count($announcements) }})</div>
        @endif
    </div>
    <!-- Announcements -->
    <div class="w-full grid grid-cols-2 gap-4 mb-5">
        @forelse ($announcements as $announcement)
            <div wire:loading.remove wire:key='announce-{{ $announcement->id }}'>
                <a href="{{ route('result', ['id' => $announcement->id]) }}" wire:navigate>
                    <x-card-announce
                        logo_url="{{ $announcement->company ? $announcement->company->company_image : '' }}">
                        <x-slot name="area">{{ $announcement->area ? $announcement->area->area_name : '' }}</x-slot>
                        <x-slot name="title">{{ $announcement->announce_title }}</x-slot>
                        <x-slot
                            name="company">{{ $announcement->company ? $announcement->company->company_name : '' }}</x-slot>
                        <x-slot name="locations">
                            @forelse ($announcement->locations as $location)
                                <span>{{ $location->location_name }}</span>
                            @empty
                                <span>Sin ubicación</span>
                            @endforelse
                        </x-slot>
                        <x-slot name="expiration">
                            @if ($announcement->expiration_time > Carbon\Carbon::now())
                                Expira
                            @else
                                Expiró
                            @endif
                            {{ (new Carbon\Carbon($announcement->expiration_time))->diffForHumans() }}
                        </x-slot>
                    </x-card-announce>
                </a>
            </div>
        @empty
            <div class="col-span-2 text-gray-600 w-full text-center px-24 py-12" wire:loading.remove>
                <svg xmlns="http://www.w3.org/2000/svg" class="max-w-12 mx-auto mb-1" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <span class="text-md italic">No hay coincidencias para tu búsqueda</span>
            </div>
        @endforelse
    </div>
    <div wire:loading.remove> {{ $announcements->links() }} </div>
    <!-- Loading card skeletons -->
    <div class="w-full" wire:loading>
        <x-cards-loading />
    </div>

    @script
        <script>
            Alpine.data('content', () => ({
                search_title: '',
                search_location: '',
                search_location_id: '',
                locations: {!! $locations !!},
                setCompanyName(id) {
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
