<section>
    <x-title-app>
        <x-slot name="title_page">Clientes</x-slot>
        <x-slot name="description_page">
            Administra la información de todos los clientes de Trabajonautas
        </x-slot>
        <x-slot name="search_field">
            <x-input type="search" wire:keydown.enter="$set('search', $event.target.value)"
                placeholder="Buscar cliente" />
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
                        Ubicación
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Profesiones
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Registro
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Cuenta
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @if ($search && count($clients) > 0)
                    <tr class="text-center text-tbn-dark text-sm">
                        <td class="py-2" colspan="5">{{ count($clients) }} Resultados encontrados
                            <button class="text-tbn-primary underline" wire:click="$set('search', null)">
                                Volver </button>
                        </td>
                    </tr>
                @endif
                @forelse ($clients as $client)
                    <tr class="border-b hover:bg-gray-300 ">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <span class="mr-2"
                                x-html="setAccountIcon({{ $client->actived }}, {{ $client->account->account_type_id }})"></span>
                            <h5 class="inline text-lg font-medium {{ !$client->actived ? 'opacity-50' : '' }}">
                                {{ $client->name }}</h5>
                        </th>
                        <td class="px-6 py-4 {{ !$client->actived ? 'opacity-50' : '' }}">
                            {{ $client->location->location_name }}
                        </td>
                        <td class="px-6 py-4 {{ !$client->actived ? 'opacity-50' : '' }}">
                            @forelse ($client->myProfesions as $profesion)
                                <span
                                    class="inline-block px-2 py-1 rounded-sm bg-gray-200 text-black text-[.8rem] leading-4 mb-1">
                                    {{ $profesion->profesion_name }}</span>
                            @empty
                                <span class="text-sm text-gray-400">(Sin registrar)</span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4 {{ !$client->actived ? 'opacity-50' : '' }}">
                            {{ (new Carbon\Carbon($client->created_at))->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 {{ !$client->actived ? 'opacity-50' : '' }}">
                            @if ($client->account && $client->account->account_type_id != 1)
                                @if ($client->account->verified_payment)
                                    <span class="inline-block bg-tbn-primary text-white text-xs px-2 py-1 rounded-full whitespace-nowrap">
                                        {{ $client->account->accountType->name }}</span>
                                @else
                                    <span class="bg-tbn-dark text-white animate-pulse text-xs px-2 py-1 rounded-full">
                                        Pendiente</span>
                                @endif
                            @endif
                        </td>
                        <td class="flex flex-row justify-end items-center h-15 px-6 py-4 text-xl">
                            <a href="{{ route('config-client', ['id' => $client->id]) }}" wire:navigate
                                class="text-tbn-dark"><i class="fas fa-cog"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="py-4 text-center font-italic text-gray-600" colspan="5">
                            No se han encontrado datos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div> {{ $clients->links() }} </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                setAccountIcon(actived, accountId) {
                    if (actived) {
                        return parseInt(accountId) == 1 ?
                            '<i class="fas fa-leaf text-green-500"></i>' :
                            '<i class="fas fa-crown text-tbn-secondary"></i>';
                    } else {
                        return '<i class="fas fa-ban text-red-700"></i>';
                    }
                },
                confirmModal(id) {
                    console.log(id)
                }
            }))
        </script>
    @endscript
</section>
