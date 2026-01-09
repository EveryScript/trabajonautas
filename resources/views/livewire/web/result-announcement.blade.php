<div class="flex flex-col gap-6 px-4 md:flex-row">
    <section class="w-full select-none md:w-3/5" x-data="content">
        <div
            class="relative bg-transparent sm:bg-white sm:border sm:rounded-lg sm:shadow-md sm:dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary sm:p-10">
            <span class="absolute top-6 right-6 {{ $announcement->pro ? '' : 'hidden' }}">
                <i class="fas fa-crown text-md text-tbn-primary"></i></span>
            <div class="flex flex-col w-full gap-2 sm:flex-row sm:gap-6">
                <img alt="team"
                    class="flex-shrink-0 rounded-lg w-[5rem] h-[5rem] object-cover object-center sm:mb-0 mb-4"
                    src="{{ $announcement->company ? asset('storage/' . $announcement->company->company_image) : asset('storage/empresas/tbn-new-default.webp') }}">
                <div class="flex-grow">
                    <h2 class="text-xl font-bold leading-6 uppercase text-tbn-dark dark:text-white">
                        {{ $announcement->announce_title }}</h2>
                    @if ($announcement->company)
                        <h3 class="inline-block mb-2 font-medium text-md text-tbn-primary">
                            {{ $announcement->company->company_name }}
                        </h3>
                    @else
                        <p class="mb-2 text-sm text-tbn-dark">(Sin empresa)</p>
                    @endif
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="flex flex-col gap-1 mb-2 text-sm font-normal text-tbn-dark">
                            @if ($announcement->locations->count() === $total_locations)
                                <span class="text-tbn-dark dark:text-white"><i
                                        class="pr-1 fas fa-map-marker-alt text-tbn-primary"></i>
                                    Toda Bolivia</span>
                            @else
                                @forelse ($announcement->locations as $location)
                                    <span class="text-tbn-dark dark:text-white"><i
                                            class="pr-1 fas fa-map-marker-alt text-tbn-primary"></i>
                                        {{ $location->location_name }}</span>
                                @empty
                                    <span>Sin ubicación</span>
                                @endforelse
                            @endif
                        </div>
                        <div class="text-sm font-normal text-tbn-dark">
                            <div class="mb-2">
                                <i class="pr-1 fas fa-calendar-alt text-tbn-primary"></i>
                                <span class="text-tbn-dark dark:text-white">
                                    @if ($announcement->expiration_time > Carbon\Carbon::now())
                                        Postula hasta el
                                    @else
                                        Fecha límite
                                    @endif
                                    {{ $this->formatDate($announcement->expiration_time) }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <i class="pr-1 fas fa-calendar-alt text-tbn-primary"></i>
                                <span class="text-tbn-dark dark:text-white"> Publicado
                                    {{ \Carbon\Carbon::parse($announcement->updated_at)->diffForHumans() }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <i class="pr-1 fas fa-money-bill text-tbn-primary"></i>
                                @if ($announcement->salary > 0)
                                    <span class="text-tbn-dark dark:text-white"> Sueldo {{ $announcement->salary }} Bs.
                                    </span>
                                @else
                                    <span class="text-tbn-dark dark:text-white"> Sueldo no declarado por la institución.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="my-3">
                <h3 class="mb-1 text-lg font-medium tbn-special text-tbn-primary">Descripción</h3>
                <div
                    class="text-tbn-dark dark:text-white font-normal [&_ol]:list-disc [&_ol]:ml-4 [&_span]:bg-transparent [&_a]:underline [&_a]:text-tbn-primary [&_h1]:text-2xl [&_h2]:text-xl [&_h3]:text-lg text-sm">
                    {!! $announcement->description !!}
                </div>
            </div>
            <div class="my-4">
                <!-- Save -->
                @if ($client && $client->myAnnounces->contains($announcement->id))
                    <x-button class="w-full my-1 sm:w-auto" wire:click='removeAnnounce({{ $announcement->id }})'>
                        <i class="pr-2 text-sm fas fa-bookmark"></i> Guardado
                    </x-button>
                @else
                    <x-button class="w-full my-1 sm:w-auto" wire:click='saveAnnounce({{ $announcement->id }})'>
                        <i class="pr-2 text-sm far fa-bookmark"></i> Guardar
                    </x-button>
                @endif
                @if ($announcement->announceFiles && count($announcement->announceFiles))
                    <x-button class="w-full my-1 sm:w-auto" wire:click='downloadAnnounceFiles()'>
                        <i class="pr-2 text-sm fas fa-arrow-down"></i> Descargar archivos
                    </x-button>
                @endif
                <!-- Return -->
                <x-secondary-button type="button" onclick="history.back()" class="w-full my-1 sm:w-auto">
                    <i class="pr-2 text-sm fas fa-arrow-left"></i> Volver
                </x-secondary-button>
            </div>
        </div>
    </section>
    <section class="w-full md:w-2/5">
        {{-- Company info --}}
        <div class="mb-4">
            <h3 class="mb-1 font-medium text-tbn-dark dark:text-white text-md">Información de la empresa</h3>
            <div
                class="p-5 bg-white border rounded-lg shadow-md dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary">
                @if ($announcement->company)
                    <picture class="block mb-0 md:mb-2">
                        <img alt="company-logo"
                            class="flex-shrink-0 object-cover object-center w-12 h-12 mb-4 rounded-lg sm:mb-0"
                            src="{{ asset('storage/' . $announcement->company->company_image) }}">
                    </picture>
                    <h5
                        class="inline font-bold mb-2 {{ $announcement->company->trashed() ? 'line-through opacity-40 ' : 'text-tbn-primary' }}">
                        {{ $announcement->company->company_name }}
                    </h5>
                    <span class="px-2 text-xs rounded-lg bg-tbn-light dark:bg-tbn-secondary"> Empresa:
                        {{ $announcement->company->companyType->company_type_name }}</span>
                    <p class="text-sm text-tbn-dark dark:text-white">
                        {{ $announcement->company->description }}</p>
                @else
                    <p class="text-sm italic text-tbn-dark dark:text-tbn-light">La empresa no tiene información
                        disponible</p>
                @endif

            </div>
        </div>
        {{-- Suggest --}}
        <div class="mb-4">
            <h3 class="mb-1 font-medium text-tbn-dark dark:text-white text-md">Convocatorias similares</h3>
            <div class="flex flex-col gap-2">
                @forelse ($suggests as $suggest)
                    <a href="{{ $suggest->pro && (!$client || !$pro_verified) ? route('purchase-cards') : route('result', ['id' => $suggest->id]) }}"
                        wire:navigate wire:key='suggest-{{ $suggest->id }}'>
                        <x-card-announce logo_url="{{ $suggest->company ? $suggest->company->company_image : '' }}"
                            title="{{ $suggest->announce_title }}" pro="{{ $suggest->pro }}"
                            created_at="{{ $suggest->created_at }}" logo_flag="{{ false }}">
                            @if ($suggest->company)
                                <x-slot name="company">{{ $suggest->company->company_name }}</x-slot>
                            @endif
                            <x-slot name="locations">
                                {{ $suggest->locations[0]->location_name }}
                                @if ($suggest->locations->count() > 1)
                                    <span class="text-xs text-gray-400">({{ $suggest->locations->count() - 1 }}
                                        más)</span>
                                @endif
                            </x-slot>
                        </x-card-announce>
                    </a>
                @empty
                    <p class="my-8 text-sm italic text-center text-tbn-dark dark:text-tbn-light">
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
