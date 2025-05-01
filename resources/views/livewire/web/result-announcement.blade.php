<div class="flex flex-col md:flex-row gap-6 px-4">
    <!-- Result description -->
    <section class="relative w-full md:w-3/5 bg-white rounded-lg shadow-md p-10" x-data="content">
        <span class="absolute top-6 right-6 {{ $announcement->pro ? '' : 'hidden' }}">
            <i class="fas fa-crown text-md text-orange-500"></i></span>
        <div class=" w-full flex sm:flex-row flex-col gap-6">
            <img alt="team" class="flex-shrink-0 rounded-lg w-[5rem] h-[5rem] object-cover object-center sm:mb-0 mb-4"
                src="{{ asset('storage/' . $announcement->company->company_image) }}">
            <div class="flex-grow">
                <h2 class="text-xl font-bold uppercase leading-6">{{ $announcement->announce_title }}</h2>
                <h3 class="inline-block text-md font-medium mb-2 text-tbn-primary">
                    {{ $announcement->company->company_name }}
                </h3>
                <div class="grid grid-cols-2">
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
                                    Expira
                                @else
                                    Expiró
                                @endif
                                {{ (new Carbon\Carbon($announcement->expiration_time))->diffForHumans() }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-calendar-alt text-red-500 pr-1"></i>
                            <span class=""> Publicado
                                {{ (new Carbon\Carbon($announcement->updated_at))->diffForHumans() }}
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
        <div class="my-5">
            <h3 class="text-lg font-medium mb-1 tbn-special text-tbn-primary">Descripción</h3>
            <div
                class="font-normal [&_ol]:list-disc [&_ol]:ml-4 [&_span]:bg-transparent [&_a]:underline [&_h1]:text-2xl [&_h2]:text-xl [&_h3]:text-lg text-sm">
                {!! $announcement->description !!}
            </div>
        </div>
        <div class="mb-4">
            <!-- Save -->
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
            <x-secondary-button @click="modalShare = !modalShare">
                <i class="fas fa-share-alt pr-2"></i> Compartir
            </x-secondary-button>
        </div>
        {{-- Modal Share --}}
        <div class="fixed z-10 inset-0 overflow-y-auto" id="modal-share" x-show="modalShare" x-cloak x-transition.fade>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-60"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-8 py-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline" @click.away="modalShare = false">
                    <x-application-logo class="mb-2 text-center" />
                    <p class="text-tbn-dark text-sm mb-4">
                        Comparte esta convocatoria con tus amigos. Haz click en los enlaces acontinuación.</p>
                    <div class="flex flex-row gap-4">
                        <picture class="block mb-0 md:mb-2">
                            <img alt="company-logo"
                                class="flex-shrink-0 rounded-lg w-[5rem] h-[5rem] object-cover object-center sm:mb-0 mb-4"
                                src="{{ asset('storage/' . $announcement->company->company_image) }}">
                        </picture>
                        <div>
                            <span class="text-xs font-normal text-gray-800">{{ $announcement->area->area_name }}</span>
                            <h4 class="font-bold text-lg leading-6 my-1">{{ $announcement->announce_title }}</h4>
                            <h4 class="text-tbn-primary font-bold">{{ $announcement->company->company_name }}</h4>
                            <div class="mt-2 [&_a]:text-4xl [&_a]:text-tbn-dark [&_a]:pr-2">
                                {!! $share_buttons !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Suggest -->
    <section class="w-full md:w-2/5">
        <div class="mb-4">
            <h3 class="text-tbn-light text-md font-medium mb-1">Información de la empresa</h3>
            <div class="bg-white rounded-lg shadow-md p-5">
                <picture class="block mb-0 md:mb-2">
                    <img alt="company-logo"
                        class="flex-shrink-0 rounded-lg w-12 h-12 object-cover object-center sm:mb-0 mb-4"
                        src="{{ asset('storage/' . $announcement->company->company_image) }}">
                </picture>
                <h5 class="inline font-medium mb-2 text-tbn-primary">{{ $announcement->company->company_name }}</h5>
                <span class="bg-gray-200 px-2 rounded-lg text-xs"> Empresa:
                    {{ $announcement->company->company_type->company_type_name }}</span>
                <p class="text-tbn-dark text-sm">{{ $announcement->company->description }}</p>
            </div>
        </div>
        <div class="mb-4">
            <h3 class="text-tbn-light text-md font-medium mb-1">Convocatorias similares</h3>
            <div class="flex flex-col gap-2">
                @forelse ($suggests as $suggest)
                    <a href="{{ $suggest->pro && (!auth()->check() || !auth()->user()->hasRole(env('PRO_CLIENT_ROLE')))
                        ? route('purchase')
                        : route('result', ['id' => $suggest->id]) }}"
                        wire:navigate>
                        <x-card-announce logo_url="{{ $suggest->company->company_image }}" pro="{{ $suggest->pro }}">
                            <x-slot name="area">{{ $suggest->area->area_name }}</x-slot>
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
                    No hay sugerencias
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
