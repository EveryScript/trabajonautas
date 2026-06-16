@props(['coins'])
<div
    class="relative bg-transparent sm:bg-white sm:border sm:rounded-lg sm:shadow-md sm:dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary sm:p-10">
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
                <p class="px-4 mb-4 text-tbn-secondary dark:text-tbn-light">
                    La profesión de esta convocatoria no coincide con tu profesión registrada, pero puedes
                    usar tus
                    <span class="text-tbn-primary">monedas</span> si quieres aterrizar en este planeta.
                </p>
                <div
                    class="flex mx-auto items-center justify-center max-w-[12rem] p-4 mb-6 bg-gray-100 border border-gray-200 rounded-xl dark:bg-tbn-dark dark:border-tbn-secondary">
                    <div>
                        <p
                            class="text-xs font-medium tracking-wider uppercase text-tbn-secondary dark:text-tbn-light/70">
                            {{ $coins == 1 ? 'Te queda:' : 'Te quedan:' }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="text-2xl text-yellow-500 fa-solid fa-coins"></i>
                            <strong class="text-2xl font-bold tracking-tight text-tbn-primary">
                                {{ $coins }}
                                <span class="text-sm font-normal text-tbn-secondary dark:text-tbn-light/80 ml-0.5">
                                    {{ $coins == 1 ? 'moneda' : 'monedas' }}
                                </span>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-center gap-1 px-4 mb-8 md:flex-row">
                @if ($coins < 1)
                    <x-button disabled class="w-full my-1 cursor-not-allowed sm:w-auto">
                        Ya no tienes monedas <i class="ml-1 text-yellow-500 fa-solid fa-circle"></i>
                    </x-button>
                @else
                    <x-button wire:click='unlock' wire:loading.attr="disabled" class="w-full my-1 sm:w-auto">
                        <span wire:loading.remove wire:target="unlock">Desbloquear por 1 <i
                                class="ml-1 text-yellow-500 fa-solid fa-circle"></i></span>
                        <span wire:loading wire:target="unlock">Desbloqueando...</span>
                    </x-button>
                @endif
                <x-secondary-button type="button" onclick="history.back()" class="w-full my-1 sm:w-auto">
                    <i class="pr-2 text-sm fas fa-arrow-left"></i> Volver
                </x-secondary-button>
            </div>

            <div class="px-4 text-xs text-tbn-secondary dark:text-tbn-light" hidden>
                ¿Necesitas ayuda o tienes preguntas? Comunícate con nosotros
                <a href="https://wa.me/{{ env('SUPPORT_PHONE') }}?text=Hola%20Trabajonautas.com%2C%20necesito%20ayuda%20con%20mis%20monedas%20para%20desbloquear%20una%20misión%20PRO"
                    class="underline text-tbn-primary" target="_blank">
                    ahora mismo</a>
            </div>
        </div>
    </section>
</div>
