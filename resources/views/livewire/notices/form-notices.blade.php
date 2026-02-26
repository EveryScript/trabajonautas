<x-modal-form class="w-full max-w-4xl">
    <x-title-app>
        <x-slot name="title_page">Crear noticia</x-slot>
        <x-slot name="description_page">
            Crea una nueva noticia para mostrarla en el sitio web de Trabajonautas.com
        </x-slot>
    </x-title-app>
    <form wire:submit='save'>
        <div class="flex flex-col w-full mb-4 md:flex-row md:gap-4">
            <div class="w-4/6">
                <div class="mb-4">
                    <x-label for="title" value="{{ __('Titulo de la noticia') }}" />
                    <x-input wire:model="form.title" id="title" type="text"
                        placeholder="Novedades en trabajonautas.com" />
                    <x-input-error for="form.title" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-label for="description" value="{{ __('Descripción (corta)') }}" />
                    <x-input wire:model="form.description" id="description" type="text"
                        placeholder="Acerca de la noticia" />
                    <x-input-error for="form.description" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-label for="link" value="{{ __('Enlace') }}" />
                    <x-input wire:model="form.link" id="link" type="text"
                        placeholder="https://url.noticia.com" />
                    <x-input-error for="form.link" class="mt-2" />
                </div>
            </div>
            <div class="w-2/6">
                <div class="mb-4">
                    <x-label for="image">Imagen <span class="text-sm font-light">(580 x 720)</span></x-label>
                    <x-filepond type="file" wire:model="form.image" id="image"
                        accept="image/png, image/jpeg, image/jpg" />
                    <x-input-error for="form.image" class="mt-2" />
                </div>
            </div>
        </div>
        <x-button wire:loading.attr='disabled' wire:target='save' type="submit">
            <span wire:loading.remove wire:target='save'>Publicar</span>
            <span wire:loading wire:target='save'>Publicando...</span>
        </x-button>
        <x-secondary-button type="button" x-on:click="closeNoticeForm">Cancelar</x-secondary-button>
    </form>
</x-modal-form>
