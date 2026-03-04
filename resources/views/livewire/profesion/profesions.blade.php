<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Profesiones</x-slot>
            <x-slot name="description_page">
                Registra nuevas profesiones existentes para los clientes del sistema Trabajonautas
            </x-slot>
            <x-slot name="search_field">
                <div class="flex flex-row h-full gap-1 sm:h-10">
                    <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)" class="w-full"
                        placeholder="Buscar profesion" />
                    <x-button type="button" x-on:click="openProfesionForm">Nuevo</x-button-link>
                </div>
            </x-slot>
        </x-title-app>
        <!-- Profesions table -->
        <div class="overflow-x-auto">
            <table class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
                <thead class="text-xs uppercase text-tbn-dark dark:text-tbn-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Nombre de profesión
                        </th>
                        <th scope="col" class="hidden px-6 py-3 lg:table-cell">
                            Area
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody wire:loading.class='opacity-40' class="divide-y divide-tbn-secondary dark:divide-tbn-secondary">
                    @if ($search)
                        <tr class="text-sm text-center bg-gray-200 text-tbn-dark dark:border-b-tbn-secondary">
                            <td class="px-6 py-2" colspan="3">
                                <div class="flex flex-row items-center justify-between">
                                    <div>
                                        <span class="font-bold">"{{ $search }}"</span>
                                        <i class="px-2 text-xs fas fa-arrow-right"></i>
                                        {{ $count_results }} Resultados encontrados
                                    </div>
                                    <button type="button" wire:click="$set('search', null)">
                                        <i class="text-lg fas fa-times text-tbn-primary"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @forelse ($profesions as $profesion)
                        <tr class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                            <th scope="row" class="px-6 py-4 font-medium dark:text-white whitespace-wrap">
                                <h5 class="font-bold text-md">{{ $profesion->profesion_name }}</h5>
                            </th>
                            <td class="hidden px-6 py-4 dark:text-tbn-light lg:table-cell">
                                {{ $profesion->area ? $profesion->area->area_name : '(area eliminada)' }}
                            </td>
                            <td class="flex flex-row items-center justify-end px-6 py-4 text-lg h-15">
                                <a wire:click="editProfesion({{ $profesion->id }})"
                                    class="font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                    <i class="far fa-edit"></i></a>
                                @role('ADMIN')
                                    <a x-on:click="confirmModal({{ $profesion->id }})" hidden
                                        class="ml-3 font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                        <i class="far fa-trash-alt"></i></a>
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b hover:bg-gray-50 ">
                            <td class="py-4 text-center text-gray-600 font-italic" colspan="4">
                                No se han encontrado datos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div> {{ $profesions->links() }} </div>
        <!-- Profesion modal form -->
        <div x-show="profesionForm">
            <livewire:profesion.form-profesion />
        </div>
    </div>

    @script
        <script>
            Alpine.data('content', () => ({
                profesionForm: false,
                init() {
                    $wire.on('profesion-edit', () => {
                        this.openProfesionForm()
                    })
                    $wire.on('profesion-saved', () => {
                        this.closeProfesionForm()
                        Swal.fire({
                            title: "Profesión guardada",
                            text: "La profesión ahora está disponible en el sistema Trabajonautas.com",
                            confirmButtonText: "Listo",
                            confirmButtonColor: '#ff420a'
                        })
                    })
                },
                confirmModal(id) {
                    Swal.fire({
                        title: "¿Eliminar profesión?",
                        text: "Los usuarios registrados con esta profesión NO serán eliminados.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        confirmButtonColor: '#ff420a',
                        denyButtonText: "No",
                        denyButtonColor: '#485054',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.delete(id)
                        }
                    });
                },
                openProfesionForm() {
                    this.profesionForm = true
                },
                closeProfesionForm() {
                    this.profesionForm = false
                },
            }))
        </script>
    @endscript
</section>
