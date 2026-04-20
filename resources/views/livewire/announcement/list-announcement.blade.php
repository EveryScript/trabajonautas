<section>
    <x-title-app>
        <x-slot name="title_page">Convocatorias</x-slot>
        <x-slot name="description_page">
            Todas las convocatorias de trabajo registradas en el portal de empleos Trabajonautas.com
        </x-slot>
        <x-slot name="search_field">
            <div class="flex flex-row flex-1 h-full gap-1 sm:h-10">
                <div wire:ignore class="flex-1 tbn-tom-select">
                    <select id="announcement-search">
                        @foreach ($profesions as $profesion)
                            <option value="{{ $profesion->profesion_name }}">{{ $profesion->profesion_name }}</option>
                        @endforeach
                        @foreach ($locations as $location)
                            <option value="{{ $location->location_name }}">{{ $location->location_name }}</option>
                        @endforeach
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_name }}">{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-button type="button" class="h-[3.2rem]" href="{{ route('new-announcement') }}"
                    wire:navigate>Nuevo</x-button>
            </div>
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <div class="overflow-x-auto">
            <table class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
                <thead class="text-xs uppercase text-tbn-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Convocatoria
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PRO
                        </th>
                        <th scope="col" class="hidden px-6 py-3 lg:table-cell">
                            Ubicación
                        </th>
                        <th scope="col" class="hidden px-6 py-3 lg:table-cell">
                            Empresa
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody wire:loading.class='opacity-40' class="divide-y divide-tbn-secondary dark:divide-tbn-secondary">
                    @if ($search)
                        <tr class="text-sm text-center bg-gray-200 text-tbn-dark">
                            <td class="px-6 py-2" colspan="5">
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
                    @forelse ($announcements as $announcement)
                        <tr wire:key='{{ $announcement->id }}' class="hover:bg-gray-300 dark:hover:bg-neutral-900">
                            <th scope="row "
                                class="px-6 py-4 font-medium max-w-60 sm:max-w-md lg:max-w-lg whitespace-wrap">
                                <h5 class="mb-1 font-bold truncate text-md dark:text-white">
                                    {{ $announcement->announce_title }}
                                </h5>
                                <p class="text-xs font-normal text-tbn-dark dark:text-tbn-light">
                                    <!-- Scheduled at -->
                                    @php
                                        $scheduled_time_left = Carbon\Carbon::parse($announcement->scheduled_at);
                                    @endphp
                                    @if ($scheduled_time_left->isFuture())
                                        <span class="text-xs font-light text-tbn-primary">
                                            <i class="inline ml-2 mr-1 fa-solid fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($announcement->scheduled_at)->format('d-m-Y H:i') }}
                                        </span>
                                    @endif
                                    <!-- Expired -->
                                    @if ($announcement->expiration_time < now())
                                        <span class="text-xs font-light text-tbn-primary">(expirada)</span>
                                    @endif
                                </p>
                            </th>
                            <td class="px-6 py-4 dark:text-tbn-light">
                                @if ($announcement->pro)
                                    <i class="text-xs fas fa-crown text-tbn-primary"></i>
                                @endif
                            </td>
                            <td class="hidden px-6 py-4 dark:text-tbn-light lg:table-cell">
                                @if ($announcement->locations->count() === $total_locations)
                                    <span
                                        class="inline-block px-2 py-1 dark:bg-tbn-secondary border border-tbn-primary rounded-md text-[.8rem] leading-4 mb-1">
                                        Toda Bolivia</span>
                                @else
                                    @forelse ($announcement->locations as $location)
                                        <span
                                            class="inline-block px-2 py-1 dark:bg-tbn-secondary rounded-md text-[.8rem] leading-4 mb-1">
                                            {{ $location->location_name }}</span>
                                    @empty
                                        <span class="text-sm">Sin ubicaciones</span>
                                    @endforelse
                                @endif
                            </td>
                            <td class="hidden px-6 py-4 dark:text-tbn-light lg:table-cell">
                                {{ $announcement->company ? $announcement->company->company_name : '(Empresa eliminada)' }}
                            </td>
                            <td class="flex flex-row items-center justify-end h-20 px-6 py-4 text-lg">
                                <a href="{{ route('result', ['id' => $announcement->id]) }}" target="_blank"
                                    class="mr-3 font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                    <i class="far fa-eye"></i></a>
                                <a href="{{ route('new-announcement', ['id' => $announcement->id]) }}" wire:navigate
                                    class="mr-3 font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                    <i class="far fa-edit"></i></a>
                                <a x-on:click="confirmModal({{ $announcement->id }})"
                                    class="font-medium transition-colors duration-300 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                    <i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white dark:bg-tbn-dark">
                            <td class="py-4 italic text-center text-tbn-secondary" colspan="5">
                                No se han encontrado datos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div> {{ $announcements->links() }} </div>
    </div>

    @script
        <script>
            Alpine.data('content', () => ({
                search_ts: null,
                init() {
                    this.search_ts = new TomSelect('#announcement-search', {
                        create: true,
                        placeholder: 'Título, profesión, ubicación',
                        allowEmptyOption: true,
                        onChange: (value) => {
                            $wire.set('search', value || null)
                        }
                    })
                },
                destroy() {
                    if (this.search_ts)
                        this.search_ts.destroy();
                },
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
