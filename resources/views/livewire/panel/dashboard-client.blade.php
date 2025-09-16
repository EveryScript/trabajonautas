<section class="mt-8">
    <div x-data="content" class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8">
        <!-- User Card -->
        <aside class="w-full md:w-[18rem]">
            <div class="bg-white rounded-xl shadow-lg mb-6 px-6 py-8 transition-all duration-300 hover:shadow-xl">
                <picture class="block relative mb-2">
                    <img src="{{ asset('storage/img/tbn-isologo.webp') }}" alt="avatar"
                        class="w-[3rem] rounded-full mx-auto">
                </picture>
                <h5 class="text-lg font-medium text-center mb-1"> {{ $client->name }} </h5>
                @if ($free_client)
                    <p class="text-center">
                        <span class="text-xs text-white bg-green-500 px-2 py-1 rounded-full">
                            <i class="fas fa-leaf mr-2"></i>FREE</span>
                    </p>
                @else
                    @if (!$pro_verified)
                        <hr class="my-4">
                        <div class="bg-gray-200 p-4 rounded-md text-left">
                            <span class="inline-block font-bold text-md text-tbn-primary">
                                Tu cuenta {{ $client->account->accountType->name }} está en camino </span>
                            <span class="inline-block font-medium text-xs text-tbn-dark">
                                Gracias por adquirir tu cuenta con Trabajonautas.com. Envíanos tu <strong>comprobante de
                                    pago</strong> por WhatsApp para habilitar tu cuenta hoy mismo.</span>
                            <x-button-link
                                href="https://api.whatsapp.com/send?phone=59172222222&text=Hola, he realizado el pago de mi cuenta PRO por QR. Mi número de celular es {{ $client->phone }} y mi nombre es {{ $client->name }}."
                                class="bg-tbn-primary inline-block text-sm mt-2">
                                <i class="fab fa-whatsapp mr-1"></i> Enviar mensaje</x-button-link>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="inline-block mb-4 text-xs text-white bg-tbn-secondary px-2 py-1 rounded-full">
                                <i class="fas fa-crown mr-2"></i>{{ $client->account->accountType->name }}
                            </p>
                            @if ($alert_time_left)
                                <div class="flex flex-row gap-4 border border-tbn-primary p-4 rounded-lg">
                                    <i class="inline-block fas fa-exclamation-triangle text-xl text-red-600"></i>
                                    <div class="flex-1 text-left">
                                        <span class="font-medium text-black">Atención</span>
                                        <p class="mb-3">
                                            <span class="inline-block font-medium text-xs text-tbn-dark">
                                                Tu cuenta va a expirar en <strong class="text-tbn-primary">
                                                    {{ $time_left }}</strong>.
                                                Renueva tu cuenta para seguir recibiendo las mejores convocatorias.
                                        </p>
                                        <x-button-link
                                            href="{{ route('purchase-account', ['account_type_id' => $client->account->account_type_id]) }}"
                                            class="bg-tbn-primary cursor-pointer text-sm select-none">
                                            Renovar ahora</x-button-link>
                                    </div>
                                </div>
                            @else
                                <p class="text-center text-xs text-gray-900">
                                    <i class="far fa-check-circle text-green-500 mr-1"></i>
                                    Tu cuenta expira en {{ $time_left }}
                                </p>
                            @endif
                        </div>


                        {{-- Verify if notifications is actived --}}
                        @if ($client->account->account_type_id === 3)
                            <hr class="my-4">
                            @if ($notify_token_actived)
                                <div class="flex flex-row gap-4 border border-tbn-secondary p-4 rounded-lg">
                                    <i class="inline-block fas fa-check-circle text-xl text-tbn-secondary"></i>
                                    <div class="flex-1">
                                        <p class="mb-1"><span class="font-medium text-black">
                                                Notificaciones activadas</span></p>
                                        <p class="mb-1"><span class="inline-block font-medium text-xs text-tbn-dark">
                                                Recibirás una notificación cuando una nueva convocatoria esteé
                                                disponible para ti.</span></p>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-row gap-4 border border-tbn-primary p-4 rounded-lg">
                                    <div>
                                        <template x-if="!loading">
                                            <i class="fas fa-lightbulb text-xl text-tbn-primary"></i>
                                        </template>
                                        <template x-if="loading">
                                            <i class="text-tbn-dark fas fa-spinner text-xl animate-spin"></i>
                                        </template>
                                    </div>
                                    <div class="flex-1 mb-2">
                                        <p class="mb-1">
                                            <template x-if="!loading">
                                                <span class="font-medium text-black">Notificaciones desactivadas</span>
                                            </template>
                                            <template x-if="loading">
                                                <span class="font-medium text-tbn-dark">Cargando...</span>
                                            </template>
                                        </p>
                                        <p class="mb-3">
                                            <span class="inline-block font-medium text-xs text-tbn-dark">
                                                Activa las notificaciones para recibir alertas cada vez que
                                                <strong>Trabajonautas.com</strong>
                                                publique una nueva convocatoria.</span>
                                        </p>
                                        <x-button-link x-on:click="registerAndGetTokenFirebase"
                                            class="bg-tbn-primary cursor-pointer text-sm select-none">
                                            Activar</x-button-link>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                @endif
                <hr class="my-4">
                {{-- User navigation --}}
                <nav class="text-sm">
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
                </nav>
            </div>
        </aside>
        <main class="flex-1 mb-0 md:mb-24">
            @if ($free_client)
                <x-dashboard-ad />
            @endif
            {{-- My suggestions --}}
            <div x-show="btnNavigation == 1">
                <h3 class="text-lg font-medium mb-1">Convocatorias de trabajo para ti</h3>
                @if ($free_client)
                    <div class="flex flex-wrap flex-row gap-6 mb-4 bg-white px-4 py-3 rounded-lg">
                        <p class="text-tbn-dark text-xs">
                            <i class="inline fas fa-map-marker-alt text-tbn-primary pr-1"></i>
                            {{ $client->location->location_name }}
                        </p>
                    </div>
                @else
                    <div class="flex flex-wrap flex-row gap-6 mb-4 bg-white px-4 py-3 rounded-lg">
                        <p class="text-tbn-dark text-xs">
                            <i class="inline fas fa-map-marker-alt text-tbn-primary pr-1"></i>
                            {{ $client->location->location_name }}
                        </p>
                        <p class="text-tbn-dark text-xs">
                            <i class="inline fas fa-suitcase text-tbn-primary pr-1"></i>
                            {{ $client->area->area_name }}
                        </p>
                    </div>
                @endif
                <div class="flex flex-col gap-4">
                    @forelse ($suggests as $announce)
                        <a href="{{ $announce->pro && (!$client || !$pro_verified) ? route('purchase-cards') : route('result', ['id' => $announce->id]) }}"
                            wire:navigate wire:key='announce-{{ $announce->id }}'>
                            <x-card-announce logo_url="{{ $announce->company->company_image }}"
                                pro="{{ $announce->pro }}">
                                <x-slot name="area">
                                    {{ $announce->area->area_name }}
                                    @foreach ($announce->profesions as $profesion)
                                        {{ '| ' . $profesion->profesion_name }}
                                    @endforeach
                                </x-slot>
                                <x-slot name="title">{{ $announce->announce_title }}</x-slot>
                                <x-slot name="company">{{ $announce->company->company_name }}</x-slot>
                                <x-slot name="locations">
                                    {{ $announce->locations[0]->location_name }}
                                    @if ($announce->locations->count() > 1)
                                        <i
                                            class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                                    @endif
                                </x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <x-section-empty class="col-span-2" title="No hay sugerencias disponibles"
                            description="Las sugerencias de convocatorias de trabajo estarán visibles en esta sección.">
                            <x-button-link href="{{ route('search') }}" class="bg-tbn-primary inline-block mt-5"
                                wire:navigate>Buscar
                                convocatorias</x-button-link>
                        </x-section-empty>
                    @endforelse
                </div>
            </div>
            {{-- My saved announces --}}
            <div x-show="btnNavigation == 2">
                <h3 class="text-lg font-medium mb-3">Mis convocatorias</h3>
                <div class="flex flex-col gap-4">
                    @forelse ($client->myAnnounces as $announce)
                        <a href="{{ $announce->pro && (!$client || !$pro_verified) ? route('purchase-cards') : route('result', ['id' => $announce->id]) }}"
                            wire:navigate wire:key='announce-{{ $announce->id }}'>
                            <x-card-announce logo_url="{{ $announce->company->company_image }}"
                                pro="{{ $announce->pro }}">
                                <x-slot name="area">
                                    {{ $announce->area->area_name }}
                                    @foreach ($announce->profesions as $profesion)
                                        {{ '| ' . $profesion->profesion_name }}
                                    @endforeach
                                </x-slot>
                                <x-slot name="title">{{ $announce->announce_title }}</x-slot>
                                <x-slot name="company">{{ $announce->company->company_name }}</x-slot>
                                <x-slot name="locations">
                                    {{ $announce->locations[0]->location_name }}
                                    @if ($announce->locations->count() > 1)
                                        <i
                                            class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                                    @endif
                                </x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <x-section-empty class="col-span-2" title="No has guardado ninguna convocatoria"
                            description="Las convocatorias guardadas en la sección de busqueda aparecerán aqui.">
                            <x-button-link href="{{ route('search') }}" class="bg-tbn-primary inline-block mt-5"
                                wire:navigate>Iniciar
                                busqueda</x-button-link>
                        </x-section-empty>
                    @endforelse
                </div>
            </div>
            {{-- My Profile --}}
            <div x-show="btnNavigation == 3">
                <h3 class="text-lg font-medium mb-3">Mi perfil</h3>
                <div class="bg-white rounded-xl shadow-lg mb-6 p-5 transition-all duration-300 hover:shadow-xl">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <p class="text-tbn-primary text-xs">Nombre del cliente</p>
                            <p class="text-gray-900">{{ $client->name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-tbn-primary text-xs">Ubicación</p>
                            <p class="text-gray-900">{{ $client->location->location_name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-tbn-primary text-xs">Celular</p>
                            <p class="text-gray-900">{{ $client->phone }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-tbn-primary text-xs">Area profesional</p>
                            <p class="text-gray-900">{{ $client->area->area_name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-tbn-primary text-xs">Profesiones</p>
                            @if ($free_client)
                                <span class="text-xs text-tbn-dark"><i
                                        class="text-tbn-secondary fas fa-crown mr-1"></i> Adquiere
                                    Trabajonautas PRO o PRO-MAX <a class="text-tbn-primary underline"
                                        href="{{ route('purchase-cards') }}">aqui</a></span>.
                            @else
                                <ul class="list-disc text-gray-900">
                                    @forelse ($client->myProfesions as $profesion)
                                        <li class="ml-4">{{ $profesion->profesion_name }}</li>
                                    @empty
                                        <span class="font-italic">No hay profesiones registradas</span>
                                    @endforelse
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @script
        <script>
            Alpine.data('content', () => ({
                btnNavigation: 1,
                btnAd: true,
                VAPID_KEY: 'BMNaBtUsrS1ops3vvcRqJXlNZ3cdxTlKHMG77c7Y6C1rfe12l5G75AhJYIthEoWJREdwZBGtisKHILPRTok46vU',
                loading: false,
                init() {
                    this.loading = true
                    if (Notification.permission === 'granted')
                        $wire.verifyHasToken().then(() => this.loading = false)
                    else this.loading = false
                    $wire.on('token-saved', () => {
                        this.loading = false
                    })
                },
                registerAndGetTokenFirebase() {
                    this.loading = true
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.register('/firebase-messaging-sw.js')
                            .then(function(registration) {
                                // console.log('Service Worker registrado:', registration);
                                messaging.getToken({
                                    vapidKey: this.VAPID_KEY,
                                    serviceWorkerRegistration: registration
                                }).then((currentToken) => {
                                    if (currentToken)
                                        $wire.saveClientToken(currentToken)
                                    else
                                        this.loading = false
                                }).catch((err) => {
                                    this.loading = false
                                    // console.error('Error al obtener el token:', err);
                                });
                            });
                    }
                }
            }))
        </script>
    @endscript
</section>
