<section>
    <x-title-app>
        <x-slot name="title_page">{{ $id ? 'Editar convocatoria' : 'Nueva convocatoria' }}</x-slot>
        <x-slot name="description_page">
            {{ $id
                ? 'Actualiza la información de una convocatoria registrada'
                : 'Registra la información para una nueva convocatoria en la base de datos de Trabajonautas' }}
        </x-slot>
    </x-title-app>
    <div x-data="content">
        <form class="max-w-4xl mb-10 tbn-form" wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="mb-4">
                <x-label for="announce_title">Título de la convocatoria</x-label>
                <x-textarea class="w-full" wire:model="announcement.announce_title" rows="3" name="announce_title" />
                <x-input-error for="announcement.announce_title" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="company">Empresa</x-label>
                <div class="mt-1 tbn-tom-select" wire:ignore>
                    <x-select class=" dark:bg-tbn-dark dark:text-white" id="company"
                        wire:model="announcement.company_id">
                        <option></option>
                        @forelse ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @empty
                            <option>No option avaliable</option>
                        @endforelse
                    </x-select>
                </div>
                <x-input-error for="announcement.company_id" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="area"><span class="font-bold">Área profesional</span> (añadir profesiones) </x-label>
                <div class="mt-1 tbn-tom-select" wire:ignore>
                    <x-select id="area">
                        <option></option>
                        @forelse ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                        @empty
                            <option>No hay opciones para mostrar</option>
                        @endforelse
                    </x-select>
                </div>
            </div>
            <div class="mb-4">
                <x-label for="profesions">Profesiones <button x-on:click="clearProfesionsSelected" type="button"
                        class="inline-block ml-2 text-sm underline text-tbn-primary">
                        Limpiar</button></x-label>
                <div class="mt-1 tbn-tom-select" wire:ignore>
                    <x-select id="profesions" wire:model="announcement.profesions" multiple>
                        @forelse ($profesions as $profesion)
                            <option value="{{ $profesion->id }}">{{ $profesion->profesion_name }}</option>
                        @empty
                            <option>No hay opciones para mostrar</option>
                        @endforelse
                    </x-select>
                </div>
                <x-input-error for="announcement.profesions" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="locations"> Ubicaciones
                    <button x-on:click="setAllLocations" type="button"
                        class="inline-block ml-2 text-sm underline text-tbn-primary">
                        Añadir toda Bolivia</button> </x-label>
                <div class="mt-1 tbn-tom-select" wire:ignore>
                    <x-select class="tbn-tom-select" id="locations" wire:model="announcement.locations" multiple>
                        @forelse ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                        @empty
                            <option>No hay opciones para mostrar</option>
                        @endforelse
                    </x-select>
                </div>
                <x-input-error for="announcement.locations" class="mt-2" />
            </div>
            @if ($announcement->current_files && count($announcement->current_files) > 0)
                <div class="flex-grow mb-4">
                    <!-- Current files uploaded -->
                    <x-label for="current">Archivos actuales</x-label>
                    <div class="flex flex-row gap-2 mt-2 text-xl">
                        @foreach ($announcement->current_files as $file)
                            @php
                                $extension = pathinfo($file->url, PATHINFO_EXTENSION);
                                $isImage = in_array($extension, ['png', 'jpg', 'jpeg']);
                                $fileUrl = asset('storage/' . $file->url);

                                $icon = match ($extension) {
                                    'png', 'jpg', 'jpeg' => 'fa-file-image',
                                    'pdf' => 'fa-file-pdf',
                                    'docx' => 'fa-file-word',
                                    'xls' => 'fa-file-excel',
                                    'xlsx' => 'fa-file-excel',
                                    'xlsm' => 'fa-file-excel',
                                    'csv' => 'fa-file-excel',
                                    default => 'fa-file',
                                };
                            @endphp

                            <div class="relative group" x-transition:leave="transition ease-in duration-300">
                                <div
                                    class="px-8 py-4 transition border rounded cursor-pointer border-tbn-dark dark:border-tbn-light text-tbn-dark dark:text-tbn-light hover:border-tbn-primary hover:text-tbn-primary">
                                    @if ($isImage)
                                        <i class="text-3xl fas {{ $icon }}"
                                            x-on:click="modalPreview = true; previewUrl = '{{ $fileUrl }}'"></i>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank">
                                            <i class="text-3xl fas {{ $icon }}"></i>
                                        </a>
                                    @endif
                                </div>
                                <!-- Delete button -->
                                <button type="button" wire:click="deleteCurrentFile({{ $file->id }})"
                                    class="absolute flex items-center justify-center w-6 h-6 text-white transition rounded-full opacity-0 bg-tbn-primary -top-1 -right-1 group-hover:opacity-100">
                                    <i class="text-xs fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="flex-grow mb-4">
                <x-label
                    for="announce_file">{{ $id ? 'Añadir archivos a la convocatoria' : 'Archivos de la convocatoria' }}</x-label>
                <x-filepond wire:model="announcement.announce_files" multiple />
                <x-input-error for="announcement.announce_files.*" class="mt-2" />
            </div>
            <div class="flex-grow block gap-2 mb-4 sm:flex">
                <div class="w-full sm:w-1/2">
                    <x-label for="expiration_time">Expiración</x-label>
                    <x-input type="text" wire:model="announcement.expiration_time" id="expiration_time"
                        class="block w-full mt-1" placeholder="Definir fecha" />
                    <x-input-error for="announcement.expiration_time" class="mt-2" />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-label for="salary">Sueldo</x-label>
                    <x-input wire:model="announcement.salary" id="salary" type="text"
                        x-mask:dynamic="$money($input.replace(/[^\d]/g), '.', ',')" class="block w-full mt-1" />
                    <span class="text-xs text-tbn-dark dark:text-tbn-light">
                        "0" = sueldo no declarado por la institución.</span>
                    <x-input-error for="announcement.salary" class="mt-2" />
                </div>
            </div>
            <div class="mb-4">
                <x-label for="description" class="mb-2">Descripción / Detalles de la convocatoria</x-label>
                <div class="tbn-quill-editor" wire:ignore>
                    <div class="block w-full" id="description"></div>
                </div>
                <x-input-error for="announcement.description" class="mt-2" />
            </div>
            <!-- Announcement PRO -->
            <div class="mb-4">
                <x-input-checkbox-block x-model="isProAnnounce" checked="{{ $announcement->pro ? 'checked' : '' }}"
                    wire:model="announcement.pro">
                    <div class="divide-y ms-6 divide-tbn-secondary">
                        <div class="w-full mb-2">
                            <p class="font-medium text-black text-md dark:text-tbn-primary">Convocatoria PRO</p>
                            <p class="text-xs text-tbn-dark dark:text-white">
                                Esta convocatoria es exclusiva para los clientes que tengan una cuenta PRO o PRO-MAX. Se
                                enviará una
                                <strong class="dark:text-tbn-primary">notificación</strong> a los clientes PRO-MAX
                                cuando
                                guarde o actualice esta
                                convocatoria.
                            </p>
                        </div>
                        <div x-show="isProAnnounce" class="w-full pt-2 md:w-1/2">
                            <x-label for="scheduled_at">Programar notificaciones</x-label>
                            <x-input type="text" wire:model="announcement.scheduled_at" id="scheduled_at"
                                placeholder="Definir fecha y hora" />
                            <x-input-error for="announcement.scheduled_at" class="mt-2" />
                        </div>
                    </div>
                </x-input-checkbox-block>
                <x-input-error for="announcement.pro" class="mt-2" />
            </div>
            <div>
                @if ($id)
                    <x-button wire:loading.attr="disabled" wire:target='update' type="submit">
                        <span wire:loading.remove wire:target='update'>Actualizar</span>
                        <span wire:loading wire:target='update'>Actualizando...</span>
                    </x-button>
                @else
                    <x-button wire:loading.attr="disabled" wire:target='save' type="submit">
                        <span wire:loading.remove wire:target='save'>Publicar</span>
                        <span wire:loading wire:target='save'>Publicando...</span>
                    </x-button>
                @endif
                <x-secondary-button type="button" href="{{ route('announcement') }}"
                    wire:navigate>Cancelar</x-secondary-button>
            </div>
        </form>
        <!-- Main modal -->
        <div class="fixed inset-0 z-10 overflow-y-auto" id="modal-share" x-show="modalPreview" x-cloak
            x-transition.fade>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-60"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-8 py-6 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    @click.away="modalPreview = false">
                    <img :src="previewUrl" alt="image">
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            // Tom Select
            new TomSelect('#locations', {
                plugins: ['remove_button']
            });
            new TomSelect('#profesions', {
                plugins: ['remove_button']
            });
            new TomSelect('#company', {
                plugins: ['remove_button']
            });
            // Quill Editor (Description)
            const quill = new Quill('#description', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike', 'link']
                    ]
                }
            });
            quill.on('text-change', (range, oldRange, source) => {
                $wire.announcement.description = source == 'user' ? quill.root.innerHTML : ''
            });

            // Set edit values
            if (@json($id)) {
                quill.root.innerHTML = $wire.announcement.description
                document.querySelector('#company').tomselect.setValue($wire.announcement.company_id);
                document.querySelector('#locations').tomselect.setValue($wire.announcement.locations);
                document.querySelector('#profesions').tomselect.setValue($wire.announcement.profesions);
            }

            Alpine.data('content', () => ({
                modalPreview: false,
                previewUrl: null,
                ts_area: null,
                profesions: @json($profesions),
                locations: @json($locations),
                areas: @json($areas),
                isProAnnounce: @json($id) ? $wire.announcement.pro : false,
                profesionsSelectedIds: @json($id) ? $wire.announcement.profesions : [],
                salary: @json($id) ? $wire.announcement.salary : '',
                init() {
                    console.log($wire.announcement.profesions)
                    this.ts_area = new TomSelect('#area', {
                        onChange: (value) => {
                            this.onAreaChange(value)
                        }
                    })
                    flatpickr("#expiration_time", {
                        defaultDate: @json($id) ? $wire.announcement.expiration_time :
                            'today',
                        enableTime: true,
                        minDate: "today",
                        time_24hr: false,
                        dateFormat: "Y-m-d H:i",
                        "locale": "es"
                    });
                    flatpickr("#scheduled_at", {
                        defaultDate: @json($id) ? $wire.announcement.scheduled_at :
                            'today',
                        enableTime: true,
                        minDate: "today",
                        time_24hr: true,
                        dateFormat: "Y-m-d H:i",
                        "locale": "es"
                    });
                },
                // Set profesions base on area selected
                onAreaChange(areaId) {
                    const profesionsSelected = this.profesions.filter(p => Number(p.area_id) === Number(areaId))
                    const selectedIds = profesionsSelected.map(p => p.id)
                    this.profesionsSelectedIds = [...new Set([...this.profesionsSelectedIds, ...selectedIds])];
                    document.querySelector('#profesions').tomselect.setValue(this.profesionsSelectedIds)
                },
                // Set all locations
                setAllLocations() {
                    const allLocations = this.locations.map(locations => locations.id)
                    document.querySelector('#locations').tomselect.clear()
                    document.querySelector('#locations').tomselect.setValue(allLocations)
                },
                // Clear profesions selected
                clearProfesionsSelected() {
                    this.profesionsSelectedIds = []
                    document.querySelector('#profesions').tomselect.clear()
                }
            }))
        </script>
    @endscript
</section>
