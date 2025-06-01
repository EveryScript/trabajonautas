<div x-show="btnAd" class="relative mx-auto mb-8">
    <button x-on:click="btnAd = false" class="absolute top-4 right-6 text-2xl text-tbn-dark">
        <i class="fas fa-times"></i>
    </button>
    <div class="w-full mx-auto rounded-lg shadow-lg overflow-hidden lg:max-w-none lg:flex bg-white">
        <div class="flex-1 px-6 py-8 lg:p-12">
            <h3 class="text-2xl font-extrabold text-tbn-primary sm:text-3xl">Trabajonautas PRO-MAX</h3>
            <p class="mt-4 text-base text-tbn-dark sm:text-md">Las mejores convocatorias para conseguir tu
                próximo empleo están aqui.</p>
            <ul class="my-4 space-y-2 text-sm">
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i> Tiempo de uso: 35 dias
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias estandar
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias Premium
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i> Notificaciones en tiempo real
                </li>
            </ul>
            <div class="mt-4">
                <span class="text-4xl font-bold">30 Bs.</span>
            </div>
            <div class="mt-6">
                <x-button-link class="bg-tbn-primary" href="{{ route('purchase') }}" wire:navigate>
                    Adquirir PRO ahora</x-button-link>
            </div>
        </div>
        <div
            class="py-8 px-6 text-center lg:flex-shrink-0 lg:flex lg:flex-col lg:justify-center lg:p-12 bg-gray-00 hidden">
            <img src="{{ asset('storage/img/tbn-champ.webp') }}" alt="empty"
                class="w-[10rem] h-[10rem] mx-auto mb-4">
        </div>
    </div>
</div>
