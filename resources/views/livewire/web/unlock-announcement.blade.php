<div>
    <section class="flex flex-col justify-center py-20 sm:px-6 lg:px-8">
        <div class="text-center sm:mx-auto sm:w-full sm:max-w-lg">
            <div class="flex items-center justify-center mx-auto mb-2 rounded-full">
                <picture class="block">
                    <img src="{{ asset('storage/img/astro-res.webp') }}" alt="empty" class="max-w-[9rem] mx-auto">
                </picture>
            </div>

            <h2
                class="mb-4 text-3xl font-extrabold tracking-tight text-center text-tbn-dark dark:text-white sm:text-4xl">
                ¿Desbloquear convocatoria?
            </h2>

            <div class="max-w-3xl mx-auto">
                <h3 class="mb-6 text-xl font-bold uppercase text-tbn-primary dark:text-white">
                    {{ $announcement->announce_title }}
                </h3>

                <p class="px-4 mb-4 text-lg text-tbn-secondary dark:text-tbn-light">
                    La profesión de esta convocatoria no coincide con tu profesión registrada, pero puedes usar tus
                    <span class="text-tbn-primary">monedas</span> si quieres aterrizar en este planeta.
                </p>

                <div
                    class="p-4 mx-4 mb-6 bg-gray-100 border border-gray-200 rounded-lg dark:bg-tbn-dark dark:border-tbn-secondary">
                    <p class="text-sm text-tbn-secondary dark:text-tbn-light">
                        {{ $coins == 1 ? 'Te queda:' : 'Te quedan:' }}
                        <strong class="text-tbn-primary">
                            🪙 {{ $coins }} {{ $coins == 1 ? 'moneda' : 'monedas' }} </strong>
                    </p>
                </div>
            </div>

            <div class="flex flex-col justify-center gap-2 px-4 mb-8 md:flex-row">
                @if (auth()->user()->coins < 1)
                    <x-button disabled class="w-full opacity-50 cursor-not-allowed md:w-auto">
                        Créditos insuficientes 🪙
                    </x-button>
                @else
                    <x-button wire:click='unlock' wire:loading.attr="disabled" class="w-full md:w-auto">
                        <span wire:loading.remove wire:target="unlock">Desbloquear por 1 🪙</span>
                        <span wire:loading wire:target="unlock">Estableciendo conexión...</span>
                    </x-button>
                @endif

                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex items-center justify-center px-4 py-2 text-sm font-medium text-tbn-secondary dark:text-tbn-light hover:underline">
                    Volver al Panel de Usuario
                </a>
            </div>

            <div class="px-4 text-xs text-tbn-secondary dark:text-tbn-light">
                ¿Necesitas soporte técnico con tus monedas? Comunícate con nosotros
                <a href="https://wa.me/{{ env('SUPPORT_PHONE') }}?text=Hola%20Trabajonautas.com%2C%20necesito%20ayuda%20con%20mis%20monedas%20para%20desbloquear%20una%20misión%20PRO"
                    class="underline text-tbn-primary" target="_blank">
                    ahora mismo</a>
            </div>
        </div>
    </section>
</div>
