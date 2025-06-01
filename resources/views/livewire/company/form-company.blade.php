<section>
    <x-title-app>
        <x-slot name="title_page">{{ $id ? 'Editar empresa' : 'Nueva empresa' }}</x-slot>
        <x-slot name="description_page">
            {{ $id
                ? 'Actualiza la información de una empresa registrada'
                : 'Registra una nueva empresa que ofrece convocatorias de trabajo.' }}
        </x-slot>
    </x-title-app>
    <div class="grid grid-cols-2 gap-8" x-data="content">
        <form wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="mb-4">
                <x-label for="company_name" value="{{ __('Nombre de la empresa') }}" />
                <x-input x-model="company_name" wire:model="company.company_name" id="company_name" type="text"
                    class="mt-1 block w-full" />
                <x-input-error for="company.company_name" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="company_type_id" value="{{ __('Tipo de empresa') }}" />
                <x-select @change="setTypeName($event.target.value || 1)" wire:model="company.company_type_id"
                    id="company_type_id">
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
                <x-textarea x-model="company_description" wire:model="company.description" rows="6"
                    name="description" />
                <x-input-error for="company.description" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="company_image" value="{{ __('Logotipo de la empresa') }}" />
                <x-input wire:model='company_image' wire:model="image_logo" id="company_image" type="file"
                    class="mt-1 block w-full" accept="image/png, image/jpeg, image/jpg" @change="imageChange" />
                <x-input-error for="company.company_image" class="mt-2" />
            </div>
            <x-button>{{ $id ? 'Actualizar' : 'Guardar' }}</x-button>
            <x-secondary-button type="button" href="{{ route('company') }}"
                wire:navigate>Cancelar</x-secondary-button>
        </form>
        <div x-show="company_name || company_type || company_description">
            <div class="bg-white rounded-lg shadow-md p-5">
                <template x-if="imageUrl">
                    <picture class="block mb-0 md:mb-2">
                        <img alt="company-logo"
                            class="flex-shrink-0 rounded-lg w-24 h-24 object-cover object-center sm:mb-0 mb-4"
                            :src="imageUrl">
                    </picture>
                </template>
                <template x-if="!imageUrl">
                    <picture class="block mb-0 md:mb-2">
                        <img alt="company-logo"
                            class="flex-shrink-0 rounded-lg w-24 h-24 object-cover object-center sm:mb-0 mb-4"
                            src="{{ asset($company->company_image ? 'storage/empresas/' . $company->company_image : 'storage/empresas/' . 'tbn-default.webp') }}">
                    </picture>
                </template>
                <h5 x-text="company_name" class="font-medium text-lg mb-2 text-tbn-primary"></h5>
                <template x-if="typeText">
                    <p x-text="typeText" class="inline-block bg-gray-200 px-3 py-1 mb-2 rounded-lg text-sm"></p>
                </template>
                <p x-text="company_description" class="text-tbn-dark text-sm"></p>
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                imageUrl: null,
                companyTypes: {!! $company_types !!},
                typeText: '',
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
