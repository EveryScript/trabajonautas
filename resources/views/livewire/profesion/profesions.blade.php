<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Profesiones</x-slot>
            <x-slot name="description_page">
                Registra nuevas profesiones existentes para los clientes del sistema Trabajonautas
            </x-slot>
            <x-slot name="search_field">
                <div class="h-full sm:h-10 flex flex-row gap-1">
                    <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)" class="w-full"
                        placeholder="Buscar profesion" />
                    <x-button type="button" href="{{ route('new-announcement') }}" wire:navigate>Nuevo</x-button-link>
                </div>
            </x-slot>
        </x-title-app>
        <!-- Profesions modal form -->
        <div x-show="modalForm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 dark:bg-opacity-80">
            <form class="max-w-lg bg-white dark:bg-tbn-dark rounded-lg px-6 py-5 shadow-lg mx-4"
                wire:submit="{{ $update_mode ? 'update' : 'save' }}">
                <div class="mb-4">
                    <x-label for="expiration_time" value="{{ __('Nombre de la profesión') }}" />
                    <x-input wire:model="profesion.profesion_name" id="profesion_name" type="text"
                        class="mt-1 block w-full" placeholder="Ingeniería, Leyes, Arquitectura" />
                    <x-input-error for="profesion.profesion_name" class="mt-2" />
                </div>
                <x-button type="submit">
                    <span wire:loading.remove>{{ $update_mode ? 'Actualizar profesion' : 'Crear profesion' }}</span>
                    <span wire:loading><i class="fas fa-spinner text-sm animate-spin"></i></span>
                </x-button>
                <x-secondary-button x-on:click="modalForm = false" type="button" class="ml-2">
                    Cancelar</x-secondary-button>
                <hr class="bg-tbn-dark dark:bg-tbn-secondary my-4">
                <div class="text-xs text-tbn-dark dark:text-tbn-light">
                    Las <strong>profesiones</strong> son utilizadas para que los usuarios en el sistema puedan
                    registrarse con ellas y también se vinculan a las convocatorias que se registran en el sitio web.
                </div>
            </form>
        </div>
        <!-- Profesions table -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-tbn-dark rounded-md shadow-md mb-5 text-sm text-left rtl:text-right">
                <thead class="text-xs uppercase text-tbn-dark dark:text-tbn-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Nombre de profesión
                        </th>
                        <th scope="col" class="px-6 py-3 hidden lg:table-cell">
                            Creación
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($search)
                        <tr class="text-center text-tbn-dark text-sm bg-gray-200 dark:border-b-tbn-secondary">
                            <td class="px-6 py-2" colspan="3">
                                <div class="flex flex-row justify-between items-center">
                                    <div>
                                        <span class="font-bold">"{{ $search }}"</span>
                                        <i class="fas fa-arrow-right text-xs px-2"></i>
                                        {{ $count_results }} Resultados encontrados
                                    </div>
                                    <button type="button" wire:click="$set('search', null)">
                                        <i class="fas fa-times text-tbn-primary text-lg"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @forelse ($profesions as $profesion)
                        <tr class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                            <th scope="row" class="px-6 py-4 font-medium dark:text-white whitespace-wrap">
                                <h5 class="text-md font-bold">{{ $profesion->profesion_name }}</h5>
                            </th>
                            <td class="px-6 py-4 dark:text-tbn-light hidden lg:table-cell">
                                {{ \Carbon\Carbon::parse($profesion->created_at)->diffForHumans() }}
                            </td>
                            <td class="flex flex-row justify-end items-center h-15 px-6 py-4 text-lg">
                                <a x-on:click="editProfesion({{ $profesion->id }})"
                                    class="font-medium text-tbn-primary hover:text-tbn-secondary transition-colors duration-300 cursor-pointer mr-3">
                                    <i class="far fa-edit"></i></a>
                                @role('ADMIN')
                                    <a x-on:click="confirmModal({{ $profesion->id }})"
                                        class="font-medium text-tbn-primary hover:text-tbn-secondary transition-colors duration-300 cursor-pointer">
                                        <i class="far fa-trash-alt"></i></a>
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b hover:bg-gray-50 ">
                            <td class="py-4 text-center font-italic text-gray-600" colspan="4">
                                No se han encontrado datos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div> {{ $profesions->links() }} </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                modalForm: false,
                init() {
                    $wire.on('profesion-saved', () => {
                        this.modalForm = false;
                    })
                },
                editProfesion(id) {
                    this.modalForm = true
                    $wire.edit(id);
                },
                cancelAndClearForm() {
                    $wire.cancelForm();
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
                }
            }))
        </script>
    @endscript
</section>
