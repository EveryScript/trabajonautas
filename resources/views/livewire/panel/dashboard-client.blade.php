<section class="mt-8">
    <div x-data="content" class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8">
        <!-- User Card -->
        <aside class="w-full md:w-[18rem]">
            <div class="bg-white rounded-xl shadow-lg mb-6 px-6 py-8 transition-all duration-300 hover:shadow-xl">
                <picture class="block relative mb-2">
                    <img src="{{ asset('storage/img/tbn-avatar.webp') }}" alt="avatar"
                        class="w-[3rem] rounded-full mx-auto">
                </picture>
                <h5 class="text-lg font-bold text-center mb-1"> {{ $user->name }} </h5>
                <p class="text-sm text-tbn-dark text-center mb-2"> {{ $user->gradeProfile->profile_name }} </p>
                @if ($user->account->account_type_id == 1)
                    <p class="text-center">
                        <span class="text-xs text-white bg-green-500 px-2 py-1 rounded-full">
                            <i class="fas fa-leaf mr-2"></i>FREE</span>
                    </p>
                @else
                    <p class="text-center">
                        <span class="inline-block mb-2 text-xs text-white bg-orange-500 px-2 py-1 rounded-full">
                            <i class="fas fa-crown mr-2"></i>PRO</span>
                        @if (!$user->account->verified_payment)
                            <p class="text-center text-xs text-orange-500">Verificación pendiente</p>
                        @else
                            <p class="text-center text-xs text-gray-900">
                                <i class="far fa-check-circle text-green-500 mr-1"></i>
                                {{ $user->account->days_left }} dias restantes
                            </p>
                        @endif
                    </p>
                @endif
                <hr class="my-4">
                {{-- User navigation --}}
                <a x-on:click="btnNavigation = 1"
                    class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                    <i class="fas fa-home mx-2"></i> Inicio
                </a>
                <a x-on:click="btnNavigation = 2"
                    class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                    <i class="fas fa-bookmark mx-2"></i> Mis convocatorias
                </a>
                <a x-on:click="btnNavigation = 3"
                    class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                    <i class="fas fa-user mx-2"></i> Mi perfil
                </a>
            </div>
        </aside>
        <main class="flex-1">
            <!-- Notice -->
            @role(env('FREE_CLIENT_ROLE'))
                <x-dashboard-ad />
            @else
                @if (!$user->account->verified_payment)
                    <div class="bg-white rounded-xl shadow-lg mb-6 p-5 transition-all duration-300 hover:shadow-xl">
                        <div class="flex flex-row gap-4">
                            <picture class="block">
                                <i class="fas fa-info-circle text-5xl inline mx-auto text-blue-500"></i>
                            </picture>
                            <div class="flex-1">
                                <h5 class="font-medium text-xl mb-2">Verificación pendiente</h5>
                                <p class="text-md text-gray-500">Estamos verificando tu pago de realizado por QR. Si ya
                                    realizaste
                                    tu pago
                                    envíanos un mensaje de Whatsapp para informarnos que hiciste tu depósito.</p>
                                <x-button-link
                                    href="https://api.whatsapp.com/send?phone=59172222222&text=Hola, he realizado el pago de mi cuenta PRO por QR. Mi número de celular es {{ $user->phone }} y mi nombre es {{ $user->name }}."
                                    class="inline-block mt-4"><i class="fab fa-whatsapp mr-1"></i> Enviar
                                    mensaje</x-button-link>
                            </div>
                        </div>
                    </div>
                @endif
            @endrole
            {{-- My suggestions --}}
            <div x-show="btnNavigation == 1">
                <h3 class="text-lg font-medium mb-3">Convocatorias de trabajo para ti</h3>
                <div class="flex flex-col gap-4">
                    @forelse ($suggests as $announcement)
                        <a href="{{ $announcement->pro && (!auth()->check() || !auth()->user()->hasRole(env('PRO_CLIENT_ROLE')))
                            ? route('purchase')
                            : route('result', ['id' => $announcement->id]) }}"
                            wire:navigate>
                            <x-card-announce logo_url="{{ $announcement->company->company_image }}"
                                pro="{{ $announcement->pro }}">
                                <x-slot name="area">{{ $announcement->area->area_name }}</x-slot>
                                <x-slot name="title">{{ $announcement->announce_title }}</x-slot>
                                <x-slot name="company">{{ $announcement->company->company_name }}</x-slot>
                                <x-slot name="locations">
                                    {{ $announcement->locations[0]->location_name }}
                                    @if ($announcement->locations->count() > 1)
                                        <i
                                            class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                                    @endif
                                </x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <x-section-empty class="col-span-2" title="No hay sugerencias disponibles"
                            description="Las sugerencias de convocatorias de trabajo estarán visibles en esta sección.">
                            <x-button-link href="{{ route('search') }}" class="inline-block mt-5" wire:navigate>Buscar
                                convocatorias</x-button-link>
                        </x-section-empty>
                    @endforelse
                </div>
            </div>
            {{-- My saved announces --}}
            <div x-show="btnNavigation == 2">
                <h3 class="text-lg font-medium mb-3">Mis convocatorias</h3>
                <div class="flex flex-col gap-4">
                    @forelse ($user->myAnnounces as $announcement)
                        <a href="{{ $announcement->pro && (!auth()->check() || !auth()->user()->hasRole(env('PRO_CLIENT_ROLE')))
                            ? route('purchase')
                            : route('result', ['id' => $announcement->id]) }}"
                            wire:navigate>
                            <x-card-announce logo_url="{{ $announcement->company->company_image }}"
                                pro="{{ $announcement->pro }}">
                                <x-slot name="area">{{ $announcement->area->area_name }}</x-slot>
                                <x-slot name="title">{{ $announcement->announce_title }}</x-slot>
                                <x-slot name="company">{{ $announcement->company->company_name }}</x-slot>
                                <x-slot name="locations">
                                    {{ $announcement->locations[0]->location_name }}
                                    @if ($announcement->locations->count() > 1)
                                        <i
                                            class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                                    @endif
                                </x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <x-section-empty class="col-span-2" title="No has guardado ninguna convocatoria"
                            description="Las convocatorias guardadas en la sección de busqueda aparecerán aqui.">
                            <x-button-link href="{{ route('search') }}" class="inline-block mt-5" wire:navigate>Iniciar
                                busqueda</x-button-link>
                        </x-section-empty>
                    @endforelse
                </div>
            </div>
            {{-- My Profile --}}
            <div x-show="btnNavigation == 3">
                <h3 class="text-lg font-medium mb-3">Mi perfil</h3>
                <form class="tbn-form" wire:submit="updateUser">
                    <div class="mb-4">
                        <x-label for="name" value="{{ __('Nombre') }}" />
                        <x-input id="name" type="text" class="mt-1 block w-full" placeholder="Jaime Suarez"
                            value="{{ $user->name }}" disabled />
                    </div>
                    <div class="mb-4">
                        <x-label for="location" value="{{ __('Ubicación') }}" />
                        <x-select id="location" wire:model='userForm.location_id'>
                            <option value="">Selecciona tu ubicación</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="userForm.location_id" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-label for="location" value="{{ __('Area profesional') }}" />
                        <x-select id="area" wire:model='userForm.area_id'>
                            <option value="">Selecciona un area</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="userForm.area_id" class="mt-2" />
                    </div>
                    <div class="tbn-field mb-4">
                        @if (!$pro_verified)
                            <i
                                class="inline-block fas fa-crown text-[7px] text-white bg-orange-500 p-1 rounded-full mr-1 -translate-y-1"></i>
                        @endif
                        <x-label class="inline-block mb-1" for="profesions" value="{{ __('Profesiones') }}" />
                        <div class="mb-4" wire:ignore>
                            <x-select id="profesions" wire:model="userForm.profesions" multiple
                                disabled="{{ !$pro_verified ? 'disabled' : '' }}">
                                <option>Seleccionar profesiones</option>
                                @foreach ($profesions as $profesion)
                                    <option class="text-sm h-[1.2rem]" value="{{ $profesion->id }}">
                                        {{ $profesion->profesion_name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>
                        @if (!$pro_verified)
                            <i
                                class="inline-block fas fa-crown text-[7px] text-white bg-orange-500 p-1 rounded-full mr-1 -translate-y-1"></i>
                        @endif
                        <span class="text-xs text-tbn-primary">Notificaciones en tiempo real</span>
                        <div class="px-4 py-3 border bg-white border-tbn-primary rounded-lg mb-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer disabled:cursor-not-allowed"
                                    id="actived" {{ !$pro_verified ? 'disabled' : '' }}>
                                <div
                                    class="relative w-14 h-7 bg-gray-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-tbn-primary">
                                </div>
                                <div class="ms-3">
                                    <p class="inline-block text-md font-medium text-black">Activar notificaciones</p>
                                    <span class="block text-xs text-tbn-dark">El usuario recibe notificaciones de las
                                        convocatorias según su profesión(es).</span>
                                </div>
                            </label>
                        </div>
                        <x-input-error for="userForm.profesions" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-button type="submit">Guardar cambios</x-button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    @assets
        <!-- Tom Select -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <!-- Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            new TomSelect('#profesions', []);
            if ($wire.userForm.profesions.length > 0)
                document.querySelector('#profesions').tomselect.setValue($wire.userForm.profesions);
            Alpine.data('content', () => ({
                btnNavigation: 1,
                btnAd: true,
                init() {
                    $wire.on('user-updated', () => {
                        Swal.fire({
                            title: "Usuario actualizado",
                            icon: "success",
                        });
                    })
                }
            }))
        </script>
    @endscript
</section>
