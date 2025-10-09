<x-web-layout>
    <!-- Announcement found -->
    <section class="max-w-2xl mx-auto my-8">
        <div class="relative bg-white rounded-lg shadow-md p-5 sm:p-10 text-center">
            <picture class="block mb-4">
                <img src="{{ asset('storage/img/empty.webp') }}" alt="empty" class="max-w-[8rem] mx-auto mb-2">
            </picture>
            <h2 class="text-2xl font-bold text-tbn-primary">
                Vaya, parece que no puedes acceder a esta convocatoria.</h2>
            <p class="text-tbn-dark text-md mb-6">
                Esta convocatoria no está disponible para ti, ya que el area a la cual perteneces es diferente al area
                de la convocatoria.
            </p>
            <div class="flex flex-col justify-center md:flex-row gap-2">
                <x-button-link class="bg-tbn-primary" href="{{ route('search') }}" wire:navigate>
                    Buscar convocatorias</x-button-link>
                <x-button-link class="bg-tbn-primary" href="{{ route('dashboard') }}" wire:navigate>
                    Ir al Panel</x-button-link>
            </div>
        </div>
    </section>

</x-web-layout>
