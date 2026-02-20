@props([
    'client' => null,
    'has_new_announces' => false,
])

<aside class="w-full md:w-[18rem] relative">
    <div
        class="px-6 py-8 transition-all duration-300 bg-white divide-y shadow-lg dark:bg-tbn-dark dark:text-tbn-light rounded-xl hover:shadow-xl divide-tbn-light dark:divide-tbn-secondary">
        <div class="mb-2 text-center">
            <!-- User name and Account -->
            <picture class="relative block mb-2">
                <img src="{{ asset('storage/img/tbn-new-isologo.webp') }}" alt="avatar"
                    class="block dark:hidden w-[3rem] rounded-full mx-auto">
                <img src="{{ asset('storage/img/tbn-white-isologo.webp') }}" alt="avatar"
                    class="hidden dark:block w-[3rem] rounded-full mx-auto">
            </picture>
            <h5 class="text-lg font-medium"> {{ $client->name }} </h5>
            <!-- Account Type or Verifing subscription -->
            @if ($client->latestPendingSubscription)
                <div class="p-4 my-4 text-left border rounded-lg border-tbn-primary">
                    <p class="mb-2 font-semibold">
                        Verificación en proceso</p>
                    <p class="inline-block mb-3 text-xs font-normal text-tbn-dark dark:text-tbn-light">
                        Envíanos tu <span class="font-bold text-tbn-primary">comprobante de
                            depósito</span> para verificar que realizaste el pago por tu cuenta <span
                            class="font-medium text-tbn-primary">{{ $client->latestPendingSubscription->type->name }}</span>.
                    </p>
                    <a class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-primary border-tbn-primary hover:bg-transparent hover:text-tbn-primary"
                        target="_blank"
                        href="https://wa.me/{{ env('SUPPORT_PHONE') }}?text=Hola%20Trabajonautas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $client->latestPendingSubscription->type->name }}%20por%20QR.%20Mi%20nombre%20es%20{{ $client->name }}.">
                        <i class="mr-1 fab fa-whatsapp"></i> Enviar mensaje</a>
                </div>
            @else
                <span
                    class="inline-block px-2 py-1 my-2 text-xs text-white {{ $client->account->account_type_id == 1 ? 'bg-green-600' : 'bg-tbn-primary' }} rounded-full tracking-wider">
                    <i class="mr-1 fas {{ $client->account->account_type_id == 1 ? 'fa-leaf' : 'fa-crown' }}"></i>
                    {{ $client->account->type->name }}
                </span>
            @endif
            <!-- Time left -->
            @if ($client->account && intval($client->account->account_type_id) > 1)
                @php
                    $expire_days = Carbon\Carbon::parse($client->account->limit_time)->diffInDays(Carbon\Carbon::now());
                @endphp
                @if ($expire_days > 5)
                    <div class="my-4 text-xs text-tbn-dark dark:text-tbn-light">
                        <i class="mr-1 fa-solid fa-clock text-tbn-secondary"></i>
                        Tu cuenta expira en <span class="relative cursor-pointer text-tbn-primary group">
                            {{ $expire_days }} días.
                            <span
                                class="absolute z-20 px-3 py-2 mb-2 text-xs text-white transition-opacity -translate-x-1/2 rounded-lg opacity-0 pointer-events-none bg-tbn-secondary dark:bg-neutral-900 bottom-full left-1/2 w-max whitespace-nowrap group-hover:opacity-100">
                                Fecha de expiración: {{ date('d/M/Y H:i', strtotime($client->account->limit_time)) }}
                            </span>
                        </span>
                    </div>
                @else
                    <div class="p-4 my-4 text-left border rounded-lg border-tbn-primary">
                        <p class="mb-2 font-medium">Tu cuenta expira en
                            {{ $expire_days > 1 ? $expire_days . ' dias.' : $expire_days . ' día.' }}
                        </p>
                        <p class="mb-3 text-xs font-medium text-tbn-dark dark:text-tbn-light">
                            Renueva tu cuenta ahora mismo antes de que expire para no perder los beneficios de
                            <span class="font-bold text-tbn-primary">Trabajonautas.com</span>
                        </p>
                        <a href="{{ route('purchase-account', ['account_type_id' => intval($client->account->account_type_id)]) }}"
                            class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-primary border-tbn-primary hover:bg-transparent hover:text-tbn-primary">
                            <i class="mr-1 fa-solid fa-arrow-rotate-right"></i> Renovar cuenta</a>
                    </div>
                @endif
            @endif
        </div>
        <!-- Notifications activated -->
        @if ($client->account && intval($client->account->account_type_id) === 3)
            <div class="py-4">
                <div x-show="!aside_error_notifications" class="text-xs text-center text-tbn-dark dark:text-tbn-light">
                    <i class="mr-1 text-green-500 far fa-check-circle"></i> Notificaciones activadas
                </div>
                <div x-show="aside_error_notifications" class="p-4 my-4 text-left border rounded-lg border-tbn-primary">
                    <p class="mb-2 font-medium">Activa las notificaciones</p>
                    <p class="mb-3 text-xs font-medium text-tbn-dark dark:text-tbn-light">
                        Entérate de las mejores convocatorias en tiempo real con
                        <span class="font-bold text-tbn-primary">Trabajonautas.com</span>
                    </p>
                    <button x-on:click="activateNotificationsAndSaveCurrentToken"
                        class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-primary border-tbn-primary hover:bg-transparent hover:text-tbn-primary">
                        <span x-show="!button_notify_loading"><i class="mr-1 fa-solid fa-bell"></i> Activar</span>
                        <span x-show="button_notify_loading"><i class="text-sm fas fa-spinner animate-spin"></i></span>
                    </button>
                </div>
            </div>
        @endif
        <!-- Navigation -->
        <nav class="py-4 text-sm select-none">
            <a x-on:click="btnNavigation = 1" wire:click='updateLastCheck' wire:poll.60s.visible
                class="relative flex items-center justify-between py-2 transition-all duration-300 cursor-pointer text-tbn-secondary dark:text-tbn-light hover:text-tbn-primary">
                <span><i class="ml-2 mr-2 fa-solid fa-bullhorn"></i> Novedades</span>
                @if ($has_new_announces && intval($client->account->account_type_id) === 3)
                    <span class="absolute right-0 block w-2 h-2 rounded-full top-4 bg-tbn-primary animate-pulse"></span>
                @endif
            </a>
            <a x-on:click="btnNavigation = 2"
                class="flex items-center py-2 transition-all duration-300 cursor-pointer text-tbn-secondary dark:text-tbn-light hover:text-tbn-primary">
                <i class="ml-2 mr-3 fas fa-bookmark"></i> Mis convocatorias
            </a>
        </nav>
        <!-- FAQ Card -->
        <div class="pt-4">
            <h4 class="mb-2 text-sm font-bold text-tbn-secondary dark:text-tbn-light">¿Necesitas ayuda?</h4>
            <p class="mt-1 mb-4 text-xs text-tbn-secondary dark:text-tbn-light">
                Nuestro Centro de Ayuda está listo para despejar tus dudas.</p>
            <a href="{{ route('faq') }}" wire:navigate
                class="inline-block px-3 py-2 text-xs text-white transition-colors duration-300 border rounded cursor-pointer bg-tbn-secondary border-tbn-secondary hover:bg-transparent hover:text-tbn-light">
                Ir al Centro de Ayuda
            </a>
        </div>
</aside>
