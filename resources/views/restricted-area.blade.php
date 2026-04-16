<x-web-layout>
    <section class="flex flex-col justify-center py-20 bg-gray-50 dark:bg-neutral-900 sm:px-6 lg:px-8">
        <div class="text-center sm:mx-auto sm:w-full sm:max-max-md">
            <div class="flex items-center justify-center mx-auto mb-2 rounded-full">
                <picture class="block">
                    <img src="{{ asset('storage/img/astro-res.webp') }}" alt="empty"
                        class="max-w-[9rem] mx-auto">
                </picture>
            </div>

            <h2
                class="mb-4 text-3xl font-extrabold tracking-tight text-center text-tbn-dark dark:text-white sm:text-4xl">
                Así que quieres ver esta convocatoria...
            </h2>
            <div class="max-w-3xl mx-auto">
                <p class="px-4 mb-2 text-xl font-semibold text-tbn-secondary dark:text-tbn-light">
                    Lo sentimos, esta misión <strong class="text-tbn-primary">NO</strong> coincide con tu profesión
                    actual. <br> El radar la muestra para que puedas invitar a otros amigos exploradores a unirse y completar el
                    desafío.
                </p>
                <p class="px-4 mb-2 text-base text-tbn-secondary dark:text-tbn-light" hidden>
                    El radar la muestra para que puedas invitar a otros amigos exploradores a unirse y completar el
                    desafío.
                </p>
                <p class="px-4 mb-6 text-lg italic text-tbn-primary">
                    ¡No guardes tu casco todavía! Tu próxima gran aventura ya te espera en el <strong>Panel de
                        Usuario</strong>.
                </p>
            </div>

            <div class="flex flex-col justify-center gap-2 mb-8 md:flex-row">
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <x-button>Ir al Panel de Usuario</x-button>
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
