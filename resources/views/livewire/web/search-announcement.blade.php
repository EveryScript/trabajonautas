<section class="max-w-6xl px-3 py-5 mx-auto sm:px-6">
    <div x-data="content">
        <!-- Search fields -->
        <div class="grid items-end w-full grid-cols-1 gap-4 mb-4 md:grid-cols-2 tbn-form">
            <div wire:ignore class="tbn-tom-select">
                <x-label class="mb-1" for="profesion" value="{{ __('¿Cuál es tu profesión?') }}" />
                <select id="profesion" @keyup.enter="searchAnnouncements" placeholder="Arquitecto, minero..."
                    class="w-full mt-1">
                    <option></option>
                    @foreach ($profesions as $p)
                        <option value="{{ $p->id }}" @selected($profesion_id == $p->id)>{{ $p->profesion_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div wire:ignore class="tbn-tom-select">
                <x-label class="mb-1" for="location" value="{{ __('Departamento o región') }}" />
                <select id="location" @keyup.enter="searchAnnouncements" placeholder="La Paz, Oruro..."
                    class="w-full mt-1">
                    <option value="">Toda Bolivia</option>
                    @foreach ($locations as $l)
                        <option value="{{ $l->id }}" @selected($location_id == $l->id)>{{ $l->location_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div wire:ignore class="tbn-tom-select">
                <x-label class="mb-1" for="company" value="{{ __('Empresa') }}" />
                <select id="company" @keyup.enter="searchAnnouncements" placeholder="Impuestos nacionales, Aduana"
                    class="w-full mt-1">
                    <option></option>
                    @foreach ($companies as $c)
                        <option value="{{ $c->id }}" @selected($company_id == $c->id)>{{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div wire:ignore>
                <x-label class="mb-1" for="post-date" value="{{ __('Fecha de publicación') }}" />
                <x-input class="w-full py-[0.75rem]" id="post-date" type="text" readonly value="{{ $post_date }}"
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
                profesion_ts: null,
                location_ts: null,
                company_ts: null,
                post_date_fp: null,

                _navigatedHandler: null,
                _navigatingHandler: null,

                init() {
                    this.setupWidgets();

                    this._navigatedHandler = () => this.setupWidgets();
                    this._navigatingHandler = () => {
                        this.destroyWidgets();
                        sessionStorage.setItem('lastSearchUrl', window.location.href);
                    };

                    document.addEventListener('livewire:navigated', this._navigatedHandler);
                    document.addEventListener('livewire:navigating', this._navigatingHandler);
                },

                destroy() {
                    document.removeEventListener('livewire:navigated', this._navigatedHandler);
                    document.removeEventListener('livewire:navigating', this._navigatingHandler);
                    this.destroyWidgets();
                },

                setupWidgets() {
                    this.destroyWidgets();

                    this.profesion_ts = new TomSelect('#profesion', {});
                    this.company_ts = new TomSelect('#company', {});
                    this.location_ts = new TomSelect('#location', {
                        allowEmptyOption: true
                    });
                    this.post_date_fp = flatpickr('#post-date', {
                        dateFormat: 'd/m/Y',
                        locale: 'es',
                    });
                },

                destroyWidgets() {
                    this.profesion_ts?.destroy();
                    this.location_ts?.destroy();
                    this.company_ts?.destroy();
                    this.post_date_fp?.destroy();

                    this.profesion_ts = null;
                    this.location_ts = null;
                    this.company_ts = null;
                    this.post_date_fp = null;
                },

                // Clear one filter
                setClear(prop) {
                    if (prop === 'profesion_id') this.profesion_ts?.clear();
                    else if (prop === 'location_id') this.location_ts?.clear();
                    else if (prop === 'company_id') this.company_ts?.clear();
                    else if (prop === 'post_date') this.post_date_fp?.clear();

                    $wire.set(prop, null);
                },

                // Clear all filtera
                clearFilters() {
                    this.profesion_ts?.clear();
                    this.location_ts?.clear();
                    this.company_ts?.clear();
                    this.post_date_fp?.clear();

                    $wire.set('profesion_id', null);
                    $wire.set('location_id', null);
                    $wire.set('company_id', null);
                    $wire.set('post_date', null);
                },

                // Submit/Search action
                searchAnnouncements() {
                    const profesionValue = this.profesion_ts?.getValue();
                    const locationValue = this.location_ts?.getValue();
                    const companyValue = this.company_ts?.getValue();
                    const dateValue = this.post_date_fp?.input?.value;

                    $wire.set('profesion_id', profesionValue ? Number(profesionValue) : null);
                    $wire.set('location_id', locationValue ? Number(locationValue) : null);
                    $wire.set('company_id', companyValue ? Number(companyValue) : null);
                    $wire.set('post_date', dateValue || null);
                }
            }));
        </script>
    @endscript
</section>
