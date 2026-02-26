<x-modal-form class="w-full max-w-4xl">
    <x-title-app>
        <x-slot name="title_page">Personalizar web</x-slot>
        <x-slot name="description_page">
            Ajusta las imágenes de la pantalla principal de Trabajonautas.com
        </x-slot>
    </x-title-app>
    <form wire:submit='save' class="mb-4">
        <div class="grid grid-cols-1 mb-4 md:grid-cols-2 md:gap-8">
            <div>
                <div class="mb-4">
                    <x-label>Imagen de fondo <span class="text-xs font-light">(1500 x 1000)</span> </x-label>
                    <img src="{{ asset('storage/' . $bg_web_image->value) }}" alt="imagen-fondo"
                        class="object-cover w-64 h-32 rounded" />
                </div>
                <x-label for="image" value="{{ __('Cambiar imagen de fondo') }}" />
                <x-filepond type="file" wire:model="bg_new_image" id="image"
                    accept="image/png, image/jpeg, image/jpg, image/webp" />
                <x-input-error for="bg_new_image" class="mt-2" />
            </div>
            <div>
                <div class="mb-4">
                    <x-label>Imagen del astronauta <span class="text-xs font-light">(580 x 720)</span> </x-label>
                    <img src="{{ asset('storage/' . $thumb_web_image->value) }}" alt="imagen-fondo"
                        class="w-32 rounded" />
                </div>
                <x-label for="image" value="{{ __('Cambiar imagen del astronauta') }}" />
                <x-filepond type="file" wire:model="thumb_new_image" id="image"
                    accept="image/png, image/jpeg, image/jpg, image/webp" />
                <x-input-error for="thumb_new_image" class="mt-2" />
            </div>
        </div>
        <x-button wire:loading.attr='disabled' type="submit">
            <span wire:loading.remove wire:target='save'>Publicar</span>
            <span wire:loading wire:target='save'>Publicando...</span>
        </x-button>
        <x-secondary-button type="button" x-on:click="closeSkinForm">Cancelar</x-secondary-button>
    </form>
</x-modal-form>
