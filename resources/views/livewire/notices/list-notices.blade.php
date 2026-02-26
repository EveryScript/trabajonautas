<section>
    <div x-data="content">
        <x-title-app>
            <x-slot name="title_page">Noticias</x-slot>
            <x-slot name="description_page">
                Administrar las noticias más recientes del sitio web Trabajonautas.com
            </x-slot>
            <x-slot name="search_field">
                <div class="flex flex-row h-full gap-1 sm:h-10">
                    <x-button x-on:click="openNoticeForm">Crear noticia</x-button>
                    <x-button x-on:click="openSkinForm">Configurar portada</x-button>
                </div>
            </x-slot>
        </x-title-app>
        <!-- List Notices table -->
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
                    <th scope="col" class="px-6 py-3 text-right">
                        Opciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notices as $notice)
                    <tr wire:key="notice-{{ $notice->id }}"
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
                            <a class="text-sm underline text-tbn-primary" href="{{ $notice->link }}" target="_blank">Ir
                                al enlace</a>
                        </td>
                        <td class="flex flex-row items-center justify-end px-6 py-4 text-lg h-15">
                            <a x-on:click="confirmDelete({{ $notice->id }})"
                                class="font-medium transition-colors duration-150 cursor-pointer text-tbn-primary hover:text-tbn-secondary">
                                <i class="far fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b dark:bg-tbn-dark dark:border-b-tbn-secondary">
                        <td class="py-4 text-center font-italic text-tbn-secondary dark:text-tbn-light" colspan="4">
                            No se han encontrado datos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div> {{ $notices->links() }} </div>
        <!-- Notice modal form -->
        <template x-if="noticeForm">
            <livewire:notices.form-notices />
        </template>
        <!-- Skin modal form -->
        <template x-if="skinForm">
            <livewire:notices.form-skin />
        </template>
    </div>

    @script
        <script>
            Alpine.data('content', () => ({
                noticeForm: false,
                skinForm: false,
                init() {
                    $wire.on('notice-saved', () => {
                        this.closeNoticeForm()
                        Swal.fire({
                            title: "Noticia guardada",
                            text: "La noticia se ha publicado correctamente",
                            confirmButtonText: "Listo",
                            confirmButtonColor: '#ff420a'
                        })
                    })
                    $wire.on('skin-saved', () => {
                        this.closeSkinForm()
                        Swal.fire({
                            title: "Imagenes guardadas",
                            text: "La portada del sitio web se ha guardado correctamente.",
                            confirmButtonText: "Listo",
                            confirmButtonColor: '#ff420a'
                        })
                    })
                },
                confirmDelete(id) {
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
                openNoticeForm() {
                    this.noticeForm = true
                },
                closeNoticeForm() {
                    this.noticeForm = false
                },
                openSkinForm() {
                    this.skinForm = true
                },
                closeSkinForm() {
                    this.skinForm = false
                }
            }))
        </script>
    @endscript
</section>
