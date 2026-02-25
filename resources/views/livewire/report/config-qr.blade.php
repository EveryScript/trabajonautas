<x-modal-form>
    <form wire:submit.prevent='save'>
        <div class="grid grid-cols-1 mb-4 md:grid-cols-2 md:gap-8">
            <div>
                @if ($qr_pro)
                    <div class="mb-4">
                        <x-label>Imagen QR <span class="text-tbn-primary">PRO</span> </x-label>
                        <img src="{{ asset('storage/' . $qr_pro->value) }}" alt="imagen-qr-pro"
                            class="object-cover w-32 h-32 rounded" />
                    </div>
                @endif
                <div class="mb-4">
                    <x-label>Cambiar imagen QR <span class="text-tbn-primary">PRO</span> </x-label>
                    <x-filepond wire:model="qr_new_pro" id="qr_new_pro" />
                    <x-input-error for="qr_new_pro" class="mt-2" />
                </div>
            </div>
            <div>
                @if ($qr_promax)
                    <div class="mb-4">
                        <x-label>Imagen QR <span class="text-tbn-primary">PROMAX</span> </x-label>
                        <img src="{{ asset('storage/' . $qr_promax->value) }}"
                            alt="imagen-qr-promax" class="object-cover w-32 h-32 rounded" />
                    </div>
                @endif
                <div class="mb-4">
                    <x-label>Cambiar imagen QR <span class="text-tbn-primary">PROMAX</span> </x-label>
                    <x-filepond wire:model="qr_new_promax" id="qr_new_promax" />
                    <x-input-error for="qr_new_promax" class="mt-2" />
                </div>
            </div>
        </div>
        <x-button wire:loading.attr='disabled' type="submit">
            <span wire:loading.remove wire:target="save">Publicar</span>
            <span wire:loading wire:target="save">Publicando...</span>
        </x-button>
        <x-secondary-button type="button" class="mb-4" x-on:click="closeModal">
            Cancelar</x-secondary-button>
        <div class="text-xs text-tbn-dark dark:text-tbn-light">
            ATENCIÓN. Una vez guardada, la imagen del código QR se actualizará en el formulario de registro
            de clientes de Trabajonautas.com
        </div>
    </form>
</x-modal-form>
