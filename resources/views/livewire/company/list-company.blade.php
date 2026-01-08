<section>
    <x-title-app>
        <x-slot name="title_page">Empresas</x-slot>
        <x-slot name="description_page">
            Administra la información sobre las empresas registradas en Trabajonautas.com
        </x-slot>
        <x-slot name="search_field">
            <div class="flex flex-row h-full gap-1 sm:h-10">
                <x-input type="search" name="search" wire:keydown.enter="$set('search', $event.target.value)"
                    class="w-full" placeholder="Buscar empresa" />
                <x-button type="button" href="{{ route('new-company') }}" wire:navigate>Nuevo</x-button>
            </div>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <div class="overflow-x-auto">
            <table class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
                <thead class="text-xs uppercase text-tbn-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Nombre empresa
                        </th>
                        <th scope="col" class="hidden px-6 py-3 lg:table-cell">
                            Tipo de empresa
                        </th>
                        <th scope="col" class="hidden px-6 py-3 lg:table-cell">
                            Última actualización
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($search)
                        <tr class="text-sm text-center bg-gray-200 text-tbn-dark">
                            <td class="px-6 py-2" colspan="4">
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
                    @forelse ($companies as $company)
                        <tr wire:key='{{ $company->id }}'
                            class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                            <th scope="row"
                                class="px-6 py-4 max-w-60 sm:max-w-md lg:max-w-2xl font-medium whitespace-wrap {{ $company->trashed() ? 'opacity-40' : '' }}">
                                <div class="flex flex-row gap-3">
                                    <img src="{{ asset('storage/' . $company->company_image) }}" alt="logo"
                                        class="flex-shrink-0 object-cover object-center w-10 h-10 mb-4 rounded-lg sm:mb-0">
                                    <div class="truncate">
                                        <h5 class="font-bold text-md dark:text-white">{{ $company->company_name }}</h5>
                                        <span
                                            class="text-sm font-normal text-tbn-secondary dark:text-tbn-light">{{ $company->description }}</span>
                                    </div>
                                </div>
                            </th>
                            <td
                                class="px-6 py-4 dark:text-tbn-light hidden lg:table-cell {{ $company->trashed() ? 'opacity-40' : '' }}">
                                {{ $company->companyType->company_type_name }}
                            </td>
                            <td
                                class="px-6 py-4 dark:text-tbn-light hidden lg:table-cell {{ $company->trashed() ? 'opacity-40' : '' }}">
                                {{ \Carbon\Carbon::parse($company->updated_at)->diffForHumans() }}
                            </td>
                            <td class="flex flex-row items-center justify-end h-20 px-6 py-4 text-lg">
                                @if ($company->trashed())
                                    <a x-on:click="confirmRestoreModal({{ $company->id }})"
                                        class="font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                        <i class="fa fa-toggle-off"></i></a>
                                @else
                                    <a href="{{ route('new-company', ['id' => $company->id]) }}" wire:navigate
                                        class="mr-3 font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                        <i class="far fa-edit"></i></a>
                                    <a x-on:click="confirmDeleteModal({{ $company->id }})"
                                        class="font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                        <i class="fa fa-toggle-on"></i></a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="py-4 text-center text-gray-600 font-italic" colspan="4">
                                No se han encontrado datos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div> {{ $companies->links() }} </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                confirmDeleteModal(id) {
                    Swal.fire({
                        title: "¿Desactivar empresa?",
                        text: "Las convocatorias vinculadas a esta empresa aún estarán visibles en el sitio web.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        confirmButtonColor: '#ff420a',
                        denyButtonText: "No",
                        denyButtonColor: '#484848'
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $wire.delete(id)
                        }
                    });
                },
                confirmRestoreModal(id) {
                    Swal.fire({
                        title: "¿Activar empresa?",
                        text: "La empresa estará disponible en el sitio web y aparecerá en los formularios de creación de convocatorias:",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        confirmButtonColor: '#ff420a',
                        denyButtonText: "No",
                        denyButtonColor: '#484848'
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $wire.restore(id)
                        }
                    });
                }
            }))
        </script>
    @endscript
</section>
