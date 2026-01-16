<section>
    <x-title-app>
        <x-slot name="title_page">{{ $id ? 'Editar empresa' : 'Nueva empresa' }}</x-slot>
        <x-slot name="description_page">
            {{ $id
                ? 'Actualiza la información de una empresa registrada'
                : 'Registra una nueva empresa que ofrece convocatorias de trabajo.' }}
        </x-slot>
    </x-title-app>
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2" x-data="content">
        <form wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="mb-4">
                <x-label for="company_name" value="{{ __('Nombre de la empresa') }}" />
                <x-input x-model="company_name" wire:model="company.company_name" id="company_name" type="text"
                    class="block w-full mt-1" />
                <x-input-error for="company.company_name" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="company_type_id" value="{{ __('Tipo de empresa') }}" />
                <x-select @change="setTypeName($event.target.value || 1)" wire:model="company.company_type_id"
                    id="company_type_id" class="w-full">
                    @forelse ($company_types as $company_type)
                        <option value="{{ $company_type->id }}">{{ $company_type->company_type_name }}</option>
                    @empty
                        <option>No hay opciones</option>
                    @endforelse
                </x-select>
                <x-input-error for="company.company_type_id" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="description" value="{{ __('Descripción de la empresa') }}" />
                <x-textarea class="w-full" x-model="company_description" wire:model="company.description" rows="6"
                    name="description" />
                <x-input-error for="company.description" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="company_image" value="{{ __('Logotipo de la empresa') }}" />
                <span class="text-xs text-tbn-dark dark:text-tbn-light">Utilizar una imagen de proporción 1:1
                    (recomendado 300x300px)</span>
                <input type="file" wire:model.live="company.company_image" id="company_image"
                    class="w-full mt-2 text-tbn-dark font-medium text-sm bg-white dark:bg-tbn-secondary dark:text-white file:cursor-pointer cursor-pointer file:border-0 file:py-2.5 file:px-4 file:mr-4 file:bg-tbn-primary file:hover:bg-tbn-secondary file:text-white rounded-lg file:transition-all file:duration-300"
                    accept="image/png, image/jpeg, image/jpg" @change="imageChange" />
                <x-input-error for="company.company_image" class="mt-2" />
            </div>
            <div class="mb-4">
                @if ($id)
                    <x-button>
                        <span wire:loading.remove wire:target='update'>Actualizar</span>
                        <span wire:loading wire:target='update'><i
                                class="text-sm fas fa-spinner animate-spin"></i></span>
                    </x-button>
                @else
                    <x-button>
                        <span wire:loading.remove wire:target='save'>Guardar</span>
                        <span wire:loading wire:target='save'><i class="text-sm fas fa-spinner animate-spin"></i></span>
                    </x-button>
                @endif
                <x-secondary-button type="button" href="{{ route('company') }}"
                    wire:navigate>Cancelar</x-secondary-button>
            </div>
        </form>
        <div x-show="company_name || company_type || company_description" class="hidden sm:block">
            <div class="p-5 bg-white rounded-lg shadow-md dark:bg-tbn-dark">
                <template x-if="imageUrl">
                    <picture class="block mb-0 md:mb-2">
                        <img alt="company-logo"
                            class="flex-shrink-0 object-cover object-center w-24 h-24 mb-4 rounded-lg sm:mb-0"
                            :src="imageUrl">
                    </picture>
                </template>
                <template x-if="!imageUrl">
                    <picture class="block mb-0 md:mb-2">
                        <img alt="company-logo"
                            class="flex-shrink-0 object-cover object-center w-24 h-24 mb-4 rounded-lg sm:mb-0"
                            src="{{ asset($preview_image ? 'storage/' . $preview_image : 'storage/empresas/tbn-new-default.webp') }}">
                    </picture>
                </template>
                <h5 x-text="company_name" class="mb-2 text-lg font-medium text-tbn-primary"></h5>
                <template x-if="typeText">
                    <p x-text="typeText"
                        class="inline-block px-3 py-1 mb-2 text-sm rounded-lg bg-tbn-light dark:bg-tbn-secondary dark:text-white">
                    </p>
                </template>
                <p x-text="company_description" class="text-sm text-tbn-dark dark:text-white"></p>
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                imageUrl: null,
                companyTypes: {!! $company_types !!},
                typeText: 'Pública',
                // Model
                company_name: '',
                company_type: '',
                company_description: '',
                // Functions
                init() {
                    if (@json($id)) {
                        this.company_name = $wire.company.company_name.toString()
                        this.company_description = $wire.company.description.toString()
                        this.setTypeName($wire.company.company_type_id.toString())
                    }
                },
                setTypeName(id) {
                    this.typeText = this.companyTypes.find(item => item.id == id).company_type_name
                },
                imageChange(event) {
                    this.fileToDataUrl(event, src => this.imageUrl = src)
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
