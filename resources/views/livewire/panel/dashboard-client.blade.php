<section class="mt-8">
    <div x-data="content" class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8">
        <!-- Aside -->
        <aside class="w-full md:w-[18rem] relative">
            <div class="bg-white rounded-xl shadow-lg mb-6 px-6 py-8 transition-all duration-300 hover:shadow-xl">
                <picture class="block relative mb-2">
                    <img src="{{ asset('storage/img/tbn-new-isologo.webp') }}" alt="avatar"
                        class="w-[3rem] rounded-full mx-auto">
                </picture>
                <h5 class="text-lg font-medium text-center mb-1"> {{ $client->name }} </h5>
                <!-- Client type -->
                @if ($free_client)
                    <p class="text-center">
                        <span class="text-xs text-white bg-green-500 px-2 py-1 rounded-full">
                            <i class="fas fa-leaf mr-2"></i>FREE</span>
                    </p>
                @else
                    @if (!$pro_verified)
                        <hr class="my-4">
                        <div class="flex flex-row gap-4 border border-tbn-primary p-4 rounded-lg">
                            <i class="inline-block fas fa-rocket text-xl text-tbn-primary"></i>
                            <div class="flex-1 text-left mb-3">
                                <span class="font-medium text-black">
                                    Tu cuenta {{ $client->account->accountType->name }} está en camino</span>
                                <p class="mb-3">
                                    <span class="inline-block font-medium text-xs text-tbn-dark">
                                        Gracias por adquirir tu cuenta con Trabajonautas.com. Envíanos tu
                                        <strong class="font-bold text-tbn-primary">comprobante de pago</strong>
                                        por WhatsApp para habilitar tu cuenta hoy mismo.
                                </p>
                                <x-button-link
                                    href="https://api.whatsapp.com/send?phone=59173858162&text=Hola, he realizado el pago de mi cuenta {{ $client->account->accountType->name }} por QR. Mi nombre es {{ $client->name }}."
                                    class="bg-tbn-primary cursor-pointer text-sm select-none">
                                    <i class="fab fa-whatsapp mr-1"></i> Enviar</x-button-link>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="inline-block mb-4 text-xs text-white bg-tbn-secondary px-2 py-1 rounded-full">
                                <i class="fas text-xs fa-crown mr-2"></i>{{ $client->account->accountType->name }}
                            </p>
                            <!-- Time left alert -->
                            @if ($alert_time_left)
                                <div class="relative max-w-md mx-auto text-left border border-tbn-primary p-4 rounded-lg">
                                    <span class="absolute -top-4 -left-2 bg-white p-2 rounded-full">
                                        <i class="inline-block fas fa-exclamation-triangle text-sm text-tbn-primary"></i>
                                    </span>
                                    <span class="font-medium text-black">Atención</span>
                                    <p class="mb-3">
                                        <span class="inline-block font-medium text-xs text-tbn-dark">
                                            Tu cuenta va a expirar en <strong class="text-tbn-primary">
                                                {{ $time_left }}</strong>.
                                            Renueva tu cuenta para seguir recibiendo las mejores convocatorias.
                                    </p>
                                    <x-button-link
                                        href="{{ route('purchase-account', ['account_type_id' => $client->account->account_type_id]) }}"
                                        class="inline-block bg-tbn-primary cursor-pointer text-sm select-none">
                                        Renovar ahora</x-button-link>
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
                            @if ($notify_token_actived)
                                <div class="absolute top-4 right-4">
                                    <i class="fas fa-bell text-tbn-secondary animate-ping float-right"></i>
                                    <i class="absolute fas fa-bell text-tbn-secondary"></i>
                                </div>
                            @else
                                <!-- Modal activate notifications -->
                                <div x-show="notificationDisplay" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
                                    style="backdrop-filter: blur(2px);">
                                    <div
                                        class="bg-white rounded-xl shadow-lg p-8 mx-2 max-w-md w-full text-center relative">
                                        <button x-on:click="closeNotificationModal" class="absolute top-4 right-5"
                                            type="button">
                                            <i class="fas fa-times text-tbn-primary text-lg"></i></button>
                                        <picture class="block mb-4">
                                            <img src="{{ asset('storage/img/tbn-notify.webp') }}" alt="empty"
                                                class="w-[10rem] h-[10rem] mx-auto">
                                        </picture>
                                        <h2 class="text-lg font-bold mb-2 text-tbn-primary">
                                            Trabajonautas <span class="text-tbn-primary font-bold">PRO-MAX</span>
                                        </h2>
                                        <p class="mb-6 text-tbn-dark text-sm">
                                            <strong class="uppercase">Importante: </strong>
                                            Activa las notificaciones de tu navegador para recibir una notificación en
                                            tiempo real cada vez que
                                            <strong>trabajonautas.com</strong> publique una
                                            nueva convocatoria adecuada para ti.
                                        </p>
                                        <template x-if="!loading">
                                            <x-button-link x-on:click="registerAndGetTokenFirebase"
                                                class="bg-tbn-primary cursor-pointer text-sm select-none w-full">
                                                Activar notificaciones
                                            </x-button-link>
                                        </template>
                                        <template x-if="loading">
                                            <i class="text-tbn-dark fas fa-spinner text-xl animate-spin"></i>
                                        </template>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                @endif
                <hr class="my-4">
                <!-- User navigation -->
                <nav class="text-sm select-none">
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
        <!-- User dashboard -->
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
                        <a href="{{ $this->isAnnouncePro($announce->pro) ? route('result', ['id' => $announce->id]) : route('purchase-cards') }}"
                            wire:navigate wire:key='announce-{{ $announce->id }}'>
                            <x-card-announce
                                logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                                area="{{ $announce->area ? $announce->area->area_name : '(sin area)' }}"
                                title="{{ $announce->announce_title }}" pro="{{ $announce->pro }}">
                                @if ($announce->company)
                                    <x-slot name="company">{{ $announce->company->company_name }}</x-slot>
                                @endif
                                <x-slot name="locations">
                                    {{ $announce->locations[0]->location_name }}
                                    @if ($announce->locations->count() > 1)
                                        <span class="text-xs text-gray-400">
                                            ({{ $announce->locations->count() - 1 }} más)
                                        </span>
                                    @endif
                                </x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <x-section-empty class="col-span-2" title="No hay sugerencias disponibles"
                            description="Las sugerencias de convocatorias de trabajo estarán visibles en esta sección.">
                            <x-button-link href="{{ route('search') }}" class="bg-tbn-primary inline-block mt-5"
                                wire:navigate>
                                Buscar convocatorias</x-button-link>
                        </x-section-empty>
                    @endforelse
                </div>
            </div>
            {{-- My saved announces --}}
            <div x-show="btnNavigation == 2">
                <h3 class="text-lg font-medium mb-3">Mis convocatorias</h3>
                <div class="flex flex-col gap-4">
                    @forelse ($client->myAnnounces as $announce)
                        <a href="{{ $this->isAnnouncePro($announce->pro) ? route('result', ['id' => $announce->id]) : route('purchase-cards') }}"
                            wire:navigate wire:key='announce-{{ $announce->id }}'>
                            <x-card-announce
                                logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                                area="{{ $announce->area ? $announce->area->area_name : '(sin area)' }}"
                                title="{{ $announce->announce_title }}" pro="{{ $announce->pro }}">
                                @if ($announce->company)
                                    <x-slot name="company">{{ $announce->company->company_name }}</x-slot>
                                @endif
                                <x-slot name="locations">
                                    {{ $announce->locations[0]->location_name }}
                                    @if ($announce->locations->count() > 1)
                                        <span class="text-xs text-gray-400">
                                            ({{ $announce->locations->count() - 1 }} más)
                                        </span>
                                    @endif
                                </x-slot>
                            </x-card-announce>
                        </a>
                    @empty
                        <x-section-empty class="col-span-2" title="No has guardado ninguna convocatoria"
                            description="Las convocatorias guardadas en la sección de busqueda aparecerán aqui.">
                            <x-button-link href="{{ route('search') }}" class="bg-tbn-primary inline-block mt-5"
                                wire:navigate>
                                Iniciar busqueda</x-button-link>
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
                notificationDisplay: false,
                init() {
                    this.loading = true
                    // Show allow notification panel
                    if (Notification.permission === 'granted') {
                        $wire.verifyHasToken().then(() => this.loading = false)
                    } else {
                        this.notificationDisplay = true
                        this.loading = false
                    }
                    // Function token saved
                    $wire.on('token-saved', () => {
                        this.notificationDisplay = false
                        this.loading = false
                    })
                    // Function empty token
                    $wire.on('empty-token', () => {
                        this.notificationDisplay = true
                        this.loading = false
                    })
                },
                registerAndGetTokenFirebase() {
                    this.loading = true
                    try {
                        if ('serviceWorker' in navigator) {
                            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                                .then(function(registration) {
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
                                        console.error('Error al obtener el token:', err);
                                    });
                                });
                        }
                    } catch (error) {
                        console.log(error);
                    }
                },
                closeNotificationModal() {
                    this.notificationDisplay = false
                }
            }))
        </script>
    @endscript
</section>
