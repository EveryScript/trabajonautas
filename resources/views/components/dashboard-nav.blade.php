@props([
    'client_name',
    'client_account_type_id',
    'client_account_type_name',
    'client_pro_verified',
    'client_account_expire_days',
    'client_account_expired',
])
<aside class="w-full md:w-[18rem] relative">
    <div class="px-6 py-8 mb-6 transition-all duration-300 bg-white shadow-lg dark:bg-tbn-dark dark:text-tbn-light rounded-xl hover:shadow-xl">
        <picture class="relative block mb-2">
            <img src="{{ asset('storage/img/tbn-new-isologo.webp') }}" alt="avatar"
                class="w-[3rem] rounded-full mx-auto">
        </picture>
        <div class="text-center">
            <h5 class="mb-1 text-lg font-medium"> {{ $client_name }} </h5>
            @if (intval($client_account_type_id) === 1)
                <span class="inline-block px-2 py-1 mb-4 text-xs text-white bg-green-600 rounded-full">
                    <i class="mr-2 fas fa-leaf"></i> {{ $client_account_type_name }}
                </span>
                <!-- Alert expired account -->
                @if ($client_account_expired)
                    <div class="p-4 mt-1 text-left border rounded-lg border-tbn-primary">
                        <h5 class="mb-2 font-semibold">Tu cuenta ha expirado.</h5>
                        <p class="mb-3 text-xs font-medium text-tbn-dark dark:text-tbn-light">
                            Renueva tu cuenta ahora mismo para disfrutar de los beneficios de Trabajonautas.com.
                        </p>
                        <a href="{{ route('purchase-account', ['account_type_id' => intval($client_account_type_id)]) }}"
                            class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-primary border-tbn-primary hover:bg-gray-50 hover:text-tbn-primary">
                            <i class="mr-1 fa-solid fa-arrow-rotate-right"></i> Renovar cuenta</a>
                    </div>
                @endif
            @elseif(intval($client_account_type_id) === 2 || intval($client_account_type_id) === 3)
                <!-- Alert verified client -->
                @if (!$client_pro_verified)
                    <!-- Message: Verify account -->
                    <div class="p-4 mt-6 text-left border rounded-lg border-tbn-primary">
                        <p class="mb-2 font-semibold">
                            Verificación en proceso</p>
                        <p class="inline-block mb-3 text-xs font-medium text-tbn-dark dark:text-tbn-light">
                            Envíanos tu <span class="font-bold text-tbn-primary">comprobante de
                                depósito</span> por WhatsApp para HABILITAR tu cuenta hoy mismo.</p>
                        <a class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-primary border-tbn-primary hover:bg-gray-50 hover:text-tbn-primary"
                            target="_blank"
                            href="https://wa.me/59173858162?text=Hola%20Trabajonauas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $client_account_type_name }}%20por%20QR.%20Mi%20nombre%20es%20{{ $client_name }}.">
                            <i class="mr-1 fab fa-whatsapp"></i> Enviar mensaje</a>
                    </div>
                @else
                    <span class="inline-block px-2 py-1 mb-4 text-xs text-white rounded-full bg-tbn-primary">
                        <i class="mr-2 text-xs fas fa-crown"></i> {{ $client_account_type_name }}
                    </span> 
                    <!-- Alert time Left -->
                    @if ($client_account_expire_days > 5)
                        <p class="text-xs text-center text-tbn-dark dark:text-tbn-light">
                            <i class="mr-1 fa-solid fa-clock text-tbn-secondary"></i>
                            Tu cuenta expira en {{ $client_account_expire_days }} dias
                        </p>
                    @else
                        <div class="p-4 mt-1 text-left border rounded-lg border-tbn-primary">
                            <p class="mb-2 font-medium">Tu cuenta expira en
                                {{ $client_account_expire_days > 1 ? $client_account_expire_days . ' dias.' : $client_account_expire_days . ' día.' }}
                            </p>
                            <p class="mb-3 text-xs font-medium text-tbn-dark dark:text-tbn-light">
                                Renueva tu cuenta <span
                                    class="font-bold text-tbn-primary">{{ $client_account_type_name }}</span> ahora
                                mismo
                                para seguir disfrutando de todos sus beneficios.</p>
                            <a href="{{ route('purchase-account', ['account_type_id' => intval($client_account_type_id)]) }}"
                                class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-primary border-tbn-primary hover:bg-gray-50 hover:text-tbn-primary">
                                <i class="mr-1 fa-solid fa-arrow-rotate-right"></i> Renovar cuenta</a>
                        </div>
                    @endif
                @endif
                <!-- Notifications -->
                @if (intval($client_account_type_id) === 3)
                    <div x-show="!aside_error_notifications" class="text-xs text-center text-tbn-dark dark:text-tbn-light">
                        <i class="mr-1 text-green-500 far fa-check-circle"></i> Notificaciones activadas
                    </div>
                    <div x-show="aside_error_notifications"
                        class="p-4 mt-1 text-left border rounded-lg border-tbn-primary">
                        <p class="mb-2 font-semibold">Notificaciones desactivadas</p>
                        <p class="mb-3 text-xs font-medium text-tbn-dark dark:text-tbn-light">
                            <span class="font-bold text-tbn-primary">
                                Activa las notificaciones </span> recibir las convocatorias más recientes en tiempo
                            real.
                        </p>
                        <button x-on:click="activateNotificationsAndSaveCurrentToken"
                            class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-primary border-tbn-primary hover:bg-gray-50 hover:text-tbn-primary">
                            <i class="mr-1 fa-solid fa-bell"></i> Activar</button>
                    </div>
                @endif
            @endif
            <hr class="my-4 fill-tbn-light dark:fill-tbn-secondary">
            <!-- Navigation -->
            <nav class="text-sm select-none">
                <a x-on:click="btnNavigation = 1"
                    class="flex items-center py-2 transition-all duration-300 cursor-pointer text-tbn-secondary dark:text-tbn-light hover:text-tbn-primary hover:translate-x-1">
                    <i class="ml-2 mr-3 fas fa-home"></i> Inicio
                </a>
                <a x-on:click="btnNavigation = 2"
                    class="flex items-center py-2 transition-all duration-300 cursor-pointer text-tbn-secondary dark:text-tbn-light hover:text-tbn-primary hover:translate-x-1">
                    <i class="ml-2 mr-3 fa-solid fa-bell"></i> Notificaciones
                </a>
                <a x-on:click="btnNavigation = 3"
                    class="flex items-center py-2 transition-all duration-300 cursor-pointer text-tbn-secondary dark:text-tbn-light hover:text-tbn-primary hover:translate-x-1">
                    <i class="ml-2 mr-3 fa-solid fa-bookmark"></i> Mis convocatorias
                </a>
                <a x-on:click="btnNavigation = 4"
                    class="flex items-center py-2 transition-all duration-300 cursor-pointer text-tbn-secondary dark:text-tbn-light hover:text-tbn-primary hover:translate-x-1">
                    <i class="ml-2 mr-3 fas fa-user"></i> Mi perfil
                </a>
            </nav>
        </div>
</aside>
