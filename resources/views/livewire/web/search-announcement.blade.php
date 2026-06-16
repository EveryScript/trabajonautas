<section class="max-w-6xl px-3 py-5 mx-auto sm:px-6">
    <div x-data="content">
        <!-- Search fields -->
        <div class="flex flex-col w-full gap-2 mb-4 md:flex-row tbn-form">
            <div wire:ignore class="w-full md:w-1/2 tbn-tom-select">
                <x-label class="mb-1" for="profesion" value="{{ __('¿Cuál es tu profesión?') }}" />
                <select id="profesion" x-model="profesion_id" @keyup.enter="searchAnnouncements"
                    placeholder="Arquitecto, minero..." class="mt-1">
                    <option></option>
                    @foreach ($profesions as $p)
                        <option value="{{ $p->id }}">{{ $p->profesion_name }}</option>
                    @endforeach
                </select>
            </div>
            <div wire:ignore class="w-full md:w-1/2 tbn-tom-select">
                <x-label class="mb-1" for="location" value="{{ __('Departamento o región') }}" />
                <select id="location" x-model="location_id" @keyup.enter="searchAnnouncements"
                    placeholder="La Paz, Oruro..." class="mt-1">
                    <option value="">Toda Bolivia</option>
                    @foreach ($locations as $l)
                        <option value="{{ $l->id }}">{{ $l->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <x-button class="h-full py-3 mt-2 md:mt-6" x-on:click="searchAnnouncements">
                <i class="pt-1 text-lg md:pt-0 md:text-2xl fa-solid fa-magnifying-glass"></i>
            </x-button>
        </div>
        <!-- Review searching -->
        <div
            class="p-6 mb-4 transition-all duration-300 bg-white border shadow-sm dark:bg-tbn-dark rounded-2xl border-tbn-light dark:border-tbn-secondary">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="p-3 shadow-lg bg-gradient-to-br from-tbn-primary to-tbn-primary/90 rounded-xl shadow-tbn-light dark:shadow-none">
                        <i class="text-2xl text-white translate-y-1 fa-solid fa-rocket" wire:loading.remove></i>
                        <i class="text-2xl text-white fa-solid fa-search animate-pulse" wire:loading></i>
                    </div>
                    <div>
                        <div class="flex flex-col items-baseline space-x-2 md:flex-row">
                            <h3 class="text-3xl font-extrabold tracking-tight text-tbn-dark dark:text-white">
                                <span wire:loading.remove>{{ $this->totalResults }}</span>
                                <span wire:loading>Buscando...</span>
                            </h3>
                            <span wire:loading.remove
                                class="text-sm tracking-widest uppercase text-tbn-secondary dark:text-tbn-light">
                                convocatorias encontradas</span>
                        </div>
                        @if ($profesion_id || $location_id)
                            <p class="m-1 text-sm text-tbn-secondary dark:text-tbn-light">
                                Resultados
                                @if ($profesion_id)
                                    para <span class="font-semibold text-tbn-primary">
                                        "{{ $profesions->firstWhere('id', $profesion_id)->profesion_name }}"
                                @endif
                                </span> en <span class="font-semibold text-tbn-primary">
                                    {{ $location_id ? $locations->firstWhere('id', $location_id)->location_name : 'Toda Bolivia' }}
                                </span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Announcements -->
        @if ($hasResults)
            <div class="grid w-full grid-cols-1 gap-4 mb-5 lg:grid-cols-2" wire:loading.remove
                wire:target='announcements'>
                @foreach ($announcements as $announce)
                    <div wire:key='announce-{{ $announce->id }}'>
                        <x-card-announce :announce="$announce" :client="$client_pro_authorized" />
                    </div>
                @endforeach
            </div>
            @if ($announcements->count() < $this->totalResults)
                <div class="flex flex-row justify-center mb-4">
                    <x-button wire:click="loadMore" wire:loading.attr="disabled" wire:target='loadMore'>
                        <span wire:loading.remove wire:target="loadMore">
                            <i class="mr-1 fa-solid fa-angles-down"></i> Ver más
                        </span>
                        <span wire:loading wire:target="loadMore">
                            <i class="mr-1 fa-solid fa-spinner animate-spin"></i> Cargando...
                        </span>
                    </x-button>
                </div>
            @endif
        @endif
        <!-- Recommends -->
        @if ($recommends->isNotEmpty())
            <h4 class="mb-4 text-lg font-semibold text-tbn-primary" wire:loading.remove wire:target='recommends'>También
                te puede interesar</h4>
            <div class="grid w-full grid-cols-1 gap-4 mb-5 lg:grid-cols-2" wire:loading.remove wire:target='recommends'>
                @foreach ($recommends as $announce)
                    <div wire:key='announce-{{ $announce->id }}'>
                        <x-card-announce :announce="$announce" :client="$client_pro_authorized" />
                    </div>
                @endforeach
            </div>
        @endif
        <div class="w-full" wire:loading wire:target='announcements'><x-cards-loading /></div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                profesion_id: null,
                location_id: null,
                profesion_ts: null,
                location_ts: null,
                init() {
                    // Set url params
                    const urlParams = new URLSearchParams(window.location.search);
                    const urlProfesion = urlParams.get('profesion_id');
                    const urlLocation = urlParams.get('location_id');
                    this.profesion_id = urlProfesion && urlProfesion !== 'null' ? Number(urlProfesion) : null;
                    this.location_id = urlLocation && urlLocation !== 'null' ? Number(urlLocation) : null;
                    // Set values in TomSelect
                    this.profesion_ts = new TomSelect('#profesion', {
                        allowEmptyOption: true,
                        items: this.profesion_id ? [this.profesion_id] : []
                    });
                    this.location_ts = new TomSelect('#location', {
                        allowEmptyOption: true,
                        items: this.location_id ? [this.location_id] : []
                    });
                    // Listen changes in Tom Select
                    this.profesion_ts.on('change', (value) => {
                        this.profesion_id = value ? Number(value) : null;
                    });
                    this.location_ts.on('change', (value) => {
                        if (value) {
                            this.location_id = Number(value);
                        } else {
                            urlParams.delete('location_id');
                            const nuevaUrl = window.location.pathname + (urlParams.toString() ? '?' +
                                urlParams.toString() : '');
                            window.history.replaceState({}, document.title, nuevaUrl);
                        }
                    });
                },
                destroy() {
                    if (this.profesion_ts)
                        this.profesion_ts.destroy()
                    if (this.location_ts)
                        this.location_ts.destroy()
                },
                // Functions
                searchAnnouncements() {
                    if (this.profesion_id)
                        $wire.set('profesion_id', Number(this.profesion_id))
                    if (this.location_id)
                        $wire.set('location_id', Number(this.location_id))
                    else
                        $wire.set('location_id', null)
                }
            }));
        </script>
    @endscript
</section>
