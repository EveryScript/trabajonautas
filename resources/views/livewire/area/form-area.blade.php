<x-modal-form>
    <x-title-app>
        <x-slot name="title_page">Configurar area</x-slot>
        <x-slot name="description_page">
            Configura las areas profesionales para agrupar las profesiones de Trabajonautas
        </x-slot>
    </x-title-app>
    <form wire:submit="save">
        <div class="mb-4">
            <x-label for="area_name" value="{{ __('Nombre del area') }}" />
            <x-input wire:model="form.area_name" id="area_name" type="text"
                placeholder="Ingeniería, Leyes, Arquitectura" />
            <x-input-error for="form.area_name" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-label for="description" value="{{ __('Descripción (corta)') }}" />
            <x-textarea wire:model="form.description" rows="4"
                class="w-full resize-none" name="description" placeholder="Descripción corta" />
            <x-input-error for="form.description" class="mt-2" />
        </div>
        <x-button wire:loading.attr='disabled' wire:target='save' type="submit">
            <span wire:loading.remove wire:target='save'>Guardar area</span>
            <span wire:loading wire:target='save'>Guardando...</span>
        </x-button>
        <x-secondary-button x-on:click="closeAreaForm" type="button" class="ml-1">
            Cancelar</x-secondary-button>
    </form>
</x-modal-form>
