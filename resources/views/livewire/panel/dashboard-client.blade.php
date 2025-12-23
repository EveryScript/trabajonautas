<section class="mt-8">
    <div x-data="content" class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8">
        <!-- Navigation -->
        <x-dashboard-nav client_name="{{ $client->name }}"
            client_account_type_id="{{ $client->account->accountType->id }}"
            client_account_type_name="{{ $client->account->accountType->name }}"
            client_pro_verified="{{ $client->account->verified_payment }}"
            client_account_expire_days="{{ $client_account_expiration_days }}" />
        <!-- Main content -->
        <main class="flex-1 mb-0 md:mb-24">
            @if ($client->account->accountType->id == 1)
                <x-dashboard-ad />
            @endif
            <!-- Suggests component -->
            <div x-show="btnNavigation == 1">
                @livewire('panel.dashboard-card', [
                    'title' => 'Convocatorias de trabajo para ti',
                    'description' => 'Te presentamos las convocatorias más recientes del país.',
                    'my_announces_mode' => false,
                    'client_account_id' => $client->account->accountType->id,
                    'client_location_id' => $client->location_id,
                    'client_profesion_id' => $client->profesion_id,
                ])
            </div>
            <!-- Notifications -->
            <div x-show="btnNavigation == 2">
                <div class="text-center">
                    <picture class="w-full mb-2">
                        <img src="{{ asset('storage/img/tbn-new-astro.webp') }}" alt="rocket"
                            class="w-[14rem] h-[14rem] mx-auto">
                    </picture>
                    <h5 class="font-medium text-lg mb-1">Notificaciones en tiempo real</h5>
                    <p class="mx-auto max-w-lg text-tbn-dark text-sm mb-6">
                        Entérate de las mejores convocatorias en cuanto son publicadas en nuestra plataforma.</p>
                    <x-button-link class="inlne-block bg-tbn-primary"
                        href="{{ route('purchase-account', ['account_type_id' => 3]) }}" wire:navigate>
                        Obtener PRO-MAX ahora</x-button-link>
                </div>
            </div>
            <!-- Client -->
            <div x-show="btnNavigation == 3">
                @livewire('panel.dashboard-card', [
                    'title' => 'Mis convocatorias',
                    'description' => 'Encuentra todas las convocatorias que guardaste en tu cuenta.',
                    'my_announces_mode' => true,
                    'client_account_id' => $client->account->accountType->id,
                    'client_location_id' => $client->location_id,
                    'client_profesion_id' => $client->profesion_id,
                ])
            </div>
            <!-- Profile -->
            <div x-show="btnNavigation == 4">
                <header>
                    <h3 class="text-lg font-medium mb-1">Mi perfil</h3>
                </header>
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
                            <p class="text-tbn-primary text-xs">Grado Académico</p>
                            <p class="text-gray-900">{{ $client->gradeProfile->profile_name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-tbn-primary text-xs">Profesion</p>
                            <p class="text-gray-900">{{ $client->profesion->profesion_name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-tbn-primary text-xs">Cuenta</p>
                            <p class="text-gray-900 font-semibold">{{ $client->account->accountType->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Modal: Verify account -->
        @if ($client->account->accountType->id >= 2 && !$client->account->verified_payment)
            <div x-show="modal_verify_account" x-cloak>
                <x-dashboard-modal title="Tu cuenta {{ $client->account->accountType->name }} está en camino">
                    <x-slot name="close">
                        <i x-on:click="modal_verify_account = false"
                            class="fas fa-times text-tbn-primary text-lg"></i></x-slot>
                    <x-slot name="image">
                        <img src="{{ asset('storage/img/tbn-notify.webp') }}" alt="empty"
                            class="w-[10rem] h-[10rem] mx-auto"></x-slot>
                    <x-slot name="description">
                        Gracias por adquirir tu cuenta con Trabajonautas.com. Envíanos tu
                        <strong class="font-bold text-tbn-primary">comprobante de pago</strong>
                        por WhatsApp para habilitar tu cuenta hoy mismo.
                    </x-slot>
                    <x-slot name="buttons">
                        <x-button-link
                            href="https://wa.me/59173858162?text=Hola%20Trabajonauas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $client->account->accountType->name }}%20por%20QR.%20Mi%20nombre%20es%20{{ $client->name }}."
                            class="bg-tbn-primary cursor-pointer text-sm select-none">
                            <i class="fab fa-whatsapp mr-1"></i> Enviar</x-button-link>
                    </x-slot>
                </x-dashboard-modal>
            </div>
        @endif
        <!-- Modal: Activate notifications -->
        @if (
            $client->account->accountType->id == 3 &&
                $client->account->verified_payment &&
                empty($client->account->device_token))
            <div x-show="modal_notifications" x-cloak>
                <x-dashboard-modal title="Bienvenido a Trabajonautas {{ $client->account->accountType->name }}">
                    <x-slot name="close">
                        <i x-on:click="modal_notifications = false"
                            class="fas fa-times text-tbn-primary text-lg"></i></x-slot>
                    <x-slot name="image">
                        <img src="{{ asset('storage/img/tbn-notify.webp') }}" alt="empty"
                            class="w-[10rem] h-[10rem] mx-auto">
                    </x-slot>
                    <x-slot name="description">
                        IMPORTANTE: Activa las
                        <strong class="font-bold text-tbn-primary"> notificaciones </strong>
                        para que te enviemos las mejores convocatorias en tiempo real.
                    </x-slot>
                    <x-slot name="buttons">
                        <x-button-link x-on:click="activateNotificationsAndSaveCurrentToken"
                            class="bg-tbn-primary cursor-pointer text-sm select-none">
                            <span wire:loading.remove><i class="fa-solid fa-bell mr-1"></i> Activar</span>
                            <span wire:loading><i class="fas fa-spinner text-sm animate-spin"></i></span>
                        </x-button-link>
                    </x-slot>
                </x-dashboard-modal>
            </div>
        @endif
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                // Propeties
                btnNavigation: 1,
                btnAd: true,
                modal_verify_account: true,
                // Notifications propeties
                modal_notifications: true,
                aside_error_notifications: true,
                VAPID_KEY: @json($VAPID_KEY),
                // Functions
                async init() {
                    // Dispatch events: Token Saved
                    this.isNotificationsActived();

                    $wire.on('token-saved', () => {
                        console.info('Current token saved succesfully')
                        this.modal_notifications = false
                        this.aside_error_notifications = false
                    })
                },
                async activateNotificationsAndSaveCurrentToken() {
                    if (this.isServiceWorkerSupported()) {
                        // Request client activate notifications
                        const permission = await Notification.requestPermission();
                        if (permission !== 'granted') {
                            console.warn('El usuario bloqueó o no aceptó activar las notificaciones.');
                            this.modal_notifications = false
                            return;
                        }
                        // Save current token with Service Worker registration
                        try {
                            console.log('Getting current token...')
                            const registration = await navigator.serviceWorker.register(
                                '/firebase-messaging-sw.js')
                            const currentToken = await messaging.getToken({
                                vapidKey: this.VAPID_KEY,
                                serviceWorkerRegistration: registration,
                            })
                            this.saveCurrentToken(currentToken);
                        } catch (error) {
                            console.error('Error en Firebase Messaging:', error)
                        } finally {
                            this.modal_notifications = false
                        }
                    } else {
                        console.warn('Service Worker no soportado en este navegador')
                        this.modal_notifications = false
                    }
                },
                async saveCurrentToken(currentToken) {
                    if (currentToken) {
                        await $wire.saveClientToken(currentToken);
                    } else {
                        console.warn('No se pudo obtener el token. Revisa los permisos.');
                    }
                },
                // Helpers
                isServiceWorkerSupported() {
                    return ('serviceWorker' in navigator) &&
                        ('PushManager' in window) &&
                        ('Notification' in window);
                },
                isNotificationsActived() {
                    this.aside_error_notifications = Notification.permission === 'granted'
                }
            }))
        </script>
    @endscript
</section>
