<section class="mt-4">
    @if (count($user->myProfesions) == 0 && count($user->myAreas) == 0)
        @livewire('panel.first-steps', ['user_id' => $user->id])
    @else
        <div x-data="content" class="flex flex-row gap-4 items-start">
            <!-- Navigation -->
            <nav class="w-[20rem] max-w-[20rem] min-w-[20rem] bg-white rounded-md shadow-md px-7 py-5">
                <div class="text-md border-b border-gray-400 pb-5">
                    <ul class="text-tbn-primary font-medium">
                        <li class="px-3 py-2 hover:bg-tbn-primary hover:text-white cursor-pointer rounded-lg"
                            @click="navArea = 1" :class="navArea == 1 ? 'bg-gray-200' : ''">
                            Inicio</li>
                        <li class="px-3 py-2 hover:bg-tbn-primary hover:text-white cursor-pointer rounded-lg"
                            @click="navArea = 2" :class="navArea == 2 ? 'bg-gray-200' : ''">
                            Mi perfil</li>
                        <li class="px-3 py-2 hover:bg-tbn-primary hover:text-white cursor-pointer rounded-lg"
                            @click="navArea = 3" :class="navArea == 3 ? 'bg-gray-200' : ''">
                            Mis convocatorias</li>
                    </ul>
                </div>
                <!-- Account type -->
                <div class="mt-5">
                    @role('FREE_CLIENT')
                        <div class="bg-gray-200 rounded-lg px-4 py-3">
                            <span
                                class="inline-block mb-2 bg-green-500 text-white px-2 font-medium text-lg rounded-lg">FREE</span>
                            <p class="text-sm font-medium mb-4">Actualmente estas utilizando una cuenta gratuita. Para
                                acceder a
                                todos los beneficios de Trabajonautas cambiate a PRO ahora.</p>
                            <x-button href="{{ route('purchase') }}" wire:navigate class="w-full">Obtener
                                PRO</x-button>
                        </div>
                    @endrole
                    @role('PRO_CLIENT')
                        <div class="bg-gray-300 rounded-lg px-4 py-3">
                            <span
                                class="inline-block mb-2 bg-orange-500 text-white px-2 font-medium text-lg rounded-lg">PRO</span>
                            <p class="text-sm font-medium">Disfruta de todos los beneficios de Trabajonautas.com con
                                tu cuenta PRO sin límites.</p>
                        </div>
                    @endrole
                </div>

            </nav>
            <!-- Announces for you -->
            <div class="flex-grow bg-white rounded-md shadow-md px-7 py-5" x-show="navArea == 1">
                <h4 class="text-2xl text-tbn-primary font-bold">Hola {{ Auth::user()->name }}</h4>
                <p class="mb-5 text-tbn-dark text-sm">Trabajonautas te da la bienvenida y te muestra las
                    mejores convocatorias de Bolivia adecuadas para tí.
                </p>
                <h4 class="text-lg text-tbn-primary font-bold">Convocatorias de trabajo para tí</h4>
                <div class="grid grid-cols-2 gap-4 mb-5">
                    @forelse ($area_announces as $announcement)
                        <a href="{{ route('result', ['id' => $announcement->id]) }}" wire:navigate>
                            <x-card-announce logo_url="{{ $announcement->company->company_image }}">
                                <x-slot name="area">{{ $announcement->area->area_name }}</x-slot>
                                <x-slot name="title">{{ $announcement->announce_title }}</x-slot>
                                <x-slot name="company">{{ $announcement->company->company_name }}</x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <div class="col-span-2 text-gray-600 w-full text-center py-7">
                            <svg xmlns="http://www.w3.org/2000/svg" class="max-w-12 mx-auto mb-1" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <span class="text-md italic">Aún no has guardado convocatorias</span>
                        </div>
                    @endforelse
                </div>
            </div>
            <!-- My profile -->
            <div class="flex-grow bg-white rounded-md shadow-md px-7 py-5" x-show="navArea == 2">
                <h4 class="text-lg text-tbn-primary font-bold">Mi perfil</h4>
                <p class="mb-5 text-tbn-dark text-sm">Establece tu información para optimizar la busqueda de empleo en
                    Trabajonautas
                </p>
                <div class="border-b border-gray-300 grid grid-cols-2 gap-2">
                    <!-- Full name -->
                    <div class="">
                        <h4 class="text-md text-tbn-dark font-bold">Nombre completo</h4>
                        <p class="text-3xl mb-5">{{ Auth::user()->name }}</p>
                    </div>
                    <!-- My Location -->
                    <div>
                        <h4 class="inline text-md text-tbn-dark font-bold">Mi ubicación</h4>
                        <button @click="locationForm = !locationForm" x-show="!locationForm"
                            class="font-medium text-tbn-primary underline font-sm">Cambiar</button>
                        <p class="text-3xl mb-5" x-show="!locationForm">{{ $user->location->location_name }}</p>
                        <!-- My Location form -->
                        <div class="tbn-form mb-4" x-show="locationForm">
                            <form class="flex flex-row gap-2 tbn-field" wire:submit='saveLocation' wire:ignore>
                                <x-select class="w-full" id="locations" wire:model="location_id">
                                    @forelse ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                                    @empty
                                        <option>No hay ubicaciones disponibles</option>
                                    @endforelse
                                </x-select>
                                <x-button @click="locationForm = !locationForm">Guardar</x-button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- My profesions -->
                <div class="border-b border-gray-300 my-5">
                    <div>
                        <h4 class="inline text-md text-tbn-dark font-bold">Mis profesiones</h4>
                        <button @click="profesionForm = !profesionForm"
                            class="font-medium text-tbn-primary underline font-sm">Añadir</button>
                        <p class="mb-2 text-tbn-dark text-sm">Esta información es util para personalizar los resultados
                            de tu busqueda y ajustarlos a tus necesidades.</p>
                    </div>
                    <!-- Add profesion form -->
                    <div class="tbn-form mb-4" x-show="profesionForm">
                        <form class="flex flex-row gap-2 tbn-field" wire:submit='saveProfesion'>
                            <x-select class="flex-grow" id="areas" wire:model="profesion_id">
                                @forelse ($profesions as $profesion)
                                    <option value="{{ $profesion->id }}">{{ $profesion->profesion_name }}</option>
                                @empty
                                    <option>No hay profesiones disponibles</option>
                                @endforelse
                            </x-select>
                            <x-button @click="profesionForm = !profesionForm">Guardar</x-button>
                        </form>
                    </div>
                    <ul class="grid grid-cols-2 gap-1">
                        @forelse ($user->myProfesions as $profesion)
                            <li class="border border-transparent bg-gray-200 px-5 py-6 rounded-md relative group">
                                <i class="fas fa-graduation-cap text-tbn-primary text-lg pr-2"></i>
                                {{ $profesion->profesion_name }}
                                <button x-on:click='confirmProfesionModal({{ $profesion->id }})'
                                    class="absolute top-0 right-0 bg-gray-300 text-red-500 rounded-tr-md rounded-bl-md p-2 group-hover:block hidden">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </li>
                        @empty
                            <span class="text-tbn-dark italic">No hay profesiones disponibles</span>
                        @endforelse
                    </ul>
                </div>
                <!-- My areas -->
                <div class="border-b border-gray-300 my-5">
                    <div>
                        <h4 class="inline text-md text-tbn-dark font-bold">Areas profesionales</h4>
                        <button @click="areaForm = !areaForm"
                            class="font-medium text-tbn-primary underline font-sm">Añadir</button>
                        <p class="mb-2 text-tbn-dark text-sm">Te ofrecemos la información acerca de convocatorias
                            disponibles de
                            acuerdo con el area profesional de tu interés.</p>
                    </div>
                    <!-- Add area form -->
                    <div class="tbn-form mb-4" x-show="areaForm">
                        <form class="flex flex-row gap-2 tbn-field" wire:submit='saveArea'>
                            <x-select class="flex-grow" id="areas" wire:model="area_id">
                                @forelse ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @empty
                                    <span class="text-tbn-dark italic">No hay areas disponibles</span>
                                @endforelse
                            </x-select>
                            <x-button @click="areaForm = !areaForm">Guardar</x-button>
                        </form>
                    </div>

                    <ul class="grid grid-cols-2 gap-1">
                        @forelse ($user->myAreas as $area)
                            <li class="border border-transparent bg-gray-200 px-5 py-6 rounded-md relative group">
                                <i class="fas fa-suitcase text-lg text-tbn-primary pr-2"></i>
                                {{ $area->area_name }}
                                <button x-on:click="confirmAreaModal({{ $area->id }})"
                                    class="absolute top-0 right-0 bg-gray-300 text-red-500 rounded-tr-md rounded-bl-md p-2 group-hover:block hidden">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </li>
                        @empty
                            <span class="my-5 text-tbn-dark italic">No hay areas seleccionadas</span>
                        @endforelse
                    </ul>
                </div>
            </div>
            <!-- My announces -->
            <div class="flex-grow bg-white rounded-md shadow-md px-7 py-5" x-show="navArea == 3">
                <h4 class="text-lg text-tbn-primary font-bold">Mis convocatorias</h4>
                <p class="mb-5 text-tbn-dark text-sm">Consulta las convocatorias que guardaste en tus busquedas.
                </p>
                <div class="grid grid-cols-2 gap-4 mb-5">
                    @forelse ($user->myAnnounces as $announcement)
                        <a href="{{ route('result', ['id' => $announcement->id]) }}" wire:navigate>
                            <x-card-announce logo_url="{{ $announcement->company->company_image }}">
                                <x-slot name="area">{{ $announcement->area->area_name }}</x-slot>
                                <x-slot name="title">{{ $announcement->announce_title }}</x-slot>
                                <x-slot name="company">{{ $announcement->company->company_name }}</x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <div class="col-span-2 text-gray-600 w-full text-center py-7">
                            <svg xmlns="http://www.w3.org/2000/svg" class="max-w-12 mx-auto mb-1" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <span class="text-md italic">Aún no has guardado convocatorias</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @assets
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        @endassets
        @script
            <script>
                Alpine.data('content', () => ({
                    navArea: 1,
                    profesionForm: false,
                    areaForm: false,
                    locationForm: false,
                    confirmProfesionModal(id) {
                        Swal.fire({
                            title: "¿Eliminar profesion?",
                            text: "¿Estas seguro de eliminar esta Profesion?",
                            showDenyButton: true,
                            confirmButtonText: "Si",
                            denyButtonText: "No"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.deleteProfesion(id)
                            }
                        });
                    },
                    confirmAreaModal(id) {
                        Swal.fire({
                            title: "¿Eliminar area?",
                            text: "¿Estas seguro de eliminar esta Area profesional?",
                            showDenyButton: true,
                            confirmButtonText: "Si",
                            denyButtonText: "No"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.deleteArea(id)
                            }
                        });
                    }
                }))
                new TomSelect('#areas', []);
                new TomSelect('#locations', []);
            </script>
        @endscript
    @endif
</section>
