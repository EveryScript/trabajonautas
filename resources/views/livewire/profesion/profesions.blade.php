<section>
    <x-title-app>
        <x-slot name="title_page">Profesiones</x-slot>
        <x-slot name="description_page">
            Registra nuevas profesiones existentes para los clientes del sistema Trabajonautas
        </x-slot>
    </x-title-app>
    <div class="grid grid-cols-2 gap-2" x-data="content">
        <!-- Profesions form -->
        <div class="">
            <form class="bg-white rounded-lg px-6 py-5 shadow-lg" wire:submit="{{ $update_mode ? 'update' : 'save' }}">
                <div class="mb-4">
                    <x-label for="expiration_time" value="{{ __('Nombre de la profesión') }}" />
                    <x-input wire:model="profesion.profesion_name" id="profesion_name" type="text"
                        class="mt-1 block w-full" placeholder="Ingeniería, Leyes, Arquitectura" />
                    <x-input-error for="profesion.profesion_name" class="mt-2" />
                </div>
                <x-button>{{ $update_mode ? 'Actualizar profesion' : 'Crear profesion' }}</x-button>
                <!-- If saved -->
                @if (session('status'))
                    <div class="flex items-center p-4 mb-4 text-sm bg-green-100 text-green-500 rounded-lg" role="alert">
                        <i class="fas fa-check-circle pr-2"></i>
                        <span class="sr-only font-bold pr-2">Listo</span>
                        <div><span class="font-medium">Listo</span> {{ session('status') }}</div>
                    </div>
                @endif
            </form>
        </div>
        <!-- Profesions table -->
        <div class="">
            <x-input wire:model='search' wire:keydown.enter="$set('search', $event.target.value)" wire:ignore
                id="search" class="block w-full mb-2" placeholder="Buscar profesion" />
            <table class="w-full bg-white rounded-md shadow-md mb-5 text-sm text-left rtl:text-right">
                <thead class="text-xs uppercase text-tbn-dark">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Nombre de profesión
                                <svg wire:click="sortField('company_name')" class="w-4 h-4 ms-1 cursor-pointer"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($search)
                        <tr class="text-center text-tbn-dark text-sm">
                            <td class="py-2" colspan="5">Resultados encontrados <button
                                    class="text-tbn-primary underline" wire:click="$set('search', null)"> Aceptar
                                </button> </td>
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
                                    <i class="far fa-edit"></i></a></a>
                                @role('ADMIN')
                                    <a x-on:click="confirmModal({{ $profesion->id }})"
                                        class="font-medium text-red-600 hover:underline cursor-pointer">
                                        <i class="far fa-trash-alt"></i></a>
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b hover:bg-gray-50 ">
                            <td class="py-4 text-center font-italic text-gray-600" colspan="4">No se han encontrado
                                datos <button class="text-tbn-primary underline" wire:click="$set('search', null)">
                                    Aceptar </button>
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
