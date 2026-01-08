<section>
    <x-title-app>
        <x-slot name="title_page">Usuarios</x-slot>
        <x-slot name="description_page">
            Administra la información de todos los usuarios y clientes registrados en Trabajonautas.com
        </x-slot>
        <x-slot name="search_field">
            <div class="mt-3">
                <x-button-link href="{{ route('config-user') }}" class="bg-tbn-primary pt-2.5"
                    wire:navigate>Nuevo</x-button-link></div>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <table class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
            <thead class="text-xs uppercase text-tbn-secondary dark:text-tbn-light">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nombre
                    </th>
                    <th scope="col" class="hidden px-6 py-3 md:table-cell">
                        Email
                    </th>
                    <th scope="col" class="hidden px-6 py-3 md:table-cell">
                        Actualización
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody class="text-tbn-secondary dark:text-tbn-light">
                @forelse ($users as $user)
                    <tr wire:key='{{ $user->id }}' class="border-b dark:border-b-tbn-secondary hover:bg-gray-300 dark:hover:bg-neutral-900">
                        <th scope="row" class="px-6 py-4 font-medium text-tbn-dark dark:text-white whitespace-nowrap">
                            <span class="mr-2" x-html="setUserRole({{ $user->getRoleNames() }})"></span>
                            <h5
                                class="inline text-md md:text-lg font-medium {{ !$user->actived ? 'line-through text-tbn-light dark:text-white' : '' }}">
                                {{ $user->name }}</h5>
                        </th>
                        <td class="hidden px-6 py-4 md:table-cell">
                            {{ $user->email }}
                        </td>
                        <td class="hidden px-6 py-4 md:table-cell">
                            {{ (new Carbon\Carbon($user->updated_at))->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($user->actived)
                                <span class="px-2 py-1 text-xs text-white rounded-full bg-tbn-primary">
                                    Activo</span>
                            @else
                                <span class="px-2 py-1 text-xs text-white rounded-full bg-tbn-dark">
                                    Inactivo</span>
                            @endif
                        </td>
                        <td class="flex flex-row items-center justify-end px-6 py-4 text-xl h-15">
                            <a href="{{ route('config-user', ['id' => $user->id]) }}" class="text-tbn-dark dark:text-tbn-light"
                                wire:navigate><i class="fas fa-cog"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="py-4 text-center text-gray-600 font-italic" colspan="4">No se han encontrado
                            datos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- <div> {{ $users->links() }} </div> --}}
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                setUserRole(roles) {
                    switch (roles[0]) {
                        case 'ADMIN':
                            return '<i class="text-gray-500 fas fa-user-cog"></i>';
                            break;
                        case 'USER':
                            return '<i class="fas fa-user text-tbn-primary"></i>';
                            break;
                        case 'FREE_CLIENT':
                            return '<i class="text-green-500 fas fa-leaf"></i>';
                            break;
                        case 'PRO_CLIENT':
                            return '<i class="text-orange-500 fas fa-crown"></i>';
                            break;
                    }
                },
                confirmModal(id) {
                    console.log(id)
                }
            }))
        </script>
    @endscript
</section>
