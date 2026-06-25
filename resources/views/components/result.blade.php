@props(['announcement', 'total_locations', 'client'])

<div
    class="relative bg-transparent sm:bg-white sm:border sm:rounded-lg sm:shadow-md sm:dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary sm:p-10">
    <span class="absolute top-6 right-6 {{ $announcement->pro ? '' : 'hidden' }}">
        <i class="fas fa-crown text-md text-tbn-primary"></i></span>
    <div class="flex flex-col w-full gap-2 sm:flex-row sm:gap-6">
        <img alt="team" class="flex-shrink-0 rounded-lg w-[5rem] h-[5rem] object-cover object-center sm:mb-0 mb-4"
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
            <!-- Data list -->
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
                    @if ($announcement->expiration_time < now())
                        <div class="mb-2">
                            <span class="uppercase text-tbn-primary">
                                <i class="pr-1 fas fa-triangle-exclamation text-tbn-primary"></i>
                                Convocatoria Expirada</span>
                        </div>
                    @endif
                    <div class="mb-2">
                        <i class="pr-1 fas fa-calendar-alt text-tbn-primary"></i>
                        <span class="text-tbn-dark dark:text-white"> Publicado
                            {{ \Carbon\Carbon::parse($announcement->updated_at)->diffForHumans() }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <i class="pr-1 fas fa-money-bill text-tbn-primary"></i>
                        @if ($announcement->salary == 0)
                            <span class="text-tbn-dark dark:text-white">
                                Sueldo no declarado por la institución.</span>
                        @elseif($announcement->salary == 1)
                            <span class="text-tbn-dark dark:text-white">
                                Los sueldos están detallados en la descripción.</span>
                        @else
                            <span class="text-tbn-dark dark:text-white">
                                Sueldo {{ number_format($announcement->salary, 0, '', '.') }} Bs.
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
            class="text-tbn-dark dark:text-white font-normal break-words [&_ol]:list-disc [&_ol]:ml-4 [&_span]:bg-transparent [&_a]:underline [&_a]:text-tbn-primary [&_h1]:text-2xl [&_h2]:text-xl [&_h3]:text-lg text-sm">
            {!! $announcement->description !!}
        </div>
    </div>
    <!-- Announcement files -->
    @if ($announcement->announceFiles && count($announcement->announceFiles))
        <div class="my-3">
            <h3 class="mb-3 text-lg font-medium tbn-special text-tbn-primary">Archivos de la convocatoria
            </h3>
            <div class="flex flex-row flex-wrap gap-2">
                @foreach ($announcement->announceFiles as $announceFile)
                    @php
                        $extension = pathinfo($announceFile->url, PATHINFO_EXTENSION);
                        $icons = [
                            'png' => 'fas fa-file-image',
                            'jpg' => 'fas fa-file-image',
                            'jpeg' => 'fas fa-file-image',
                            'pdf' => 'fas fa-file-pdf',
                            'docx' => 'fas fa-file-word',
                            'doc' => 'fas fa-file-word',
                            'xls' => 'fas fa-file-excel',
                            'xlsx' => 'fas fa-file-excel',
                            'xlsm' => 'fas fa-file-excel',
                            'csv' => 'fas fa-file-excel',
                        ];
                        $icon = $icons[$extension] ?? 'fas fa-file'; // Icono por defecto
                    @endphp

                    <a href="{{ asset('storage/' . $announceFile->url) }}"
                        class="px-4 py-2 text-sm border rounded-lg border-tbn-primary dark:border-tbn-light text-tbn-primary dark:text-tbn-light hover:border-tbn-secondary hover:text-tbn-secondary dark:hover:text-tbn-primary"
                        download="{{ basename($announceFile->original_name) }}">
                        <i class="{{ $icon }} mr-1"></i> Descargar
                    </a>
                @endforeach
            </div>
        </div>
    @endif
    <div class="my-4">
        <!-- Save -->
        @if ($client && $client->myAnnounces->contains($announcement->id))
            <x-button class="w-full my-1 sm:w-auto" wire:click='removeAnnounce({{ $announcement->id }})'>
                <span wire:loading.remove wire:target='removeAnnounce'>
                    <i class="pr-2 text-sm fas fa-bookmark"></i> Guardado</span>
                <span wire:loading wire:target='removeAnnounce'>
                    <i class="text-sm fas fa-spinner animate-spin"></i></span>
            </x-button>
        @else
            <x-button class="w-full my-1 sm:w-auto" wire:click='saveAnnounce({{ $announcement->id }})'>
                <span wire:loading.remove wire:target='saveAnnounce'>
                    <i class="pr-2 text-sm far fa-bookmark"></i> Guardar</span>
                <span wire:loading wire:target='saveAnnounce'>
                    <i class="text-sm fas fa-spinner animate-spin"></i></span>
            </x-button>
        @endif
        <!-- Return -->
        <x-secondary-button type="button" onclick="history.back()" class="w-full my-1 sm:w-auto">
            <i class="pr-2 text-sm fas fa-arrow-left"></i> Volver
        </x-secondary-button>
    </div>
</div>
