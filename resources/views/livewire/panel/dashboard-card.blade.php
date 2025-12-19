<section x-data="{ show_dropdown: false }" class="flex flex-col gap-4">
    <header class="flex flex-row">
        <div class="flex-1">
            <h3 class="text-lg font-medium">Convocatorias de trabajo para ti</h3>
            <small class="text-tbn-secondary text-xs">Te presentamos las convocatorias más recientes del país.</small>
        </div>
        <!-- Filter suggests -->
        <div class="relative">
            <button @click="show_dropdown = true" type="button"
                class="flex px-3 py-2 text-sm font-medium text-tbn-secondary border border-gray-300 bg-white rounded-md"
                id="suggest-menu" aria-expanded="false" data-dropdown-toggle="suggest-dropdown"
                data-dropdown-placement="bottom">
                <span wire:loading.remove>
                    {{ $filter_title }} <i class="fa-solid fa-angle-down ml-2"></i></span>
                <span wire:loading class="text-tbn-secondary">
                    <i class="text-tbn-dark fas fa-spinner text-sm animate-spin"></i></span>
            </button>
            <div x-show="show_dropdown" @click.outside="show_dropdown = false"
                class="absolute w-40 shadow-lg top-6 right-0 z-50 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg"
                id="suggest-dropdown">
                <ul @click="show_dropdown = false" class="py-2" aria-labelledby="user-menu-button">
                    <li class="cursor-pointer">
                        <a wire:click='filterSuggests(1)'
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Publicado hoy</a>
                    </li>
                    <li class="cursor-pointer">
                        <a wire:click='filterSuggests(2)'
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Publicado esta semana</a>
                    </li>
                    <li class="cursor-pointer">
                        <a wire:click='filterSuggests(3)'
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Publicado este mes</a>
                    </li>
                    <li class="cursor-pointer">
                        <a wire:click='filterSuggests(4)'
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Mis convocatorias</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    @forelse ($suggests as $announce)
        <a href="{{ $this->isAnnouncePro($announce->pro) ? route('result', ['id' => $announce->id]) : route('purchase-cards') }}"
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
