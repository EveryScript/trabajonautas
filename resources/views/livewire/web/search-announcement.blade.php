<section class="max-w-6xl px-3 py-5 mx-auto sm:px-6">
    <div x-data="content">
        <!-- Search fields -->
        <div class="grid items-end w-full grid-cols-1 gap-4 mb-4 md:grid-cols-2 tbn-form">
            <div wire:ignore class="tbn-tom-select">
                <x-label class="mb-1" for="profesion" value="{{ __('¿Cuál es tu profesión?') }}" />
                <select id="profesion" x-model="profesion_id" @keyup.enter="searchAnnouncements"
                    placeholder="Arquitecto, minero..." class="w-full mt-1">
                    <option></option>
                    @foreach ($profesions as $p)
                        <option value="{{ $p->id }}">{{ $p->profesion_name }}</option>
                    @endforeach
                </select>
            </div>
            <div wire:ignore class="tbn-tom-select">
                <x-label class="mb-1" for="location" value="{{ __('Departamento o región') }}" />
                <select id="location" x-model="location_id" @keyup.enter="searchAnnouncements"
                    placeholder="La Paz, Oruro..." class="w-full mt-1">
                    <option value="">Toda Bolivia</option>
                    @foreach ($locations as $l)
                        <option value="{{ $l->id }}">{{ $l->location_name }}</option>
                    @endforeach
                </select>
            </div>

            <div wire:ignore class="tbn-tom-select">
                <x-label class="mb-1" for="company" value="{{ __('Empresa') }}" />
                <select id="company" x-model="company_id" @keyup.enter="searchAnnouncements"
                    placeholder="Impuestos nacionales, Aduana" class="w-full mt-1">
                    <option></option>
                    @foreach ($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div wire:ignore>
                <x-label class="mb-1" for="post-date" value="{{ __('Fecha de publicación') }}" />
                <x-input class="w-full py-[0.75rem]" id="post-date" type="text" readonly
                    placeholder="Fecha de publicación"></x-input>
            </div>

            <div class="col-span-1 mt-2 md:col-span-2">
                <x-button type="button" x-on:click="searchAnnouncements"
                    class="flex items-center justify-center w-full h-12 gap-2 font-semibold text-white transition-all duration-200 ease-in-out bg-indigo-600 rounded-lg shadow-md cursor-pointer hover:bg-indigo-700 active:bg-indigo-800 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="text-lg fa-solid fa-magnifying-glass"></i>
                    <span>{{ __('Buscar convocatorias') }}</span>
                </x-button>
            </div>
        </div>

        <!-- Review searching -->
        <div
            class="p-6 mb-4 transition-all duration-300 bg-white border shadow-sm dark:bg-tbn-dark rounded-2xl border-tbn-light dark:border-tbn-secondary">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center sm:space-x-4">
                    <div
                        class="hidden p-3 shadow-lg sm:block bg-gradient-to-br from-tbn-primary to-tbn-primary/90 rounded-xl shadow-tbn-light dark:shadow-none shrink-0">
                        <i class="text-2xl text-white translate-y-1 fa-solid fa-rocket" wire:loading.remove></i>
                        <i class="text-2xl text-white fa-solid fa-search animate-pulse" wire:loading></i>
                    </div>
                    <div>
                        <div class="flex flex-col items-baseline mb-2 space-x-2 md:flex-row">
                            <h3 class="text-3xl font-extrabold tracking-tight text-tbn-dark dark:text-white">
                                <span wire:loading.remove>{{ $this->totalResults }}</span>
                                <span wire:loading>Buscando...</span>
                            </h3>
                            <span wire:loading.remove
                                class="text-sm tracking-widest uppercase text-tbn-secondary dark:text-tbn-light">
                                convocatorias encontradas
                            </span>
                        </div>

                        @if ($profesion_id || $location_id || $company_id || $post_date)
                            {{-- Badge Profesión --}}
                            @if ($profesion_id)
                                <span
                                    class="inline-flex items-center gap-1.5 px-4 py-2 mb-1 text-xs font-medium text-tbn-primary bg-tbn-primary/10 rounded-full dark:bg-tbn-primary/20 dark:text-white">
                                    <i class="fa-solid fa-briefcase"></i>
                                    {{ $profesions->firstWhere('id', $profesion_id)?->profesion_name }}
                                    <button type="button" x-on:click="setClear('profesion_id')"
                                        class="ml-1 transition-colors hover:text-red-500 focus:outline-none">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </span>
                            @endif

                            {{-- Badge Ubicación --}}
                            @if ($location_id)
                                <span
                                    class="inline-flex items-center gap-1.5 px-4 py-2 mb-1 text-xs font-medium text-tbn-primary bg-tbn-primary/10 rounded-full dark:bg-tbn-primary/20 dark:text-white">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $locations->firstWhere('id', $location_id)?->location_name }}
                                    <button type="button" x-on:click="setClear('location_id')"
                                        class="ml-1 transition-colors hover:text-red-500 focus:outline-none">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </span>
                            @endif

                            {{-- Badge Empresa --}}
                            @if ($company_id)
                                <span
                                    class="inline-flex items-center gap-1.5 px-4 py-2 mb-1 text-xs font-medium text-tbn-primary bg-tbn-primary/10 rounded-full dark:bg-tbn-primary/20 dark:text-white">
                                    <i class="fa-solid fa-building"></i>
                                    {{ $companies->firstWhere('id', $company_id)?->company_name }}
                                    <button type="button" x-on:click="setClear('company_id')"
                                        class="ml-1 transition-colors hover:text-red-500 focus:outline-none">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </span>
                            @endif

                            {{-- Badge Fecha --}}
                            @if ($post_date)
                                <span
                                    class="inline-flex items-center gap-1.5 px-4 py-2 mb-1 text-xs font-medium text-tbn-primary bg-tbn-primary/10 rounded-full dark:bg-tbn-primary/20 dark:text-white">
                                    <i class="fa-solid fa-calendar"></i>
                                    {{ $post_date }}
                                    <button type="button" x-on:click="setClear('post_date')"
                                        class="ml-1 transition-colors hover:text-red-500 focus:outline-none">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Botón para limpiar todos los filtros a la vez --}}
                @if ($profesion_id || $location_id || $company_id || $post_date)
                    <button type="button" x-on:click="clearFilters"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-tbn-primary transition-colors bg-red-50 hover:bg-red-100 rounded-lg dark:bg-red-950/30 dark:text-tbn-primary dark:hover:bg-red-900/40 border border-red-200 dark:border-red-800/50 cursor-pointer shrink-0 self-start md:self-auto">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Limpiar filtros</span>
                    </button>
                @endif
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
            <h4 class="mb-4 text-lg font-semibold text-tbn-primary" wire:loading.remove wire:target='recommends'>
                También te puede interesar
            </h4>
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
                // Tom Select Instances
                profesion_ts: null,
                location_ts: null,
                company_ts: null,
                post_date_fp: null,

                // Model properties
                profesion_id: null,
                location_id: null,
                company_id: null,
                post_date: null,

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const urlProfesion = urlParams.get('profesion_id');
                    const urlLocation = urlParams.get('location_id');
                    const urlCompany = urlParams.get('company_id');

                    this.profesion_id = urlProfesion && urlProfesion !== 'null' ? Number(urlProfesion) : null;
                    this.location_id = urlLocation && urlLocation !== 'null' ? Number(urlLocation) : null;
                    this.company_id = urlCompany && urlCompany !== 'null' ? Number(urlCompany) : null;

                    // Initialize TomSelects
                    this.profesion_ts = new TomSelect('#profesion', {
                        allowEmptyOption: true,
                        items: this.profesion_id ? [this.profesion_id] : []
                    });
                    this.location_ts = new TomSelect('#location', {
                        allowEmptyOption: true,
                        items: this.location_id ? [this.location_id] : []
                    });
                    this.company_ts = new TomSelect('#company', {
                        allowEmptyOption: true,
                        items: this.company_id ? [this.company_id] : []
                    });

                    // Tom Select Change Listeners
                    this.profesion_ts.on('change', (value) => {
                        this.profesion_id = value ? Number(value) : null;
                        this.updateUrlParam('profesion_id', value);
                    });
                    this.location_ts.on('change', (value) => {
                        this.location_id = value ? Number(value) : null;
                        this.updateUrlParam('location_id', value);
                    });
                    this.company_ts.on('change', (value) => {
                        this.company_id = value ? Number(value) : null;
                        this.updateUrlParam('company_id', value);
                    });

                    // Initialize Flatpickr
                    this.post_date_fp = flatpickr("#post-date", {
                        dateFormat: "d/m/Y",
                        locale: "es",
                        onChange: (selectedDates, dateStr) => {
                            this.post_date = dateStr || null;
                        }
                    });
                },

                updateUrlParam(key, value) {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (value) {
                        urlParams.set(key, value);
                    } else {
                        urlParams.delete(key);
                    }
                    const nuevaUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() :
                        '');
                    window.history.replaceState({}, document.title, nuevaUrl);
                },

                // Clear a single filter
                setClear(prop) {
                    if (prop === 'profesion_id') {
                        this.profesion_id = null;
                        this.profesion_ts.clear();
                    } else if (prop === 'location_id') {
                        this.location_id = null;
                        this.location_ts.clear();
                    } else if (prop === 'company_id') {
                        this.company_id = null;
                        this.company_ts.clear();
                    } else if (prop === 'post_date') {
                        this.post_date = null;
                        if (this.post_date_fp) this.post_date_fp.clear();
                    }

                    this.updateUrlParam(prop, null);
                    $wire.set(prop, null);
                },

                // Clear all filters at once
                clearFilters() {
                    this.profesion_id = null;
                    this.location_id = null;
                    this.company_id = null;
                    this.post_date = null;

                    this.profesion_ts.clear();
                    this.location_ts.clear();
                    this.company_ts.clear();
                    if (this.post_date_fp) this.post_date_fp.clear();

                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.delete('profesion_id');
                    urlParams.delete('location_id');
                    urlParams.delete('company_id');
                    window.history.replaceState({}, document.title, window.location.pathname);

                    $wire.set('profesion_id', null);
                    $wire.set('location_id', null);
                    $wire.set('company_id', null);
                    $wire.set('post_date', null);
                },

                // Submit/Search action
                searchAnnouncements() {
                    $wire.set('profesion_id', this.profesion_id ? Number(this.profesion_id) : null);
                    $wire.set('location_id', this.location_id ? Number(this.location_id) : null);
                    $wire.set('company_id', this.company_id ? Number(this.company_id) : null);
                    $wire.set('post_date', this.post_date ?? null);
                }
            }));
        </script>
    @endscript
</section>
