<section>
    <x-title-app>
        <x-slot name="title_page">{{ $id ? 'Editar usuario' : 'Nuevo usuario' }}</x-slot>
        <x-slot name="description_page">
            {{ $id
                ? 'Actualiza la información de un usuario'
                : 'Crea un nuevo usuario que administra la información de Trabajonautas' }}
        </x-slot>
    </x-title-app>
    <form class="max-w-2xl" wire:submit="{{ $id ? 'update' : 'save' }}">
        <div class="mb-4">
            <x-label for="name" value="{{ __('Nombre completo') }}" />
            <x-input wire:model="user.name" id="name" type="text" class="mt-1 block w-full"
                placeholder="Sergio Rodriguez" />
            <x-input-error for="user.name" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input wire:model="user.email" id="email" type="email" class="mt-1 block w-full"
                placeholder="example@email.com" />
            <x-input-error for="user.email" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-label for="password" value="{{ $id ? __('Nueva contraseña') : __('Contraseña') }}" />
            <x-input-password wire:model="user.password" id="password" type="password" class="mt-1 block w-full" />
            <x-input-error for="user.password" class="mt-2" />
        </div>
        <div class="mb-4">
            <span class="text-xs text-tbn-primary">Privilegios de usuarios</span>
            <ul class="grid w-full gap-4 md:grid-cols-2">
                <li>
                    <input type="radio" wire:model='user.role' id="user-account" class="hidden peer"
                        value="{{ USER }}" {{ $user->role == USER ? 'checked' : '' }}>
                    <label for="user-account"
                        class="inline-flex items-center justify-between w-full p-5 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                        <div class="w-2/3">
                            <div class="w-full text-lg font-semibold">Usuario</div>
                            <p class="w-full text-xs text-tbn-dark dark:text-white">Utiliza las funciones básicas del sistema
                                Trabajonautas</p>
                        </div>
                        <i class="fas fa-user text-tbn-primary text-2xl"></i>
                    </label>
                </li>
                <li>
                    <input type="radio" wire:model='user.role' id="admin-account" class="hidden peer"
                        value="{{ ADMIN }}" {{ $user->role == ADMIN ? 'checked' : '' }} />
                    <label for="admin-account"
                        class="inline-flex items-center justify-between w-full p-5 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                        <div class="w-2/3">
                            <div class="w-full text-lg font-semibold">Administrador</div>
                            <p class="w-full text-xs text-tbn-dark dark:text-white">Utiliza todas las funciones del sistema
                                Trabajonautas.</p>
                        </div>
                        <i class="fas fa-user-cog text-tbn-dark dark:text-white text-2xl"></i>
                    </label>
                </li>
            </ul>
        </div>
        @if ($id)
            <span class="text-xs text-tbn-primary">Control de acceso</span>
            <x-input-checkbox-block checked="{{ $user->actived ? 'checked' : '' }}"
                disabled="{{ auth()->user()->id === $id ? 'disabled' : '' }}" wire:model="user.actived">
                <div class="ms-4">
                    <p class="text-md font-medium text-black">Habilitar usuario</p>
                    <span class="text-xs text-tbn-dark">
                        El usuario utiliza el sistema y su cuenta está disponible actualmente</span>
                </div>
            </x-input-checkbox-block>
        @endif

        <div class="mb-4">
            <x-button type="submit">{{ $id ? 'Acualizar usuario' : 'Crear usuario' }}</x-button>
            <x-secondary-button type="button" href="{{ route('user') }}" wire:navigate>Cancelar</x-secondary-button>
        </div>
    </form>
</section>
