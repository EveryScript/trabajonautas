<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Noticias</x-slot>
            <x-slot name="description_page">
                Administrar las noticias más recientes del sitio web Trabajonautas.com
            </x-slot>
            <x-slot name="search_field">
                <div class="flex flex-row h-full gap-1 sm:h-10">
                    <x-button x-on:click="modalForm = true">Crear noticia</x-button>
                </div>
            </x-slot>
        </x-title-app>
        <!-- Notice modal form -->
        <div x-show="modalForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
            style="backdrop-filter: blur(5px);">
            <div class="relative w-full max-w-2xl p-8 mx-2 bg-white shadow-lg dark:bg-tbn-dark rounded-xl">
                <form wire:submit='save'>
                    <div class="mb-4">
                        <x-label for="title" value="{{ __('Titulo de la noticia') }}" />
                        <x-input wire:model="notice.title" id="title" type="text" class="block w-full mt-1"
                            placeholder="Novedades en trabajonautas.com" />
                        <x-input-error for="notice.title" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-label for="description" value="{{ __('Descripción (corta)') }}" />
                        <x-input wire:model="notice.description" id="description" type="text"
                            class="block w-full mt-1" placeholder="Acerca de la noticia" />
                        <x-input-error for="notice.description" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-label for="link" value="{{ __('Enlace') }}" />
                        <x-input wire:model="notice.link" id="link" type="text" class="block w-full mt-1"
                            placeholder="https://url.noticia.com" />
                        <x-input-error for="notice.link" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-label for="image" value="{{ __('Imagen') }}" />
                        <input type="file" wire:model.live="notice.image" id="image"
                            class="w-full mt-2 text-tbn-dark font-medium text-sm bg-white dark:bg-tbn-dark dark:text-white file:cursor-pointer cursor-pointer file:border-0 file:py-2.5 file:px-4 file:mr-4 file:bg-tbn-primary file:hover:bg-tbn-secondary file:text-white rounded-lg file:transition-all file:duration-300"
                            accept="image/png, image/jpeg, image/jpg" />
                        <x-input-error for="notice.image" class="mt-2" />
                    </div>
                    <x-button type="submit">
                        <span wire:loading.remove>Publicar</span>
                        <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
                    </x-button>
                    <x-secondary-button type="button" x-on:click="modalForm = false">Cancelar</x-secondary-button>
                </form>
            </div>
        </div>
        <!-- List notices -->
        <div class="overflow-x-auto">
            <table class="w-full mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
                <thead class="text-xs uppercase text-tbn-dark dark:text-tbn-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Noticia
                            </div>
                        </th>
                        <th scope="col" class="hidden px-6 py-3 md:table-cell">
                            <div class="flex items-center">
                                Enlace
                            </div>
                        </th>
                        <th scope="col" class="hidden px-6 py-3 md:table-cell">
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
                    @forelse ($notices as $notice)
                        <tr wire:key="{{ $notice->id }}"
                            class="border-b dark:border-b-tbn-secondary text-tbn-secondary dark:text-tbn-light hover:bg-gray-300 dark:hover:bg-neutral-900">
                            <th scope="row"
                                class="px-6 py-4 font-medium max-w-60 sm:max-w-md lg:max-w-2xl whitespace-wrap">
                                <div class="flex flex-row gap-3">
                                    <img src="{{ asset('storage/' . $notice->image) }}" alt="logo"
                                        class="flex-shrink-0 object-cover object-center w-10 h-10 mb-4 rounded-lg sm:mb-0">
                                    <div class="truncate">
                                        <h5 class="font-bold text-md dark:text-white">{{ $notice->title }}</h5>
                                        <span
                                            class="text-sm font-normal text-tbn-secondary dark:text-tbn-light">{{ $notice->description }}</span>
                                    </div>
                                </div>
                            </th>
                            <td class="hidden px-6 py-4 md:table-cell">
                                <a class="text-sm underline text-tbn-primary" href="{{ $notice->link }}"
                                    target="_blank">Ir
                                    al enlace</a>
                            </td>
                            <td class="hidden px-6 py-4 md:table-cell">
                                {{ \Carbon\Carbon::parse($notice->updated_at)->diffForHumans() }}
                            </td>
                            <td class="flex flex-row items-center justify-end px-6 py-4 text-lg h-15">
                                <a x-on:click="confirmModal({{ $notice->id }})"
                                    class="font-medium transition-colors duration-150 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                    <i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b dark:bg-tbn-dark dark:border-b-tbn-secondary">
                            <td class="py-4 text-center font-italic text-tbn-secondary dark:text-tbn-light"
                                colspan="4">
                                No se han encontrado datos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div> {{ $notices->links() }} </div>
    </div>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                modalForm: false,
                init() {
                    $wire.on('notice-saved', () => {
                        this.modalForm = false;
                    })
                },
                confirmModal(id) {
                    Swal.fire({
                        title: "¿Eliminar noticia?",
                        text: "Esta noticia ya no se mostrará en el sitio web Trabajoanautas.com",
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
