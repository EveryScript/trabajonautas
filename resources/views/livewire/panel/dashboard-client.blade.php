<section class="mt-8">
    <div x-data="content" class="flex flex-col gap-8 mx-auto max-w-7xl md:flex-row">
        <!-- Navigation -->
        <x-dashboard-nav :client="$client" :has_new_announces="$this->hasNewAnnounces" />
        <!-- Main content -->
        <main class="flex-1 mb-0 md:mb-24">
            @if ($client->account && intval($client->account->account_type_id) === 1 && !$client->latestPendingSubscription)
                <x-dashboard-ad />
            @endif
            <!-- Suggests component -->
            <div x-show="btnNavigation == 1">
                @livewire('panel.dashboard-card', [
                    'title' => 'Convocatorias de trabajo para ti',
                    'description' => 'Te presentamos las convocatorias más recientes del país.',
                    'client' => $client,
                    'my_announces_mode' => false,
                ])
            </div>
            <!-- Client Announces Saved -->
            <div x-show="btnNavigation == 2">
                @livewire('panel.dashboard-card', [
                    'title' => 'Mis convocatorias',
                    'description' => 'Encuentra las convocatorias que guardaste hasta ahora.',
                    'client' => $client,
                    'my_announces_mode' => true,
                ])
            </div>
        </main>
        <!-- Modal: Verifing account -->
        @if ($client->latestPendingSubscription)
            <div x-show="modal_verify_account" x-cloak>
                <x-dashboard-modal
                    title="Tu cuenta {{ $client->latestPendingSubscription->type->name }} está en camino">
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
                            href="https://wa.me/{{ env('SUPPORT_PHONE') }}?text=Hola%20Trabajonautas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $client->latestPendingSubscription->type->name }}%20por%20QR.%20Mi%20nombre%20es%20{{ $client->name }}."
                            target="_blank" class="text-sm cursor-pointer select-none bg-tbn-primary">
                            <i class="mr-1 fab fa-whatsapp"></i> Enviar</x-button-link>
                    </x-slot>
                </x-dashboard-modal>
            </div>
        @endif
        <!-- Modal: Activate notifications -->
        @if ($client->account && intval($client->account->account_type_id) === 3 && empty($client->account->device_token))
            <div x-show="modal_notifications" x-cloak>
                <x-dashboard-modal title="Bienvenido a Trabajonautas {{ $client->account->type->name }}">
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
                }
            }))
        </script>
    @endscript
</section>
