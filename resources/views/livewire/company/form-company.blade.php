<section>
    <x-title-app>
        <x-slot name="title_page">{{ $form->company ? 'Editar empresa' : 'Nueva empresa' }}</x-slot>
        <x-slot name="description_page">
            {{ $form->company
                ? 'Actualiza la información de una empresa registrada'
                : 'Registra una nueva empresa que ofrece convocatorias de trabajo.' }}
        </x-slot>
    </x-title-app>
    <form wire:submit="save">
        <div class="grid grid-cols-1 md:gap-8 md:grid-cols-2">
            <div>
                <div class="mb-4">
                    <x-label for="company_name" value="{{ __('Nombre de la empresa') }}" />
                    <x-input wire:model="form.company_name" id="company_name" type="text" />
                    <x-input-error for="form.company_name" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-label for="company_type_id" value="{{ __('Tipo de empresa') }}" />
                    <x-select wire:model="form.company_type_id" id="company_type_id">
                        @forelse ($company_types as $company_type)
                            <option value="{{ $company_type->id }}">{{ $company_type->company_type_name }}</option>
                        @empty
                            <option>No hay opciones</option>
                        @endforelse
                    </x-select>
                    <x-input-error for="form.company_type_id" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-label for="description" value="{{ __('Descripción de la empresa') }}" />
                    <x-textarea class="w-full" wire:model="form.description" rows="6" name="description" />
                    <x-input-error for="form.description" class="mt-2" />
                </div>
            </div>
            <div>
                @if ($form->company && $preview_image)
                    <div class="mb-4">
                        <x-label value="Logotipo actual" />
                        <img src="{{ asset('storage/' . $preview_image) }}" alt="logotipo-empresa"
                            class="object-cover w-32 h-32 rounded" />
                    </div>
                @endif
                <div class="mb-4">
                    <x-label for="company_image">
                        Logotipo de la empresa <span class="text-xs text-tbn-dark dark:text-tbn-light">
                            (300x300px)</span>
                    </x-label>
                    <x-filepond wire:model="form.company_image" id="company_image" />
                    <x-input-error for="form.company_image" class="mt-2" />
                </div>
            </div>
        </div>
        <div class="mb-4">
            @if ($form->company)
                <x-button wire:loading.attr='disabled'>
                    <span wire:loading.remove wire:target='save'>Actualizar</span>
                    <span wire:loading wire:target='save'>Actualizando...</span>
                </x-button>
            @else
                <x-button wire:loading.attr='disabled'>
                    <span wire:loading.remove wire:target='save'>Guardar</span>
                    <span wire:loading wire:target='save'>Guardando...</span>
                </x-button>
            @endif
            <x-secondary-button type="button" href="{{ route('company') }}" wire:navigate>Cancelar</x-secondary-button>
        </div>
    </form>
</section>
