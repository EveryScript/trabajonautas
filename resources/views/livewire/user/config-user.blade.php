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
        <div x-data="{
            name: {{ $user->name ? "'$user->name'" : "''" }},
            email: {{ $user->email ? "'$user->email'" : "''" }},
            password: '',
            role: {{ $user->role ? "'$user->role'" : "''" }},
            get hasUppercase() { return /[A-Z]/.test(this.password) },
            get hasNumber() { return /[0-9]/.test(this.password) },
            get hasMinLength() { return this.password.length >= 8 }
        }">
            <div class="mb-4">
                <x-label for="name" value="{{ __('Nombre completo') }}" />
                <x-input x-model="name" wire:model="user.name" id="name" type="text" class="block w-full mt-1"
                    placeholder="Sergio Rodriguez" />
                <x-input-error for="user.name" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input x-model="email" wire:model="user.email" id="email" type="email" class="block w-full mt-1"
                    placeholder="example@email.com" />
                <x-input-error for="user.email" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-label for="password" value="{{ $id ? __('Nueva contraseña') : __('Contraseña') }}" />
                <x-input-password x-model="password" wire:model="user.password" id="password" type="password"
                    class="block w-full mt-1" />
                <x-input-error for="user.password" class="mt-2" />
                <div class="mt-3 space-y-1 text-sm">
                    <div :class="hasMinLength ? 'text-green-500 text-xs' : 'text-tbn-primary text-xs'"
                        class="flex items-center transition-colors duration-300">
                        <template x-if="hasMinLength">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!hasMinLength">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </template>
                        <span>Al menos 8 caracteres</span>
                    </div>
                    <div :class="hasUppercase ? 'text-green-500 text-xs' : 'text-tbn-primary text-xs'"
                        class="flex items-center transition-colors duration-300">
                        <template x-if="hasUppercase">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!hasUppercase">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </template>
                        <span>Al menos una mayúscula</span>
                    </div>
                    <div :class="hasNumber ? 'text-green-600 text-xs' : 'text-tbn-primary text-xs'"
                        class="flex items-center transition-colors duration-300">
                        <template x-if="hasNumber">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!hasNumber">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </template>
                        <span>Al menos un número</span>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <span class="text-xs text-tbn-primary">Privilegios de usuarios</span>
                <ul class="grid w-full gap-4 md:grid-cols-2">
                    <li>
                        <input type="radio" x-model="role" wire:model='user.role' id="user-account"
                            class="hidden peer" value="{{ USER }}" {{ $user->role == USER ? 'checked' : '' }}>
                        <label for="user-account"
                            class="inline-flex items-center justify-between w-full p-5 bg-white border rounded-lg cursor-pointer text-tbn-secondary dark:text-white dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div class="w-2/3">
                                <div class="w-full text-lg font-semibold">Usuario</div>
                                <p class="w-full text-xs text-tbn-dark dark:text-white">Utiliza las funciones básicas
                                    del sistema
                                    Trabajonautas</p>
                            </div>
                            <i class="text-2xl fas fa-user text-tbn-primary"></i>
                        </label>
                    </li>
                    <li>
                        <input type="radio" x-model="role" wire:model='user.role' id="admin-account"
                            class="hidden peer" value="{{ ADMIN }}"
                            {{ $user->role == ADMIN ? 'checked' : '' }} />
                        <label for="admin-account"
                            class="inline-flex items-center justify-between w-full p-5 bg-white border rounded-lg cursor-pointer text-tbn-secondary dark:text-white dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div class="w-2/3">
                                <div class="w-full text-lg font-semibold">Administrador</div>
                                <p class="w-full text-xs text-tbn-dark dark:text-white">Utiliza todas las funciones del
                                    sistema
                                    Trabajonautas.</p>
                            </div>
                            <i class="text-2xl fas fa-user-cog text-tbn-dark dark:text-white"></i>
                        </label>
                    </li>
                </ul>
            </div>
            @if ($id)
                <span class="text-xs text-tbn-primary">Control de acceso</span>
                <x-input-checkbox-block checked="{{ $user->actived ? 'checked' : '' }}"
                    disabled="{{ auth()->user()->id === $id ? 'disabled' : '' }}" wire:model="user.actived">
                    <div class="ms-4">
                        <p class="font-medium text-md text-tbn-dark dark:text-white">Habilitar usuario</p>
                        <span class="text-xs text-tbn-secondary dark:text-tbn-light">
                            El usuario utiliza el sistema y su cuenta está disponible actualmente</span>
                    </div>
                </x-input-checkbox-block>
            @endif

            <div class="mb-4">
                <x-button
                    x-bind:disabled="!name || !email || !role || (!hasUppercase || !hasNumber || !hasMinLength) &&
                        !{{ $id ? 'true' : 'false' }}"
                    type="submit">{{ $id ? 'Actualizar usuario' : 'Crear usuario' }}</x-button>
                <x-secondary-button type="button" href="{{ route('user') }}"
                    wire:navigate>Cancelar</x-secondary-button>
            </div>
        </div>
    </form>
</section>
