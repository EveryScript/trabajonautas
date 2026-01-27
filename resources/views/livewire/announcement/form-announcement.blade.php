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
                <x-input wire:model="announcement.announce_title" id="announce_title" type="text"
                    class="w-full mt-1 dark:bg-tbn-dark dark:text-white" />
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
            <div class="flex-grow block gap-2 mb-4 sm:flex">
                <div class="w-full sm:w-1/2">
                    <x-label for="area">Area profesional
                        <span class="font-light">(sugerencias de cliente)</span></x-label>
                    <div class="mt-1 tbn-tom-select" wire:ignore>
                        <x-select id="area" wire:model="announcement.area_id">
                            <option></option>
                            @forelse ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                            @empty
                                <option>No hay opciones para mostrar</option>
                            @endforelse
                        </x-select>
                    </div>
                    <x-input-error for="announcement.area_id" class="mt-2" />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-label for="area">Areas (añadir profesiones)</x-label>
                    <x-select class="mt-1 w-full h-[3.2rem]" x-on:change="onAreaChange" id="areas">
                        <option>Selecciona un area</option>
                        <template x-for="area in areas">
                            <option :key="'area-' + location.id" :value="area.id">
                                + <span x-text="area.area_name" class="block text-sm font-medium"></span>
                            </option>
                        </template>
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
            <div class="flex-grow mb-4">
                <x-label for="announce_file">Archivos de la convocatoria</x-label>
                <input wire:model="announcement.announce_files"
                    class="w-full mt-1 text-tbn-dark font-medium text-sm bg-white dark:bg-tbn-secondary dark:text-white file:cursor-pointer cursor-pointer file:border-0 file:py-2.5 file:px-4 file:mr-4 file:bg-tbn-primary file:hover:bg-tbn-dark file:text-white rounded-lg file:transition-all file:duration-300"
                    id="announce_files" type="file" multiple accept="image/*,.pdf,.docx" />
                <x-input-error for="announcement.announce_files.*" class="mt-2" />
                @if ($announcement->announce_urls)
                    <div class="flex flex-row gap-2 mt-2 text-xl">
                        @foreach ($announcement->announce_urls as $url)
                            @switch(pathinfo($url, PATHINFO_EXTENSION))
                                @case('png')
                                    <x-button-link
                                        x-on:click="modalPreview = true; previewUrl = '{{ asset('storage/' . $url) }}'"
                                        class="bg-tbn-primary">
                                        <i class="fas fa-file-image"></i>
                                    </x-button-link>
                                @break

                                @case('jpg')
                                    <x-button-link
                                        x-on:click="modalPreview = true; previewUrl = '{{ asset('storage/' . $url) }}'"
                                        class="bg-tbn-primary">
                                        <i class="fas fa-file-image"></i>
                                    </x-button-link>
                                @break

                                @case('pdf')
                                    <x-button-link href="{{ asset('storage/' . $url) }}" class="bg-tbn-primary">
                                        <i class="fas fa-file-pdf"></i>
                                    </x-button-link>
                                @break

                                @case('docx')
                                    <x-button-link href="{{ asset('storage/' . $url) }}" class="bg-tbn-primary">
                                        <i class="fas fa-file-word"></i>
                                    </x-button-link>
                                @break

                                @default
                                    <i class="mr-2 fas fa-file"></i>
                            @endswitch
                        @endforeach
                    </div>
                @endif
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
            <div class="mb-4">
                <x-input-checkbox-block checked="{{ $announcement->pro ? 'checked' : '' }}"
                    wire:model="announcement.pro">
                    <div class="ms-4">
                        <p class="font-medium text-black text-md dark:text-tbn-primary">Convocatoria PRO</p>
                        <p class="text-xs text-tbn-dark dark:text-white">
                            Esta convocatoria es exclusiva para los clientes que tengan una cuenta PRO o PRO-MAX. Se
                            enviará una
                            <strong class="dark:text-tbn-primary">notificación</strong> a los clientes PRO-MAX cuando
                            guarde o actualice esta
                            convocatoria.
                        </p>
                    </div>
                </x-input-checkbox-block>
                <x-input-error for="announcement.pro" class="mt-2" />
            </div>
            <div>
                @if ($id)
                    <x-button>
                        <span wire:loading.remove wire:target='update'>Actualizar</span>
                        <span wire:loading wire:target='update'><i
                                class="text-sm fas fa-spinner animate-spin"></i></span>
                    </x-button>
                @else
                    <x-button>
                        <span wire:loading.remove wire:target='save'>Publicar</span>
                        <span wire:loading wire:target='save'><i
                                class="text-sm fas fa-spinner animate-spin"></i></span>
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
            new TomSelect('#area', {
                plugins: ['remove_button']
            });
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
                document.querySelector('#area').tomselect.setValue($wire.announcement.area_id);
            }

            Alpine.data('content', () => ({
                modalPreview: false,
                previewUrl: null,
                profesions: @json($profesions),
                locations: @json($locations),
                areas: @json($areas),
                profesionsSelectedIds: [],
                salary: @json($id) ? $wire.announcement.salary : '',
                init() {
                    flatpickr("#expiration_time", {
                        defaultDate: @json($id) ? $wire.announcement.expiration_time :
                            'today',
                        enableTime: true,
                        minDate: "today",
                        time_24hr: false,
                        dateFormat: "Y-m-d H:i",
                        "locale": "es"
                    });
                },
                // Set profesions base on area selected
                onAreaChange(event) {
                    const areaId = event.target.value;
                    const profesionsSelected = this.profesions.filter(profesion => profesion.area_id == areaId)
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
