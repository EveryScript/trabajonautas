<section>
    <div class="mb-4">
        <x-title-app>
            <x-slot name="title_page">Areas profesionales</x-slot>
            <x-slot name="description_page">
                Registra las areas a las que los clientes pueden acceder para registrarse en nuestro sistema.
            </x-slot>
            <x-slot name="search_field">
                <x-button-link href="{{ route('new-area') }}" wire:navigate>Nuevo</x-button-link>
            </x-slot>
        </x-title-app>
    </div>
    <div x-data="content">
        <div class="grid grid-cols-3 gap-5">
            @forelse ($areas as $area)
                <article
                    class="bg-white rounded-md shadow-lg px-6 py-5 border border-transparent hover:border-tbn-primary transition duration-200">
                    <div class="h-full flex flex-col justify-between">
                        <div>
                            <span class="text-xs text-tbn-primary">Area profesional</span>
                            <h5 class="text-lg font-bold">{{ $area->area_name }}</h5>
                            <p class="text-sm text-tbn-dark">{{ $area->description }}</p>
                            <p class="text-sm">Profesiones <span
                                    class="text-tbn-primary">{{ count($area->profesions) }}</span></p>
                            <p class="text-sm">Creador <span class="text-tbn-primary">{{ $area->user->name }}</span>
                            </p>
                        </div>
                        <div class="flex flex-row justify-end text-lg">
                            <a href="{{ route('new-area', ['id' => $area->id]) }}" wire:navigate
                                class="font-medium text-blue-600 hover:underline cursor-pointer mr-3">
                                <i class="far fa-edit"></i></a>
                            <a class="font-medium text-red-600 hover:underline cursor-pointer"
                                x-on:click="confirmModal({{ $area->id }})">
                                <i class="far fa-trash-alt"></i></a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-tbn-dark text-center">
                    <span class="text-sm">No hay areas profesionales aún</span>
                </div>
            @endforelse
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                confirmModal(id) {
                    Swal.fire({
                        title: "¿Eliminar area?",
                        text: "Las convocatorias vinculadas a esta area aún estarán disponibles.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        denyButtonText: "No"
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $wire.delete(id)
                        }
                    });
                }
            }))
        </script>
    @endscript
</section>
