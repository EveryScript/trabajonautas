<x-action-section>
    <x-slot name="title">
        Eliminar cuenta
    </x-slot>

    <x-slot name="description">
        Elimina el acceso a Trabajonautas y tu cuenta actual. Conservaremos tus datos
        solamente como información y estadística. Revisa nuestras <a href="{{ route('terms.show') }}" target="_blank"
            class="underline text-tbn-primary">Términos de Servicio</a> y <a class="underline text-tbn-primary"
            href="{{ route('policy.show') }}" target="_blank">Pólíticas de
            Privacidad</a>
    </x-slot>

    <x-slot name="content">
        <div class="mt-5">
            <x-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                Eliminar cuenta
            </x-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                Eliminar cuenta
            </x-slot>

            <x-slot name="content">
                @if (auth()->user()->password)
                    <div class="mt-4" x-data="{}"
                        x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                        Ingresa tu <span class="font-bold">contraseña</span> actual para eliminar tu cuenta
                        definitivamente. Toma en cuenta que ya no
                        podrás ingresar al sistema Trabajonautas.com ni utilizar sus funcionalidades.
                        <x-input-password type="password" class="w-full mt-1" autocomplete="current-password"
                            placeholder="{{ __('Password') }}" x-ref="password" wire:model="password"
                            wire:keydown.enter="deleteUser" />

                        <x-input-error for="password" class="mt-2" />
                    </div>
                @else
                    <input type="hidden" wire:model="password" value="socialite-user">
                    <div class="mt-4">
                        ¿Estás seguro de eliminar tu cuenta? Una vez que aceptes dejarás de utilizar el sistema
                        Trabajonautas y todas sus funcionalidades.
                    </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>

                <x-button class="ms-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    Eliminar
                </x-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
