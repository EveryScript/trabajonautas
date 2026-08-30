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
                    {{-- Coins tooltip --}}
                    <div class="relative inline-block ml-2 group">
                        <button class="rounded-full text-tbn-primary hover:text-tbn-secondary focus:outline-none">
                            <i class="text-xs fa-regular fa-circle-question"></i>
                        </button>
                        <div
                            class="absolute bottom-full left-1/3 mb-2 mr-2 hidden -translate-x-1/2 group-hover:block
                w-64 max-w-[85vw] sm:w-max sm:max-w-xs
                rounded-md bg-neutral-900 px-4 py-3 text-xs text-white shadow-lg transition-opacity">
                            Estas monedas te permiten ver convocatorias que NO son de tu profesión por si
                            tienes interés en explorar alguna otra oferta laboral.
                            <div
                                class="absolute -mt-1 -translate-x-1/2 border-4 border-transparent top-full left-1/2 border-t-neutral-900">
                            </div>
                        </div>
                    </div>
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
