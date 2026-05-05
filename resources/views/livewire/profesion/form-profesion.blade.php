<x-modal-form class="w-full max-w-2xl">
    <x-title-app>
        <x-slot name="title_page">Configurar profesion</x-slot>
        <x-slot name="description_page">
            Administra la información de las profesiones disponibles en Trabajonautas.com
        </x-slot>
    </x-title-app>
    <form wire:submit="save">
        <div class="mb-4">
            <x-label for="profesion_name" value="{{ __('Nombre de la profesión') }}" />
            <x-input wire:model="form.profesion_name" id="profesion_name" type="text" class="block w-full mt-1"
                placeholder="Ingeniería, Leyes, Arquitectura" />
            <x-input-error for="form.profesion_name" class="mt-2" />
        </div>
        <x-button wire:loading.attr='disabled' wire:target='save' type="submit">
            <span wire:loading.remove wire:target='save'>Guardar profesion</span>
            <span wire:loading wire:target='save'>Guardando...</span>
        </x-button>
        <x-secondary-button x-on:click="closeProfesionForm" type="button" class="ml-1">
            Cancelar</x-secondary-button>
        <hr class="my-4 bg-tbn-dark dark:bg-tbn-secondary">
        <div class="text-xs text-tbn-dark dark:text-tbn-light">
            Las <strong>profesiones</strong> son utilizadas para que los usuarios en el sistema puedan
            registrarse con ellas y también se vinculan a las convocatorias que se registran en el sitio web.
        </div>
    </form>
</x-modal-form>
