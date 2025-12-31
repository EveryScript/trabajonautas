<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Noticias</x-slot>
            <x-slot name="description_page">
                Administrar las noticias más recientes del sitio web Trabajonautas.com
            </x-slot>
            <x-slot name="search_field">
                <x-button x-on:click="modalForm = true">Crear noticia</x-button>
            </x-slot>
        </x-title-app>
        <!-- Notice modal form -->
        <div x-show="modalForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
            style="backdrop-filter: blur(5px);">
            <div class="bg-white rounded-xl shadow-lg p-8 mx-2 max-w-2xl w-full relative">
                <form wire:submit='save'>
                    <div class="mb-4">
                        <x-label for="title" value="{{ __('Titulo de la noticia') }}" />
                        <x-input wire:model="notice.title" id="title" type="text" class="mt-1 block w-full"
                            placeholder="Novedades en trabajonautas.com" />
                        <x-input-error for="notice.title" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-label for="description" value="{{ __('Descripción (corta)') }}" />
                        <x-input wire:model="notice.description" id="description" type="text"
                            class="mt-1 block w-full" placeholder="Acerca de la noticia" />
                        <x-input-error for="notice.description" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-label for="link" value="{{ __('Enlace') }}" />
                        <x-input wire:model="notice.link" id="link" type="text" class="mt-1 block w-full"
                            placeholder="https://url.noticia.com" />
                        <x-input-error for="notice.link" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-label for="image" value="{{ __('Imagen') }}" />
                        <input type="file" wire:model.live="notice.image" id="image"
                            class="w-full mt-2 text-tbn-dark font-medium text-sm bg-white file:cursor-pointer cursor-pointer file:border-0 file:py-2 file:px-4 file:mr-4 file:bg-tbn-primary file:hover:bg-tbn-dark file:text-white rounded"
                            accept="image/png, image/jpeg, image/jpg" />
                        <x-input-error for="notice.image" class="mt-2" />
                    </div>
                    <x-button type="submit">
                        <span wire:loading.remove>Publicar</span>
                        <span wire:loading><i class="fas fa-spinner text-sm animate-spin"></i></span>
                    </x-button>
                    <x-secondary-button type="button" x-on:click="modalForm = false">Cancelar</x-secondary-button>
                </form>
            </div>
        </div>
        <!-- List notices -->
        <table class="w-full bg-white rounded-md shadow-md mb-5 text-sm text-left rtl:text-right">
            <thead class="text-xs uppercase text-tbn-dark">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Noticia
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Enlace
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
                @forelse ($notices as $notice)
                    <tr wire:key="{{ $notice->id }}" class="border-b hover:bg-gray-300">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-wrap">
                            <div class="flex flex-row gap-3">
                                <img src="{{ asset('storage/' . $notice->image) }}" alt="notice-image"
                                    class="flex-shrink-0 rounded-lg w-10 h-10 object-cover object-center sm:mb-0 mb-4">
                                <div class="max-w-xs truncate">
                                    <h5 class="text-md font-bold">{{ $notice->title }}</h5>
                                    <span class="text-sm text-gray-600 font-normal">{{ $notice->description }}</span>
                                </div>
                            </div>
                        </th>
                        <td class="px-6 py-4">
                            <a class="text-tbn-primary underline text-sm" href="{{ $notice->link }}" target="_blank">Ir
                                al enlace</a>
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($notice->updated_at)->diffForHumans() }}
                        </td>
                        <td class="flex flex-row justify-end items-center h-15 px-6 py-4 text-lg">
                            <a x-on:click="confirmModal({{ $notice->id }})"
                                class="font-medium text-tbn-primary hover:underline cursor-pointer">
                                <i class="far fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td class="py-4 text-center font-italic text-gray-600" colspan="4">
                            No se han encontrado datos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
