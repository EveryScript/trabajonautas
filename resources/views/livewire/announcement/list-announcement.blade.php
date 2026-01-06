<section>
    <x-title-app>
        <x-slot name="title_page">Convocatorias</x-slot>
        <x-slot name="description_page">
            Todas las convocatorias de trabajo registradas en el portal de empleos Trabajonautas.com
        </x-slot>
        <x-slot name="search_field">
            <div class="h-full sm:h-10 flex flex-row gap-1">
                <x-input type="search" name="search" wire:keydown.enter="$set('search', $event.target.value)" class="w-full" placeholder="Buscar convocatoria" />
                <x-button type="button" href="{{ route('new-announcement') }}" wire:navigate>Nuevo</x-button>
            </div>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <table class="bg-white dark:bg-tbn-dark rounded-md shadow-md mb-5 w-full text-sm text-left rtl:text-right">
            <thead class="text-xs uppercase text-tbn-secondary">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Convocatoria
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PRO
                    </th>
                    <th scope="col" class="px-6 py-3 hidden lg:table-cell">
                        Ubicación
                    </th>
                    <th scope="col" class="px-6 py-3 hidden lg:table-cell">
                        Empresa
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @if ($search)
                    <tr class="text-center text-tbn-dark text-sm bg-gray-200">
                        <td class="px-6 py-2" colspan="5">
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
                @forelse ($announcements as $announcement)
                    <tr wire:key='{{ $announcement->id }}'
                        class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                        <th scope="row" class="px-6 py-4 max-w-60 sm:max-w-md lg:max-w-2xl font-medium whitespace-wrap">
                            <h5 class="text-md font-bold truncate dark:text-white">
                                {{ $announcement->announce_title }}
                            </h5>
                            <span class="font-normal text-xs text-tbn-dark dark:text-tbn-light">
                                {{ $announcement->area->area_name }}</span>
                        </th>
                        <td class="px-6 py-4 dark:text-tbn-light">
                            @if ($announcement->pro)
                                <i class="fas fa-crown text-tbn-primary text-xs"></i>
                            @endif
                        </td>
                        <td class="px-6 py-4 dark:text-tbn-light hidden lg:table-cell">
                            @forelse ($announcement->locations as $location)
                                <span
                                    class="inline-block px-2 py-1 dark:bg-tbn-secondary rounded-md text-[.8rem] leading-4 mb-1">{{ $location->location_name }}</span>
                            @empty
                                <span class="text-sm">No items</span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4 dark:text-tbn-light hidden lg:table-cell">
                            {{ $announcement->company ? $announcement->company->company_name : '(Empresa eliminada)' }}
                        </td>
                        <td class="flex flex-row justify-end items-center h-20 px-6 py-4 text-lg">
                            <a href="{{ route('new-announcement', ['id' => $announcement->id]) }}" wire:navigate
                                class="font-medium text-tbn-primary hover:text-tbn-secondary transition-colors duration-300 cursor-pointer mr-3">
                                <i class="far fa-edit"></i></a>
                            <a x-on:click="confirmModal({{ $announcement->id }})"
                                class="font-medium text-tbn-primary hover:text-tbn-secondary transition-colors duration-300 cursor-pointer">
                                <i class="far fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="py-4 text-center font-italic text-gray-600" colspan="5">
                            No se han encontrado datos
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
