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
        <div class="flex flex-col gap-4 mb-4 md:flex-row">
            <!-- List notices -->
            <div class="w-4/6 overflow-x-auto">
                <table class="mb-5 text-sm text-left bg-white rounded-md shadow-md dark:bg-tbn-dark rtl:text-right">
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
                                    class="px-6 py-4 font-medium max-w-60 sm:max-w-md lg:max-w-lg whitespace-wrap">
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
                <div> {{ $notices->links() }} </div>
            </div>
            <!-- Custom web images -->
            <div class="w-2/6">
                <div class="p-5 bg-white rounded-lg shadow-md dark:bg-tbn-dark">
                    <form wire:submit='saveBgImage' class="mb-6">
                        <h5 class="mb-2 text-lg font-medium text-tbn-primary">Personalizar web</h5>
                        <p class="mb-2 text-sm text-tbn-dark dark:text-tbn-light">
                            Imagen de fondo <small class="text-xs text-tbn-secondary dark:text-tbn-secondary">
                                (1500 x 1000 px)</small> </p>
                        <div class="relative group">
                            <template x-if="bgImageUrl">
                                <picture class="block mb-0 md:mb-2">
                                    <img alt="bg-image-url" class="rounded" :src="bgImageUrl">
                                </picture>
                            </template>
                            <template x-if="!bgImageUrl">
                                <picture class="block mb-0 md:mb-2">
                                    <img src="{{ $bg_web_image ? asset('storage/' . $bg_web_image->value) : 'https://placehold.co/1500x1000' }}"
                                        alt="bg-web-image" class="rounded">
                                </picture>
                            </template>
                            <label x-show="!bgImageUrl" for="bg-image-upload"
                                class="absolute flex items-center justify-center w-8 h-8 text-white transition-all duration-300 rounded-full shadow-md cursor-pointer bg-tbn-primary bottom-3 right-3 hover:bg-tbn-primary/80 hover:scale-110 active:scale-95 ring-1 ring-tbn-light">
                                <i class="text-xs fa-solid fa-pen"></i>
                                <input wire:model='bg_new_image' id="bg-image-upload" name="bg-image-upload"
                                    type="file" class="hidden" accept="image/*" x-on:change="imageBgChange" />
                            </label>
                            <x-button type="submit" x-show="bgImageUrl" class="absolute text-xs right-3 bottom-3">
                                <span wire:loading.remove wire:target='saveBgImage'>Publicar</span>
                                <span wire:loading wire:target='saveBgImage'>
                                    <i class="text-sm fas fa-spinner animate-spin"></i></span>
                            </x-button>
                        </div>
                        <x-input-error for="bg_new_image" class="mt-2" />
                    </form>
                    <form wire:submit='saveThumbImage' class="mb-6">
                        <p class="mb-2 text-sm text-tbn-dark dark:text-tbn-light">
                            Imagen del astronauta <small class="text-xs text-tbn-secondary dark:text-tbn-secondary">
                                (580 x 720 px)</small> </p>
                        <div class="relative group">
                            <template x-if="bgThumbUrl">
                                <picture class="block mb-0 md:mb-2">
                                    <img alt="bg-image-url" class="w-3/5 mx-auto" :src="bgThumbUrl">
                                </picture>
                            </template>
                            <template x-if="!bgThumbUrl">
                                <picture class="block mb-0 md:mb-2">
                                    <img src="{{ $thumb_web_image ? asset('storage/' . $thumb_web_image->value) : 'https://placehold.co/580x720' }}"
                                        alt="bg-web-image" class="w-3/5 mx-auto">
                                </picture>
                            </template>
                            <label x-show="!bgThumbUrl" for="thumb-image-upload"
                                class="absolute flex items-center justify-center w-8 h-8 text-white transition-all duration-300 rounded-full shadow-md cursor-pointer bg-tbn-primary bottom-3 right-3 hover:bg-tbn-primary/80 hover:scale-110 active:scale-95 ring-1 ring-tbn-light">
                                <i class="text-xs fa-solid fa-pen"></i>
                                <input wire:model='thumb_new_image' id="thumb-image-upload" name="thumb-image-upload"
                                    type="file" class="hidden" accept="image/*" x-on:change="imageThumbChange" />
                            </label>
                            <x-button x-show="bgThumbUrl" class="absolute text-xs right-3 bottom-3">
                                <span wire:loading.remove wire:target='saveThumbImage'>Publicar</span>
                                <span wire:loading wire:target='saveThumbImage'>
                                    <i class="text-sm fas fa-spinner animate-spin"></i></span>
                            </x-button>
                        </div>

                        <x-input-error for="thumb_new_image" class="mt-2" />
                    </form>
                </div>
            </div>
        </div>
    </div>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                modalForm: false,
                bgImageUrl: null,
                bgThumbUrl: null,
                init() {
                    $wire.on('notice-saved', () => {
                        this.modalForm = false;
                    })
                    $wire.on('image-saved', () => {
                        this.bgImageUrl = null
                        this.bgThumbUrl = null
                        Swal.fire({
                            title: "Imagen publicada",
                            confirmButtonColor: '#ff420a'
                        })
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
                },
                imageBgChange(event) {
                    this.fileToDataUrl(event, src => this.bgImageUrl = src)
                },
                imageThumbChange(event) {
                    this.fileToDataUrl(event, src => this.bgThumbUrl = src)
                },
                fileToDataUrl(event, callback) {
                    if (!event.target.files.length) return
                    let file = event.target.files[0],
                        reader = new FileReader()
                    reader.readAsDataURL(file)
                    reader.onload = e => callback(e.target.result)
                },
            }))
        </script>
    @endscript
</section>
