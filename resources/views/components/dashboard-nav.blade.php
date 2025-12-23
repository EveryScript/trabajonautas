@props([
    'client_name',
    'client_account_type_id',
    'client_account_type_name',
    'client_pro_verified',
    'client_account_expire_days',
])
<aside class="w-full md:w-[18rem] relative">
    <div class="bg-white rounded-xl shadow-lg mb-6 px-6 py-8 transition-all duration-300 hover:shadow-xl">
        <picture class="block relative mb-2">
            <img src="{{ asset('storage/img/tbn-new-isologo.webp') }}" alt="avatar"
                class="w-[3rem] rounded-full mx-auto">
        </picture>
        <div class="text-center">
            <h5 class="text-lg font-medium mb-1"> {{ $client_name }} </h5>
            @if (intval($client_account_type_id) === 1)
                <span class="inline-block mb-4 text-xs text-white bg-green-600 px-2 py-1 rounded-full">
                    <i class="fas fa-leaf mr-2"></i> {{ $client_account_type_name }}
                </span>
            @elseif(intval($client_account_type_id) === 2 || intval($client_account_type_id) === 3)
                <!-- Alert verified client -->
                @if (!$client_pro_verified)
                    <!-- Message: Verify account -->
                    <div class="text-left border border-tbn-primary p-4 rounded-lg mt-6">
                        <p class="font-medium mb-2">
                            Verificación en proceso</p>
                        <p class="inline-block font-medium text-xs mb-3 text-tbn-dark">
                            Envíanos tu <span class="font-bold text-tbn-primary">comprobante de
                                depósito</span> por WhatsApp para HABILITAR tu cuenta hoy mismo.</p>
                        <a class="inline-block px-3 py-2 text-xs rounded text-white bg-tbn-primary border border-tbn-primary hover:bg-gray-50 hover:text-tbn-primary cursor-pointer transition-colors duration-300"
                            target="_blank"
                            href="https://wa.me/59173858162?text=Hola%20Trabajonauas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $client_account_type_name }}%20por%20QR.%20Mi%20nombre%20es%20{{ $client_name }}.">
                            <i class="fab fa-whatsapp mr-1"></i> Enviar mensaje</a>
                    </div>
                @else
                    <span class="inline-block mb-4 text-xs text-white bg-tbn-secondary px-2 py-1 rounded-full">
                        <i class="fas text-xs fa-crown mr-2"></i> {{ $client_account_type_name }}
                    </span>
                    <!-- Alert time Left -->
                    @if ($client_account_expire_days > 5)
                        <p class="text-center text-xs text-gray-900">
                            <i class="fa-solid fa-clock text-tbn-secondary mr-1"></i>
                            Tu cuenta expira en {{ $client_account_expire_days }} dias
                        </p>
                    @else
                        <div class="text-left border border-tbn-primary p-4 rounded-lg mt-1">
                            <p class="font-medium mb-2">Tu cuenta expira en 
                                {{ $client_account_expire_days > 1 ? $client_account_expire_days . ' dias.' : $client_account_expire_days . ' día.' }}
                            </p>
                            <p class="font-medium text-xs mb-3 text-tbn-dark">
                                Renueva tu cuenta <span
                                    class="font-bold text-tbn-primary">{{ $client_account_type_name }}</span> ahora mismo
                                para seguir disfrutando de todos sus beneficios.</p>
                            <a href="{{ route('purchase-account', ['account_type_id' => intval($client_account_type_id)]) }}"
                                class="inline-block px-3 py-2 text-xs rounded text-white bg-tbn-primary border border-tbn-primary hover:bg-gray-50 hover:text-tbn-primary cursor-pointer transition-colors duration-300">
                                <i class="fa-solid fa-arrow-rotate-right mr-1"></i> Renovar cuenta</a>
                        </div>
                    @endif
                @endif
                <!-- Notifications -->
                @if ($client_account_type_id === 3)
                    <hr class="my-4">
                    <div x-show="!aside_error_notifications" class="text-center text-xs">
                        <i class="far fa-check-circle text-green-500 mr-1"></i> Notificaciones activadas
                    </div>
                    <div x-show="aside_error_notifications"
                        class="text-left border border-tbn-primary p-4 rounded-lg mt-1">
                        <p class="font-semibold mb-2">Notificaciones desactivadas</p>
                        <p class="font-medium text-xs mb-3 text-tbn-dark">
                            <span class="font-bold text-tbn-primary">
                                Activa las notificaciones </span> recibir las convocatorias más recientes en tiempo
                            real.
                        </p>
                        <button x-on:click="activateNotificationsAndSaveCurrentToken"
                            class="inline-block px-3 py-2 text-xs rounded text-white bg-tbn-primary border border-tbn-primary hover:bg-gray-50 hover:text-tbn-primary cursor-pointer transition-colors duration-300">
                            <i class="fa-solid fa-bell mr-1"></i> Activar</button>
                    </div>
                @endif
            @endif
            <hr class="my-4">
            <!-- Navigation -->
            <nav class="text-sm select-none">
                <a x-on:click="btnNavigation = 1"
                    class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                    <i class="fas fa-home ml-2 mr-3"></i> Inicio
                </a>
                <a x-on:click="btnNavigation = 2"
                    class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                    <i class="fa-solid fa-bell ml-2 mr-3"></i> Notificaciones
                </a>
                <a x-on:click="btnNavigation = 3"
                    class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                    <i class="fa-solid fa-bookmark ml-2 mr-3"></i> Mis convocatorias
                </a>
                <a x-on:click="btnNavigation = 4"
                    class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                    <i class="fas fa-user ml-2 mr-3"></i> Mi perfil
                </a>
            </nav>
        </div>
</aside>
