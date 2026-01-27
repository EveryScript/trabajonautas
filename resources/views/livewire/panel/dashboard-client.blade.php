<section class="mt-8">
    <div x-data="content" class="flex flex-col gap-8 mx-auto max-w-7xl md:flex-row">
        <!-- Navigation -->
        <x-dashboard-nav client_name="{{ $client->name }}"
            client_account_type_id="{{ $client->account->account_type_id }}"
            client_account_type_name="{{ $client->account->accountType->name }}"
            client_pro_verified="{{ $client->account->verified_payment }}"
            client_account_expire_time="{{ $client->account->limit_time }}"
            client_account_expire_days="{{ $client_account_expire_days }}"
            client_account_expired="{{ $client_account_expired }}"
            has_new_notify_announces="{{ $has_new_notify_announces }}" />
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
                    'client_location_id' => $client->location_id,
                    'client_profesion_id' => $client->profesion_id,
                    'client_profesion_area_id' => $client->profesion->area_id,
                ])
            </div>
            <!-- Notifications -->
            <div x-show="btnNavigation == 2">
                @if (intval($client->account->account_type_id) === 1)
                    <div class="text-center">
                        <picture class="w-full mb-2">
                            <img src="{{ asset('storage/img/tbn-notify.webp') }}" alt="rocket"
                                class="w-[14rem] h-[14rem] mx-auto">
                        </picture>
                        <h5 class="mb-1 text-lg font-medium text-tbn-dark dark:text-white">Notificaciones en tiempo real
                        </h5>
                        <p class="max-w-lg mx-auto mb-6 text-sm text-tbn-dark dark:text-tbn-light">
                            Entérate de las mejores convocatorias en cuanto son publicadas en nuestra plataforma.</p>
                        <x-button type="button" class="inlne-block bg-tbn-primary"
                            href="{{ route('purchase-account', ['account_type_id' => 3]) }}" wire:navigate>
                            Obtener PRO-MAX ahora</x-button>
                    </div>
                @else
                    @livewire('panel.dashboard-notify', [
                        'title' => 'Notificaciones de convocatorias',
                        'description' => 'Aquí encontrarás las convocatorias más recientes que coinciden con tu perfil.',
                        'client_location_id' => $client->location_id,
                        'client_profesion_id' => $client->profesion_id,
                    ])
                @endif
            </div>
            <!-- Client -->
            <div x-show="btnNavigation == 3">
                @livewire('panel.dashboard-card', [
                    'title' => 'Mis convocatorias',
                    'description' => 'Aún no has guardado ninguna convocatoria.',
                    'my_announces_mode' => true,
                    'client_account_id' => $client->account->accountType->id,
                    'client_location_id' => $client->location_id,
                    'client_profesion_id' => $client->profesion_id,
                    'client_profesion_area_id' => $client->profesion->area_id,
                ])
            </div>
            <!-- Profile -->
            <div x-show="btnNavigation == 4">
                <header>
                    <h3 class="mb-1 text-lg font-medium text-tbn-dark dark:text-white">Mi perfil</h3>
                </header>
                <div
                    class="p-5 mb-6 transition-all duration-300 bg-white shadow-lg dark:bg-tbn-dark rounded-xl hover:shadow-xl">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="mb-4">
                            <p class="text-xs text-tbn-primary">Nombre del cliente</p>
                            <p class="text-gray-900 dark:text-tbn-light">{{ $client->name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-tbn-primary">Ubicación</p>
                            <p class="text-gray-900 dark:text-tbn-light">{{ $client->location->location_name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-tbn-primary">Celular</p>
                            <p class="text-gray-900 dark:text-tbn-light">
                                {{ substr($client->phone, 4, 10) }}
                            </p>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-tbn-primary">Grado Académico</p>
                            <p class="text-gray-900 dark:text-tbn-light">{{ $client->gradeProfile->profile_name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-tbn-primary">Profesion</p>
                            <p class="text-gray-900 dark:text-tbn-light">{{ $client->profesion->profesion_name }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-tbn-primary">Cuenta</p>
                            <p class="font-semibold text-gray-900 dark:text-tbn-light">
                                {{ $client->account->accountType->name }}</p>
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
                            class="text-lg fas fa-times text-tbn-primary"></i></x-slot>
                    <x-slot name="image">
                        <img src="{{ asset('storage/img/tbn-crown.webp') }}" alt="empty"
                            class="w-[10rem] h-[10rem] mx-auto"></x-slot>
                    <x-slot name="description">
                        Gracias por adquirir tu cuenta con Trabajonautas.com. Envíanos tu
                        <strong class="font-bold text-tbn-primary">comprobante de pago (imagen) </strong>
                        por WhatsApp para habilitar tu cuenta hoy mismo.
                    </x-slot>
                    <x-slot name="buttons">
                        <x-button-link
                            href="https://wa.me/59173858162?text=Hola%20Trabajonautas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $client->account->accountType->name }}%20por%20QR.%20Mi%20nombre%20es%20{{ $client->name }}."
                            target="_blank" class="text-sm cursor-pointer select-none bg-tbn-primary">
                            <i class="mr-1 fab fa-whatsapp"></i> Enviar</x-button-link>
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
                            class="text-lg fas fa-times text-tbn-primary"></i></x-slot>
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
                            class="text-sm cursor-pointer select-none bg-tbn-primary">
                            <span wire:loading.remove><i class="mr-1 fa-solid fa-bell"></i> Activar</span>
                            <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
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
                button_notify_loading: false,
                VAPID_KEY: '{{ config('services.firebase.vapid_key') }}',
                // Functions
                async init() {
                    // Dispatch events: Token Saved
                    this.isNotificationsActived();

                    $wire.on('token-saved', () => {
                        console.info('Current token saved succesfully')
                        this.modal_notifications = false
                        this.aside_error_notifications = false
                        this.button_notify_loading = false
                    })
                },
                async activateNotificationsAndSaveCurrentToken() {
                    this.button_notify_loading = true
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
                    if (Notification.permission === 'granted' && $wire.checkIfTokenExists()) {
                        this.aside_error_notifications = false
                    }
                },
                btnNotification() {
                    this.btnNavigation = 2
                    $wire.updateLastCheck()
                }
            }))
        </script>
    @endscript
</section>
