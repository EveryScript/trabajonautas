@props([
    'client_name',
    'client_account_type_id',
    'client_account_name',
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
            @if ($client_account_type_id > 1 && !$client_pro_verified)
                <!-- Message: Verify account -->
                <div class="text-left border border-tbn-primary p-4 rounded-lg mt-6">
                    <p class="font-medium mb-2">
                        Verificación en proceso</p>
                    <p class="inline-block font-medium text-xs mb-3 text-tbn-dark">
                        Envíanos tu <span class="font-bold text-tbn-primary underline">comprobante de
                            depósito</span> por WhatsApp para HABILITAR tu cuenta hoy mismo.</p>
                    <a class="inline-block px-3 py-2 text-xs text-white bg-tbn-primary rounded" target="_blank"
                        href="https://wa.me/59173858162?text=Hola%20Trabajonauas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $client_account_name }}%20por%20QR.%20Mi%20nombre%20es%20{{ $client_name }}.">
                        <i class="fab fa-whatsapp mr-1"></i> Enviar mensaje</a>
                </div>
            @else
                <!-- Client type -->
                <span
                    class="inline-block mb-4 text-xs text-white {{ $client_account_type_id == 1 ? 'bg-green-600' : 'bg-tbn-secondary' }} px-2 py-1 rounded-full">
                    @if ($client_account_type_id == 1)
                        <i class="fas fa-leaf mr-2"></i>
                    @else
                        <i class="fas text-xs fa-crown mr-2"></i>
                    @endif
                    {{ $client_account_name }}
                </span>
                @if ($client_account_type_id > 1)
                    <!-- Time Left -->
                    <p class="text-center text-xs text-gray-900">
                        <i class="far fa-check-circle text-green-500 mr-1"></i>
                        Tu cuenta expira en {{ $client_account_expire_days }} dias
                    </p>
                @endif
            @endif
        </div>
        <hr class="my-4">
        <!-- Navigation -->
        <nav class="text-sm select-none">
            <a x-on:click="btnNavigation = 1"
                class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                <i class="fas fa-home mx-2"></i> Inicio
            </a>
            <a x-on:click="btnNavigation = 2"
                class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                <i class="fa-solid fa-bell mx-2"></i> Notificaciones
            </a>
            <a x-on:click="btnNavigation = 3"
                class="flex items-center text-gray-600 hover:text-tbn-primary py-2 transition-all duration-300 hover:translate-x-1 cursor-pointer">
                <i class="fas fa-user mx-2"></i> Mi perfil
            </a>
        </nav>
    </div>
</aside>
