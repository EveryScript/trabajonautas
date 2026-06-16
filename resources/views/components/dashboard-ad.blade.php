@props(['tbn_coins'])
<div x-show="btnAd" class="relative mx-auto mb-8">
    <button x-on:click="btnAd = false" class="absolute top-4 right-6 text-md text-tbn-primary">
        <i class="fas fa-times"></i>
    </button>
    <div
        class="w-full p-6 mx-auto overflow-hidden bg-white rounded-lg shadow-lg lg:max-w-none lg:flex sm:p-10 dark:bg-tbn-dark">
        <div class="flex-1">
            <h3 class="text-2xl font-extrabold text-tbn-primary sm:text-3xl">Trabajonautas PRO-MAX</h3>
            <p class="mt-4 text-base text-tbn-dark dark:text-white sm:text-md">
                Las mejores convocatorias para conseguir tu próximo empleo están aqui.</p>
            <ul
                class="grid grid-cols-1 gap-1 my-4 space-y-2 text-sm lg:grid-cols-2 md:gap-2 text-tbn-dark dark:text-tbn-light">
                <li class="flex items-center">
                    <i class="mr-2 text-green-500 fas fa-check"></i> Tiempo de uso: 35 dias
                </li>
                <li class="flex items-center">
                    <i class="mr-2 text-green-500 fas fa-check"></i> Convocatorias estandar
                </li>
                <li class="flex items-center">
                    <i class="mr-2 text-green-500 fas fa-check"></i> Convocatorias Premium
                </li>
                <li class="flex items-center">
                    <i class="mr-2 text-green-500 fas fa-check"></i> Notificaciones en tiempo real
                </li>
                <li class="flex items-center">
                    <i class="mr-2 text-green-500 fas fa-check"></i> Monedas especiales: {{ $tbn_coins }}
                </li>
            </ul>
            <div class="mt-4">
                <span class="text-4xl font-bold text-tbn-dark dark:text-tbn-light">30 Bs.</span>
            </div>
            <div class="mt-6">
                <x-button type="button" href="{{ route('purchase-account', ['account_type_id' => 3]) }}" wire:navigate>
                    Adquirir PRO-MAX ahora</x-button>
            </div>
        </div>
        <div class="hidden text-center lg:flex-shrink-0 lg:flex lg:flex-col lg:justify-center bg-gray-00">
            <img src="{{ asset('storage/img/tbn-starship.webp') }}" alt="empty"
                class="w-[16rem] h-[16rem] mx-auto mb-4">
        </div>
    </div>
</div>
