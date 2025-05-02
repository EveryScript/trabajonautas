<section>
    <x-title-app>
        <x-slot name="title_page">Empresas</x-slot>
        <x-slot name="description_page">
            Administra la información sobre las empresas registradas en Trabajonautas.com
        </x-slot>
        <x-slot name="search_field">
            <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)"
                placeholder="Buscar empresa" />
            <x-button-link class="pt-2.5" href="{{ route('new-company') }}" wire:navigate>Nuevo</x-button-link>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <table class="w-full bg-white rounded-md shadow-md mb-5 text-sm text-left rtl:text-right">
            <thead class="text-xs uppercase text-tbn-dark">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Nombre empresa
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Tipo de empresa
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Última actualización
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @if ($search && count($companies) > 0)
                    <tr class="text-center text-tbn-dark text-sm">
                        <td class="py-2" colspan="5">
                            {{ count($companies) }} Resultados encontrados <button class="text-tbn-primary underline"
                                wire:click="$set('search', null)"> Cerrar </button> </td>
                    </tr>
                @endif
                @forelse ($companies as $company)
                    <tr class="border-b hover:bg-gray-300">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <div class="flex flex-row gap-2">
                                <img src="{{ asset('storage/' . $company->company_image) }}" alt="logo"
                                    class="w-10">
                                <div class="max-w-[20rem] truncate">
                                    <h5 class="text-md font-bold">{{ $company->company_name }}</h5>
                                    <span class="text-sm text-gray-600 font-normal">{{ $company->description }}</span>
                                </div>
                            </div>
                        </th>
                        <td class="px-6 py-4">
                            {{ $company->company_type->company_type_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ (new Carbon\Carbon($company->updated_at))->diffForHumans() }}
                        </td>
                        <td class="flex flex-row justify-end items-center h-20 px-6 py-4 text-lg">
                            <a href="{{ route('new-company', ['id' => $company->id]) }}" wire:navigate
                                class="font-medium text-blue-600 hover:underline cursor-pointer mr-3">
                                <i class="far fa-edit"></i></a>
                            <a x-on:click="confirmModal({{ $company->id }})"
                                class="font-medium text-red-600 hover:underline cursor-pointer">
                                <i class="far fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="py-4 text-center font-italic text-gray-600" colspan="4">No se han encontrado
                            datos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div> {{ $companies->links() }} </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                confirmModal(id) {
                    Swal.fire({
                        title: "¿Eliminar empresa?",
                        text: "Las convocatorias vinculadas a esta empresa aún estarán disponibles.",
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
