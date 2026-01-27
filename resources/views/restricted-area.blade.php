<x-web-layout>
    <section class="flex flex-col justify-center py-48 bg-gray-50 dark:bg-neutral-900 sm:px-6 lg:px-8">
        <div class="text-center sm:mx-auto sm:w-full sm:max-max-md">
            <div class="flex items-center justify-center w-24 h-24 mx-auto mb-8 bg-indigo-100 rounded-full">
                <picture class="block mb-4">
                    <img src="{{ asset('storage/img/tbn-empty.webp') }}" alt="empty" class="max-w-[8rem] mx-auto mb-2">
                </picture>
            </div>

            <h2
                class="mb-4 text-3xl font-extrabold tracking-tight text-center text-tbn-dark dark:text-white sm:text-4xl">
                Planeta restringido
            </h2>

            <p class="max-w-md mx-auto mb-6 text-base text-tbn-secondary dark:text-tbn-light">
                Lo sentimos, pero no puedes acceder a convocatorias PRO que <strong>NO</strong> corresponden a tu
                profesión actual.
                Existen más convocatorias adecuadas para ti en el Panel de Usuario o encuentra muchas más en el area de
                Búsqueda.
            </p>

            <div class="flex flex-col justify-center gap-2 mb-8 md:flex-row">
                <a href="{{ route('search') }}" wire:navigate>
                    <x-button>Buscar convocatorias</x-button>
                </a>
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <x-secondary-button>Ir al Panel de Usuario</x-secondary-button>
                </a>
            </div>
            <div class="text-xs text-tbn-secondary dark:text-tbn-light">
                ¿Necesitas ayuda?. Comunícate con nosotros.
                <a href="https://wa.me/{{ env('SUPPORT_PHONE') }}?text=Hola%20Trabajonautas.com%2C%20parece%20que%20no%20puedo%20acceder%20a%20una%20convocatoria%20PRO%2C%20podrían%20ayudarme%3F"
                    class="underline text-tbn-primary" target="_blank">
                    ahora mismo</a>
            </div>
        </div>
    </section>
</x-web-layout>
