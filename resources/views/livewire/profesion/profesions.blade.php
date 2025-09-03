<section>
    <x-title-app>
        <x-slot name="title_page">Profesiones</x-slot>
        <x-slot name="description_page">
            Registra nuevas profesiones existentes para los clientes del sistema Trabajonautas
        </x-slot>
        <x-slot name="search_field">
            <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)"
                placeholder="Buscar profesión" />
        </x-slot>
    </x-title-app>
    <div x-data="content" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <!-- Profesions form -->
            <form class="bg-white rounded-lg px-6 py-5 shadow-lg mb-4"
                wire:submit="{{ $update_mode ? 'update' : 'save' }}">
                <div class="mb-4">
                    <x-label for="expiration_time" value="{{ __('Nombre de la profesión') }}" />
                    <x-input wire:model="profesion.profesion_name" id="profesion_name" type="text"
                        class="mt-1 block w-full" placeholder="Ingeniería, Leyes, Arquitectura" />
                    <x-input-error for="profesion.profesion_name" class="mt-2" />
                </div>
                <x-button type="submit">{{ $update_mode ? 'Actualizar profesion' : 'Crear profesion' }}</x-button>
                <x-secondary-button @click="cancelAndClearForm" type="button" class="ml-2">
                    Cancelar</x-secondary-button>
                <hr class="bg-tbn-dark my-4">
                <div class="text-xs text-tbn-dark">
                    Las <strong>profesiones</strong> son utilizadas para que los usuarios en el sistema puedan
                    registrarse con ellas y también se vinculan a las convocatorias que se registran en el sitio web.
                </div>
            </form>
        </div>
        <div>
            <!-- Profesions table -->
            <table class="w-full bg-white rounded-md shadow-md mb-5 text-sm text-left rtl:text-right">
                <thead class="text-xs uppercase text-tbn-dark">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Nombre de profesión
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($search)
                        <tr class="text-center text-tbn-dark text-sm bg-gray-200">
                            <td class="px-6 py-2" colspan="2">
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
                        <tr class="border-b hover:bg-gray-300">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                <h5 class="text-md font-bold">{{ $profesion->profesion_name }}</h5>
                            </th>
                            <td class="flex flex-row justify-end items-center h-15 px-6 py-4 text-lg">
                                <a wire:click="edit({{ $profesion->id }})"
                                    class="font-medium text-blue-600 hover:underline cursor-pointer mr-3">
                                    <i class="far fa-edit"></i></a>
                                @role('ADMIN')
                                    <a x-on:click="confirmModal({{ $profesion->id }})"
                                        class="font-medium text-red-600 hover:underline cursor-pointer">
                                        <i class="far fa-trash-alt"></i></a>
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b hover:bg-gray-50 ">
                            <td class="py-4 text-center font-italic text-gray-600" colspan="4">
                                No se han encontrado datos
                                <button class="text-tbn-primary underline"
                                    wire:click="$set('search', null)">Aceptar</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div> {{ $profesions->links() }} </div>
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                init() {
                    $wire.on('profesion-saved', () => {
                        Swal.fire({
                            title: "Profesión registrada",
                            icon: "success"
                        });
                    })
                },
                cancelAndClearForm() {
                    this.profesion_name = '';
                    $wire.cancelForm();
                },
                confirmModal(id) {
                    Swal.fire({
                        title: "¿Eliminar profesion?",
                        text: "Los usuarios registrados con esta profesión NO serán eliminados.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        denyButtonText: "No"
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
