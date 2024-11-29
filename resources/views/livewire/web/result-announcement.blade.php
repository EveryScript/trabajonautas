<div class="max-w-4xl px-5">
    <!-- Result description -->
    <section class="bg-white rounded-lg shadow-md my-5 p-8" x-data="content">
        <div class="w-full flex sm:flex-row flex-col gap-4">
            <img alt="team" class="flex-shrink-0 rounded-lg w-[8rem] h-[8rem] object-cover object-center sm:mb-0 mb-4"
                src="{{ asset('storage/' . $announcement->company->company_image) }}">
            <div class="flex-grow">
                <h2 class="text-2xl font-bold uppercase">{{ $announcement->announce_title }}</h2>
                <h3 class="inline text-lg text-tbn-light font-medium mb-1">
                    {{ $announcement->company->company_name }}
                </h3>
                <p x-text="setCompanyType({{ $announcement->company->company_type_id }})"
                    class="inline text-xs px-2 py-[.1rem] text-blue-900 bg-blue-200 rounded-full">
                </p>
                <div class="grid grid-cols-2 font-normal">
                    <div>
                        <i class="fas fa-map-marker-alt text-red-500 pr-1"></i>
                        @forelse ($announcement->locations as $location)
                            <span>{{ $location->location_name }}</span>
                        @empty
                            <span>Sin ubicación</span>
                        @endforelse
                    </div>
                    <div>
                        <i class="fas fa-calendar-alt text-red-500 pr-1"></i>
                        <span class="">
                            @if ($announcement->expiration_time > Carbon\Carbon::now())
                                Expira
                            @else
                                Expiró
                            @endif
                            {{ (new Carbon\Carbon($announcement->expiration_time))->diffForHumans() }}
                        </span>
                    </div>
                    <div>
                        <i class="fas fa-calendar-alt text-red-500 pr-1"></i>
                        <span class=""> Publicado
                            {{ (new Carbon\Carbon($announcement->updated_at))->diffForHumans() }}
                        </span>
                    </div>
                    <div>
                        <i class="fas fa-money-bill text-red-500"></i>
                        <span class=""> Sueldo
                            {{ $announcement->salary }} Bs.
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="my-5">
            <h3 class="text-lg font-medium mb-1 tbn-special text-tbn-primary">Descripción</h3>
            <div
                class="font-normal [&_span]:bg-transparent [&_a]:underline [&_h1]:text-3xl [&_h2]:text-2xl [&_h3]:text-xl">
                {!! $announcement->description !!}
            </div>
        </div>
        <div class="mb-4">
            <!-- Share buttons -->
            <div class="mb-5 [&_a]:text-4xl [&_a]:text-tbn-dark [&_a]:pr-2">
                <h3 class="text-lg font-medium mb-1 tbn-special text-tbn-primary">Compartir</h3>
                {!! $share_buttons !!}
            </div>
            <!-- Back -->
            <x-secondary-button href="{{ route('search') }}" wire:navigate>
                <i class="fas fa-chevron-left pr-2"></i> Volver
            </x-secondary-button>
            <!-- Download -->
            <x-button wire:click="downloadFile('{{ $announcement->announce_file }}')">
                <i class="fas fa-file-download pr-2"></i> Descargar</x-button>
            <!-- Like -->
            @if ($user && $user->myAnnounces->contains($announcement->id))
                <x-button wire:click='removeAnnounce({{ $announcement->id }})'>
                    <i class="fas fa-bookmark pr-2"></i> Guardado
                    {{-- <span class="font-bold">{{ $announcement->usersOf()->count() }}</span> --}}
                </x-button>
            @else
            <x-secondary-button wire:click='saveAnnounce({{ $announcement->id }})'>
                    <i class="far fa-bookmark pr-2"></i> Guardar
                    {{-- <span class="font-bold">{{ $announcement->usersOf()->count() }}</span> --}}
                </x-secondary-button>
            @endif
        </div>
        <!-- If throw error (download) -->
        @if (session('status'))
            <div class="flex items-center p-4 mb-4 text-sm bg-red-100 text-red-500 rounded-lg" role="alert">
                <i class="fas fa-exclamation-triangle pr-2"></i>
                <span class="sr-only font-bold pr-2">Error</span>
                <div><span class="font-medium">Error</span> {{ session('status') }}</div>
            </div>
        @endif
    </section>
    <!-- Suggest -->
    <section class="mb-20">
        <h3 class="text-tbn-light text-xl font-medium mb-1">Convocatorias similares</h3>
        <div class="grid grid-cols-2 gap-4 mb-5">
            @forelse ($suggests as $suggest)
                <a href="{{ route('result', ['id' => $suggest->id]) }}" wire:navigate>
                    <x-card-announce logo_url="{{ $suggest->company->company_image }}">
                        <x-slot name="area">{{ $suggest->area->area_name }}</x-slot>
                        <x-slot name="title">{{ $suggest->announce_title }}</x-slot>
                        <x-slot name="company">{{ $suggest->company->company_name }}</x-slot>
                        <x-slot name="locations">
                            @forelse ($suggest->locations as $location)
                                <span>{{ $location->location_name }}</span>
                            @empty
                                <span>Sin ubicación</span>
                            @endforelse
                        </x-slot>
                        <x-slot name="expiration">
                            @if ($suggest->expiration_time > Carbon\Carbon::now())
                                Expira
                            @else
                                Expiró
                            @endif
                            {{ (new Carbon\Carbon($suggest->expiration_time))->diffForHumans() }}
                        </x-slot>
                    </x-card-announce>
                </a>
            @empty
                No hay sugerencias
            @endforelse
        </div>
    </section>
    @assets
    @endassets
    @script
        <script>
            Alpine.data('content', () => ({
                company_types: {!! $company_types !!},
                setCompanyType(id) {
                    return "Empresa " + this.company_types.find(item => item.id == id).company_type_name
                },
            }))
        </script>
    @endscript
</div>
