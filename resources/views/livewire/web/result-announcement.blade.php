<div class="flex flex-col md:flex-row gap-6 px-4">
    <section class="w-full md:w-3/5 select-none" x-data="content">
        <div class="relative bg-white rounded-lg shadow-md p-5 sm:p-10">
            <span class="absolute top-6 right-6 {{ $announcement->pro ? '' : 'hidden' }}">
                <i class="fas fa-crown text-md text-tbn-secondary"></i></span>
            <div class=" w-full flex sm:flex-row flex-col gap-2 sm:gap-6">
                <img alt="team"
                    class="flex-shrink-0 rounded-lg w-[5rem] h-[5rem] object-cover object-center sm:mb-0 mb-4"
                    src="{{ asset('storage/' . $announcement->company->company_image) }}">
                <div class="flex-grow">
                    <h2 class="text-xl font-bold uppercase leading-6">{{ $announcement->announce_title }}</h2>
                    <h3 class="inline-block text-md font-medium mb-2 text-tbn-primary">
                        {{ $announcement->company->company_name }}
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="flex flex-col gap-1 text-sm text-tbn-dark font-normal mb-2">
                            @forelse ($announcement->locations as $location)
                                <span><i class="fas fa-map-marker-alt text-red-500 pr-1"></i>
                                    {{ $location->location_name }}</span>
                            @empty
                                <span>Sin ubicación</span>
                            @endforelse
                        </div>
                        <div class="text-sm text-tbn-dark font-normal">
                            <div class="mb-2">
                                <i class="fas fa-calendar-alt text-red-500 pr-1"></i>
                                <span class="">
                                    @if ($announcement->expiration_time > Carbon\Carbon::now())
                                        Postula hasta el
                                    @else
                                        Fecha límite
                                    @endif
                                    {{ $this->formatDate($announcement->expiration_time) }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar-alt text-red-500 pr-1"></i>
                                <span class=""> Publicado
                                    {{ \Carbon\Carbon::parse($announcement->updated_at)->diffForHumans() }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-money-bill text-red-500 pr-1"></i>
                                <span class=""> Sueldo
                                    {{ $announcement->salary }} Bs.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="my-3">
                <h3 class="text-lg font-medium mb-1 tbn-special text-tbn-primary">Descripción</h3>
                <div
                    class="font-normal [&_ol]:list-disc [&_ol]:ml-4 [&_span]:bg-transparent [&_a]:underline [&_h1]:text-2xl [&_h2]:text-xl [&_h3]:text-lg text-sm">
                    {!! $announcement->description !!}
                </div>
            </div>
            <div class="my-4">
                <!-- Save -->
                @if ($client && $client->myAnnounces->contains($announcement->id))
                    <x-button class="w-full sm:w-auto my-1" wire:click='removeAnnounce({{ $announcement->id }})'>
                        <i class="fas fa-bookmark pr-2 text-sm"></i> Guardado
                    </x-button>
                @else
                    <x-button class="w-full sm:w-auto my-1" wire:click='saveAnnounce({{ $announcement->id }})'>
                        <i class="far fa-bookmark pr-2 text-sm"></i> Guardar
                    </x-button>
                @endif
                @if ($announcement->announceFiles && count($announcement->announceFiles))
                    <x-button class="w-full sm:w-auto my-1" wire:click='downloadAnnounceFiles()'>
                        <i class="fas fa-arrow-down pr-2 text-sm"></i> Descargar archivos
                    </x-button>
                @endif
                <!-- Return -->
                <a href="{{ route('search') }}" wire:navigate>
                    <x-secondary-button class="w-full sm:w-auto my-1">
                        <i class="fas fa-arrow-left pr-2 text-sm"></i> Volver
                    </x-secondary-button>
                </a>
            </div>
        </div>
    </section>
    <section class="w-full md:w-2/5">
        {{-- Company info --}}
        <div class="mb-4">
            <h3 class="text-tbn-dark text-md font-medium mb-1">Información de la empresa</h3>
            <div class="bg-white rounded-lg shadow-md p-5">
                <picture class="block mb-0 md:mb-2">
                    <img alt="company-logo"
                        class="flex-shrink-0 rounded-lg w-12 h-12 object-cover object-center sm:mb-0 mb-4"
                        src="{{ asset('storage/' . $announcement->company->company_image) }}">
                </picture>
                <h5 class="inline font-bold mb-2 text-tbn-primary">{{ $announcement->company->company_name }}</h5>
                <span class="bg-gray-200 px-2 rounded-lg text-xs"> Empresa:
                    {{ $announcement->company->companyType->company_type_name }}</span>
                <p class="text-tbn-dark text-sm">{{ $announcement->company->description }}</p>
            </div>
        </div>
        {{-- Suggest --}}
        <div class="mb-4">
            <h3 class="text-tbn-dark text-md font-medium mb-1">Convocatorias similares</h3>
            <div class="flex flex-col gap-2">
                @forelse ($suggests as $suggest)
                    <a href="{{ $suggest->pro && (!$client || !$pro_verified) ? route('purchase-cards') : route('result', ['id' => $suggest->id]) }}"
                        wire:navigate wire:key='suggest-{{ $suggest->id }}'>
                        <x-card-announce logo_url="{{ $suggest->company->company_image }}"
                            logo_flag="{{ false }}" pro="{{ $suggest->pro }}">
                            <x-slot name="area">
                                {{ $suggest->area->area_name }}
                            </x-slot>
                            <x-slot name="title">{{ $suggest->announce_title }}</x-slot>
                            <x-slot name="company">{{ $suggest->company->company_name }}</x-slot>
                            <x-slot name="locations">
                                {{ $suggest->locations[0]->location_name }}
                                @if ($suggest->locations->count() > 1)
                                    <i class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                                @endif
                            </x-slot>
                        </x-card-announce>
                    </a>
                @empty
                    <p class="text-tbn-dark text-sm text-center my-8">
                        No hay sugerencias para esta convocatoria</p>
                @endforelse
            </div>
        </div>
    </section>
    @script
        <script>
            Alpine.data('content', () => ({
                modalShare: false
            }))
        </script>
    @endscript
</div>
