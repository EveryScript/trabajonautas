<section>
    <x-title-app>
        <x-slot name="title_page">Usuarios</x-slot>
        <x-slot name="description_page">
            Administra la información de todos los usuarios y clientes registrados en Trabajonautas.com
        </x-slot>
        <x-slot name="search_field">
            <x-select wire:model.change='filterClients'>
                <option value="1">Clientes</option>
                <option value="0">Usuarios</option>
            </x-select>
            <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)"
                placeholder="Buscar usuario o cliente" />
            <div class="mt-3"><x-button-link href="{{ route('create-user') }}" class="py-[.70rem]"
                    wire:navigate>Nuevo</x-button-link></div>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <table class="w-full bg-white rounded-md shadow-md mb-5 text-sm text-left rtl:text-right">
            <thead class="text-xs uppercase text-tbn-dark">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Profesiones
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Registro
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b hover:bg-gray-300">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <span class="mr-2" x-html="setUserRole({{ $user->getRoleNames() }})"
                                x-bind:class="{
                                    'text-gray-500': {{ $user->getRoleNames() }} == 'ADMIN',
                                    'text-tbn-primary ': {{ $user->getRoleNames() }} == 'USER',
                                    'text-green-500': {{ $user->getRoleNames() }} == 'FREE_CLIENT',
                                    'text-orange-500': {{ $user->getRoleNames() }} == 'PRO_CLIENT'
                                }">
                            </span>
                            <h5 class="inline text-lg font-medium {{ !$user->actived ? 'line-through text-gray-400' : '' }}">
                                {{ $user->name }}</h5>
                        </th>
                        <td class="px-6 py-4">
                            @forelse ($user->myProfesions as $profesion)
                                <span
                                    class="inline-block px-2 py-1 rounded-sm bg-gray-200 text-black text-[.8rem] leading-4 mb-1">
                                    {{ $profesion->profesion_name }}</span>
                            @empty
                                <span class="text-sm text-gray-400">(Sin registrar)</span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4">
                            {{ (new Carbon\Carbon($user->created_at))->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($user->proAccount)
                                @if ($user->proAccount->verified_payment)
                                    <span
                                        class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Verificado</span>
                                @else
                                    <span
                                        class="bg-tbn-dark text-white animate-pulse text-xs px-2 py-1 rounded-full">Pendiente</span>
                                @endif
                            @endif
                        </td>
                        <td class="flex flex-row justify-end items-center h-15 px-6 py-4 text-xl">
                            <a href="{{ $user->hasRole(USER) || $user->hasRole(ADMIN)
                                ? route('create-user', ['id' => $user->id])
                                : route('config-user', ['id' => $user->id]) }}"
                                class="text-tbn-dark" wire:navigate>
                                <i class="fas fa-cog"></i>
                            </a>
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
        {{-- <div> {{ $users->links() }} </div> --}}
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                setUserRole(roles) {
                    switch (roles[0]) {
                        case 'ADMIN':
                            return '<i class="fas fa-user-cog"></i>';
                            break;
                        case 'USER':
                            return '<i class="fas fa-user"></i>';
                            break;
                        case 'FREE_CLIENT':
                            return '<i class="fas fa-leaf"></i>';
                            break;
                        case 'PRO_CLIENT':
                            return '<i class="fas fa-crown"></i>';
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
