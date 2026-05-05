<x-modal-form class="w-full max-w-6xl">
    <x-title-app>
        <x-slot name="title_page">Configurar area</x-slot>
        <x-slot name="description_page">
            Configura las areas profesionales para agrupar las profesiones de Trabajonautas
        </x-slot>
    </x-title-app>
    <form wire:submit="save">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <div class="mb-4">
                    <x-label for="area_name" value="{{ __('Nombre del area') }}" />
                    <x-input wire:model="form.area_name" id="area_name" type="text"
                        placeholder="Ingeniería, Leyes, Arquitectura" />
                    <x-input-error for="form.area_name" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-label for="description" value="{{ __('Descripción (corta)') }}" />
                    <x-textarea wire:model="form.description" rows="4" class="w-full resize-none"
                        name="description" placeholder="Descripción corta" />
                    <x-input-error for="form.description" class="mt-2" />
                </div>
            </div>
            <div>
                <div class="tbn-tom-select" wire:ignore>
                    <x-label for="profesions" value="{{ __('Profesiones') }}" />
                    <x-select id="profesions" wire:model="form.profesions" multiple
                        @area-edit.window="ts_profesions.setValue($event.detail.profesion_ids)">
                        @forelse ($profesions as $profesion)
                            <option value="{{ $profesion['id'] }}">{{ $profesion['profesion_name'] }}
                            </option>
                        @empty
                            <option>No hay opciones para mostrar</option>
                        @endforelse
                    </x-select>
                </div>
                <x-input-error for="form.profesions" class="mt-2" />
            </div>
        </div>
        <x-button wire:loading.attr='disabled' wire:target='save' type="submit">
            <span wire:loading.remove wire:target='save'>Guardar area</span>
            <span wire:loading wire:target='save'>Guardando...</span>
        </x-button>
        <x-secondary-button wire:click="closeModal" type="button" class="ml-1">
            Cancelar</x-secondary-button>
    </form>
</x-modal-form>
