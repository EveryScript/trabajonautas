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
        <form class="tbn-form max-w-4xl mb-10" wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="mb-4">
                <x-label for="announce_title" value="{{ __('Título') }}" />
                <x-input wire:model="announcement.announce_title" id="announce_title" type="text"
                    class="mt-1 block w-full" />
                <x-input-error for="announcement.announce_title" class="mt-2" />
            </div>
            <div class="tbn-field mb-4">
                <x-label for="company" value="{{ __('Empresa') }}" />
                <div wire:ignore>
                    <x-select id="company" wire:model="announcement.company_id">
                        <option>Seleccionar empresa</option>
                        @forelse ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @empty
                            <option>No option avaliable</option>
                        @endforelse
                    </x-select>
                </div>
                <a class="font-medium text-tbn-primary text-sm hover:underline cursor-pointer mr-3"
                    href="{{ route('new-company') }}" wire:navigate> Agregar empresa</a>
                <x-input-error for="announcement.company_id" class="mt-2" />
            </div>
            <div class="tbn-field mb-4">
                <x-label for="area" value="{{ __('Area profesional') }}" />
                <div wire:ignore>
                    <x-select id="area" wire:model="announcement.area_id">
                        <option>Seleccionar areas</option>
                        @forelse ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                        @empty
                            <option>No hay opciones para mostrar</option>
                        @endforelse
                    </x-select>
                </div>
                <x-input-error for="announcement.area_id" class="mt-2" />
            </div>
            <div class="tbn-field mb-4">
                <x-label for="profesions" value="{{ __('Profesiones') }}" />
                <div wire:ignore>
                    <x-select id="profesions" wire:model="announcement.profesions" multiple>
                        <option>Seleccionar profesiones</option>
                        @forelse ($profesions as $profesion)
                            <option value="{{ $profesion->id }}">{{ $profesion->profesion_name }}</option>
                        @empty
                            <option>No hay opciones para mostrar</option>
                        @endforelse
                    </x-select>
                </div>
                <x-input-error for="announcement.profesions" class="mt-2" />
            </div>
            <div class="tbn-field mb-4">
                <x-label for="locations" value="{{ __('Ubicaciones') }}" />
                <div wire:ignore>
                    <x-select id="locations" wire:model="announcement.locations" multiple>
                        <option>Seleccionar ubicaciones</option>
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
                <x-label for="announce_file" value="{{ __('Archivos de la convocatoria') }}" />
                <input wire:model="announcement.announce_files"
                    class="w-full mt-2 text-tbn-dark font-medium text-sm bg-white file:cursor-pointer cursor-pointer file:border-0 file:py-2 file:px-4 file:mr-4 file:bg-tbn-primary file:hover:bg-tbn-dark file:text-white rounded"
                    id="announce_files" type="file" multiple accept="image/*,.pdf,.docx" />
                <x-input-error for="announcement.announce_files.*" class="mt-2" />
                @if ($announcement->announce_urls)
                    <div class="mt-2 flex flex-row gap-2 text-xl">
                        @foreach ($announcement->announce_urls as $url)
                            @switch(explode('.', $url)[1])
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
                                    <i class="fas fa-file mr-2"></i>
                            @endswitch
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="flex-grow flex gap-2 mb-4">
                <div class="w-1/2">
                    <x-label for="expiration_time" value="{{ __('Expiración') }}" />
                    <x-input wire:model="announcement.expiration_time" id="expiration_time" type="datetime-local"
                        class="mt-1 block w-full" min="{{ now()->format('Y-m-d\TH:i') }}" />
                    <x-input-error for="announcement.expiration_time" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="salary" value="{{ __('Sueldo') }}" />
                    <x-input wire:model="announcement.salary" id="salary" type="number" class="mt-1 block w-full" />
                    <x-input-error for="announcement.salary" class="mt-2" />
                </div>
            </div>
            <div class="tbn-field mb-4">
                <x-label for="description" class="mb-2" value="{{ __('Descripción/Detalles de la convocatoria') }}" />
                <div wire:ignore>
                    <div class="block w-full bg-white" id="description"></div>
                </div>
                <x-input-error for="announcement.description" class="mt-2" />
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="announcement.pro" class="sr-only peer" id="pro"
                        {{ $announcement->pro ? 'checked' : '' }}>
                    <div
                        class="relative w-14 h-7 bg-gray-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-tbn-primary">
                    </div>
                    <div class="ms-3">
                        <p class="text-md font-medium text-black">Convocatoria PRO</p>
                        <span class="text-xs text-tbn-dark">
                            Esta convocatoria es exclusiva para los usuarios que tengan una cuenta PRO. El resto de
                            usuarios no podrán ver esta convocatoria.</span>
                    </div>
                </label>
                <x-input-error for="announcement.pro" class="mt-2" />
            </div>
            <div>
                <x-button>{{ $id ? 'Actualizar' : 'Guardar' }}</x-button>
                <x-secondary-button type="button" href="{{ route('announcement') }}"
                    wire:navigate>Cancelar</x-secondary-button>
            </div>
        </form>
        <!-- Main modal -->
        <div class="fixed z-10 inset-0 overflow-y-auto" id="modal-share" x-show="modalPreview" x-cloak
            x-transition.fade>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-60"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-8 py-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    @click.away="modalPreview = false">
                    <img :src="previewUrl" alt="image">
                </div>
            </div>
        </div>
    </div>

    @assets
        <!-- Tom Select -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <!-- Quill Editor -->
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    @endassets

    @script
        <script>
            new TomSelect('#area', []);
            new TomSelect('#locations', []);
            new TomSelect('#profesions', []);
            new TomSelect('#company', {
                maxItems: 1
            });

            const quill = new Quill('#description', {
                theme: 'snow'
            });
            quill.on('text-change', (range, oldRange, source) => {
                $wire.announcement.description = source == 'user' ? quill.root.innerHTML : ''
            });

            if (@json($id)) {
                quill.root.innerHTML = $wire.announcement.description
                document.querySelector('#company').tomselect.setValue($wire.announcement.company_id);
                document.querySelector('#locations').tomselect.setValue($wire.announcement.locations);
                document.querySelector('#profesions').tomselect.setValue($wire.announcement.profesions);
                document.querySelector('#area').tomselect.setValue($wire.announcement.area_id);
            }

            Alpine.data('content', () => ({
                init() {
                    $wire.on('job-launched', () => {
                        alert('Cron job launched successfully :)')
                    })
                },
                modalPreview: false,
                previewUrl: null
            }))
        </script>
    @endscript
</section>
