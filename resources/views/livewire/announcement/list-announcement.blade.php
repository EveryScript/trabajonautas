<section>
    <x-title-app>
        <x-slot name="title_page">Convocatorias</x-slot>
        <x-slot name="description_page">
            Todas las convocatorias de trabajo registradas en el portal de empleos Trabajonautas.com
        </x-slot>
        <x-slot name="search_field">
            <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)"
                placeholder="Buscar convocatoria" />
            <x-button-link class="pt-2.5 bg-tbn-primary" href="{{ route('new-announcement') }}" wire:navigate>Nuevo</x-button-link>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <table class="bg-white rounded-md shadow-md mb-5 w-full text-sm text-left rtl:text-right">
            <thead class="text-xs uppercase text-tbn-dark">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Convocatoria
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Ubicación
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Empresa
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
                                class="text-tbn-primary underline" wire:click="$set('search', null)"> Volver
                            </button> </td>
                    </tr>
                @endif
                @forelse ($announcements as $announcement)
                    <tr class="border-b hover:bg-gray-300">
                        <th scope="row" class="px-6 py-4 font-medium  whitespace-nowrap">
                            <div class="flex flex-row gap-2">
                                <div class="max-w-[20rem] truncate">
                                    <h5 class="text-md font-bold">
                                        @if ($announcement->pro)
                                            <i class="fas fa-crown pr-1 text-sm text-tbn-secondary"></i>
                                        @endif
                                        {{ $announcement->announce_title }}
                                    </h5>
                                    <span class="font-normal text-tbn-dark">
                                        {{ $announcement->area ? $announcement->area->area_name : '(Area eliminada)' }}</span>
                                </div>
                            </div>
                        </th>
                        <td class="px-6 py-4">
                            @forelse ($announcement->locations as $location)
                                <span
                                    class="inline-block px-2 py-1 rounded-md bg-gray-200 text-black text-[.8rem] leading-4 mb-1">{{ $location->location_name }}</span>
                            @empty
                                <span class="text-sm text-gray-400">No items</span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4">
                            {{ $announcement->company ? $announcement->company->company_name : '(Empresa eliminada)' }}
                        </td>
                        <td class="flex flex-row justify-end items-center h-20 px-6 py-4 text-lg">
                            <a href="{{ route('new-announcement', ['id' => $announcement->id]) }}" wire:navigate
                                class="font-medium text-tbn-primary hover:underline cursor-pointer mr-3">
                                <i class="far fa-edit"></i></a>
                            <a x-on:click="confirmModal({{ $announcement->id }})"
                                class="font-medium text-red-600 hover:underline cursor-pointer">
                                <i class="far fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="py-4 text-center font-italic text-gray-600" colspan="6">No se han
                            encontrado
                            datos <button class="text-tbn-primary underline" wire:click="$set('search', null)">
                                Aceptar </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div> {{ $announcements->links() }} </div>
    </div>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                confirmModal(id) {
                    Swal.fire({
                        title: "¿Eliminar convocatoria?",
                        text: "La convocatoria se eliminará para todos los clientes del sistema Trabajonautas",
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
