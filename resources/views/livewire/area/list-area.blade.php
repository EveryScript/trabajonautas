<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Areas profesionales</x-slot>
            <x-slot name="description_page">
                Administra las areas profesionales para clasificar las convocatorias y a los clientes.
            </x-slot>
            <x-slot name="search_field">
                <div class="mt-4">
                    <x-button-link x-on:click="openAreaForm">Nuevo</x-button-link>
                </div>
            </x-slot>
        </x-title-app>
        <div class="block grid-cols-2 gap-5 md:grid lg:grid-cols-3">
            @forelse ($areas as $area)
                <article
                    class="px-6 py-5 mb-3 transition duration-200 bg-white border border-transparent rounded-md shadow-lg dark:bg-tbn-dark md:mb-0 hover:border-tbn-primary">
                    <div class="flex flex-col justify-between h-full">
                        <div class="dark:text-white">
                            <span class="text-xs text-tbn-primary">Area profesional</span>
                            <h5 class="mb-2 text-lg font-bold">{{ $area->area_name }}</h5>
                            <p class="mb-2 text-xs text-tbn-dark dark:text-tbn-light">{{ $area->description }}</p>
                            <p class="text-sm">Creador: <span class="text-tbn-primary">{{ $area->user->name }}</span>
                            </p>
                            {{-- <p class="text-sm">Profesiones: <span class="text-tbn-primary">{{ count($area->profesions) }}</span> --}}
                            <p class="text-sm">Profesiones: <span class="font-bold underline cursor-pointer text-tbn-primary"
                                    title="{{ $area->profesions->pluck('profesion_name')->join("\n") }}">{{ count($area->profesions) }}</span>
                            </p>
                        </div>
                        <div class="flex flex-row justify-end text-lg">
                            <a wire:click="editArea({{ $area->id }})"
                                class="mr-3 font-medium transition-colors duration-150 cursor-pointer text-tbn-secondary dark:text-tbn-primary dark:hover:text-tbn-secondary">
                                <i class="far fa-edit"></i></a>
                            <a class="font-medium transition-colors duration-150 cursor-pointer text-tbn-secondary dark:text-tbn-primary dark:hover:text-tbn-secondary"
                                x-on:click="confirmModal({{ $area->id }})">
                                <i class="far fa-trash-alt"></i></a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center text-tbn-dark dark:text-tbn-light">
                    <span class="text-sm">No hay areas profesionales aún</span>
                </div>
            @endforelse
        </div>
        <!-- Area form modal --->
        <div x-show="areaForm">
            <livewire:area.form-area />
        </div>
    </div>

    @script
        <script>
            Alpine.data('content', () => ({
                areaForm: false,
                init() {
                    $wire.on('area-edit', () => {
                        this.openAreaForm()
                    })
                    $wire.on('area-saved', () => {
                        this.closeAreaForm()
                        Swal.fire({
                            title: "Area guardada",
                            text: "El área ahora está disponible en el sistema Trabajonautas.com",
                            confirmButtonText: "Listo",
                            confirmButtonColor: '#ff420a'
                        })
                    })
                },
                confirmModal(id) {
                    Swal.fire({
                        title: "¿Eliminar area?",
                        text: "Las convocatorias vinculadas a esta area aún estarán disponibles.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        confirmButtonColor: '#ff420a',
                        denyButtonText: "No",
                        denyButtonColor: '#484848'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.delete(id)
                        }
                    });
                },
                openAreaForm() {
                    this.areaForm = true
                },
                closeAreaForm() {
                    this.areaForm = false
                }
            }))
        </script>
    @endscript
</section>
