<x-modal-form class="w-full max-w-2xl">
    <x-title-app>
        <x-slot name="title_page">Configurar profesion</x-slot>
        <x-slot name="description_page">
            Administra la información de las profesiones disponibles en Trabajonautas.com
        </x-slot>
    </x-title-app>
    <form wire:submit="save">
        <div class="mb-4">
            <x-label for="expiration_time" value="{{ __('Nombre de la profesión') }}" />
            <x-input wire:model="form.profesion_name" id="profesion_name" type="text" class="mt-1 block w-full"
                placeholder="Ingeniería, Leyes, Arquitectura" />
            <x-input-error for="form.profesion_name" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-label for="area_id" value="{{ __('Area') }}" />
            <x-select class="w-full" wire:model='form.area_id' name="area_id" id="area_id">
                <option>Seleccione un area</option>
                @foreach ($areas as $area)
                    <option value="{{ intval($area->id) }}">{{ $area->area_name }}</option>
                @endforeach
            </x-select>
            <x-input-error for="form.area_id" class="mt-2" />
        </div>
        <x-button wire:loading.attr='disabled' wire:target='save' type="submit">
            <span wire:loading.remove wire:target='save'>Guardar profesion</span>
            <span wire:loading wire:target='save'>Guardando...</span>
        </x-button>
        <x-secondary-button x-on:click="closeProfesionForm" type="button" class="ml-1">
            Cancelar</x-secondary-button>
        <hr class="bg-tbn-dark dark:bg-tbn-secondary my-4">
        <div class="text-xs text-tbn-dark dark:text-tbn-light">
            Las <strong>profesiones</strong> son utilizadas para que los usuarios en el sistema puedan
            registrarse con ellas y también se vinculan a las convocatorias que se registran en el sitio web.
        </div>
    </form>
</x-modal-form>
