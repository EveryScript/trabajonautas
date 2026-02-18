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
                    <option>Toda Bolivia</option>
                    @foreach ($locations as $l)
                        <option value="{{ $l->id }}">{{ $l->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <x-button class="h-full mt-2 md:mt-6" x-on:click="searchAnnouncements">
                <i class="pt-2 text-lg md:pt-0 md:text-2xl fa-solid fa-magnifying-glass"></i>
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
            <div class="grid w-full grid-cols-1 gap-4 mb-5 lg:grid-cols-2" wire:loading.remove>
                @foreach ($announcements as $announce)
                    <div wire:key='announce-{{ $announce->id }}'>
                        <x-card-announce :announce="$announce" :client="$client_pro_authorized" />
                    </div>
                @endforeach
            </div>
            <div class="mb-4" wire:loading.remove> {{ $announcements->links() }} </div>
        @endif
        <!-- Recommends -->
        @if ($recommends->isNotEmpty())
            <h4 class="mb-4 text-lg font-semibold text-tbn-primary" wire:loading.remove>También te puede interesar</h4>
            <div class="grid w-full grid-cols-1 gap-4 mb-5 lg:grid-cols-2" wire:loading.remove>
                @foreach ($recommends as $announce)
                    <div wire:key='announce-{{ $announce->id }}'>
                        <x-card-announce :announce="$announce" :client="$client_pro_authorized" />
                    </div>
                @endforeach
            </div>
        @endif
        <div class="w-full" wire:loading><x-cards-loading /></div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                profesion_id: null,
                location_id: null,
                profesion_ts: null,
                location_ts: null,
                init() {
                    this.profesion_ts = new TomSelect('#profesion', {
                        onItemAdd: function(value) {
                            this.profesion_id = value
                        }
                    })
                    this.location_ts = new TomSelect('#location', {
                        onItemAdd: function(value) {
                            this.location_id = value
                        }
                    })
                },
                destroy() {
                    if (this.profesion_ts)
                        this.profesion_ts.destroy()
                    if (this.location_ts)
                        this.location_ts.destroy()
                },
                // Functions
                searchAnnouncements() {
                    if (this.profesion_id) {
                        $wire.set('profesion_id', Number(this.profesion_id))
                    }
                    if (this.location_id)
                        $wire.set('location_id', Number(this.location_id))
                }
            }));
        </script>
    @endscript
</section>
