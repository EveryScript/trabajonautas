<aside class="p-5 transition-all duration-300 bg-white rounded-md shadow dark:bg-tbn-dark">
    <!-- Client Detail -->
    @if ($view_client)
        <form wire:submit='saveClient' wire:key='detail-{{ $view_client->id }}' x-show="!loading_client">
            <div class="mb-4">
                <p class="text-xl text-tbn-dark dark:text-white">{{ $view_client->name }}</p>
                <small class="text-tbn-secondary dark:text-tbn-light">{{ $view_client->email }}</small>
            </div>
            @if ($view_client->register_completed)
                <div class="mb-4">
                    <span class="text-xs font-normal text-tbn-primary">Profesión</span>
                    <p class="text-tbn-dark dark:text-white">{{ $view_client->profesion->profesion_name }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div>
                        <span class="text-xs font-normal text-tbn-primary">Ubicación</span>
                        <p class="text-tbn-dark dark:text-white">
                            {{ $view_client->location->location_name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-normal text-tbn-primary">Celular</span>
                        <p class="text-tbn-dark dark:text-white">
                            <a href="http://wa.me/{{ $view_client->phone }}" target="_blank" rel="phone-verify"
                                class="text-green-500 underline">{{ $view_client->phone }}</a>
                        </p>
                    </div>
                    @if ($view_client->trashed())
                        <div class="col-span-2">
                            <span class="text-xs font-normal text-tbn-primary">Eliminación de la cuenta</span>
                            <p class="text-tbn-dark dark:text-white">
                                {{ $view_client->deleted_at->translatedFormat('l d/F/Y H:i') }}
                            </p>
                        </div>
                    @else
                        @if ($view_client->account)
                            <div>
                                <span class="text-xs font-normal text-tbn-primary">Cuenta</span>
                                <p class="text-tbn-dark dark:text-white">
                                    <span
                                        class="inline-block px-2 py-1 text-xs text-white {{ $view_client->account->account_type_id == 1 ? 'bg-green-600' : 'bg-tbn-primary' }} rounded-full tracking-wider">
                                        <i
                                            class="mr-1 fas {{ $view_client->account->account_type_id == 1 ? 'fa-leaf' : 'fa-crown' }}"></i>
                                        {{ $view_client->account->type->name }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <span class="text-xs font-normal text-tbn-primary">Registro de cuenta</span>
                                <p class="text-tbn-dark dark:text-white">
                                    {{ $view_client->account->updated_at->translatedFormat('l d/F/Y H:i') }}
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
                @if (!$view_client->trashed())
                    @if ($view_client->latestPendingSubscription)
                        <div class="mb-4">
                            <span class="text-xs font-normal text-tbn-primary">Fecha de solicitud</span>
                            <p class="text-tbn-dark dark:text-white">
                                {{ $view_client->latestPendingSubscription->updated_at->translatedFormat('l d/F/Y H:i') }}
                            </p>
                        </div>
                        <x-input-checkbox-block wire:model="verified_payment">
                            <div class="ms-4">
                                <p class="font-medium text-md text-tbn-dark dark:text-white">Verificación de pago</p>
                                <p class="text-xs text-tbn-dark dark:text-tbn-light">
                                    Solicitud de cuenta: <span class="font-medium text-tbn-primary">
                                        {{ $view_client->latestPendingSubscription->type->name }}</span> <br>
                                    Precio: <span class="font-medium text-tbn-primary">
                                        {{ $view_client->latestPendingSubscription->price }} Bs. </span>
                                </p>
                            </div>
                        </x-input-checkbox-block>
                    @endif
                    <x-input-checkbox-block wire:model="client_actived" :checked="$client_actived">
                        <div class="ms-4">
                            <p class="font-medium text-md text-tbn-dark dark:text-white">Habilitar cliente</p>
                            <p class="text-xs text-tbn-dark dark:text-tbn-light">
                                El cliente utiliza el sistema y su cuenta está disponible actualmente </p>
                        </div>
                    </x-input-checkbox-block>
                    <x-button type='submit' wire:loading.attr="disabled" wire:target="saveClient" class="w-full">
                        <span wire:loading.remove wire:target="saveClient">
                            Guardar Cambios
                        </span>
                        <span wire:loading wire:target="saveClient" class="flex items-center gap-2">
                            <i class="mr-1 text-sm fa-solid fa-spinner animate-spin"></i> Guardando...
                        </span>
                    </x-button>
                @endif
            @else
                <div class="mb-4">
                    <span class="text-xs font-normal text-tbn-primary">Datos del cliente</span>
                    <p class="text-tbn-dark dark:text-white animate-pulse">Registro en proceso...</p>
                </div>
            @endif
        </form>
        <!-- Loading client -->
        <div x-show="loading_client" class="flex items-center h-[30rem]">
            <p class="w-full text-center text-tbn-secondary dark:text-tbn-light">
                <i class="text-tbn-primary animate-spin fa-solid fa-spinner"></i> <br> Cargando
            </p>
        </div>
    @else
        <div class="flex items-center h-[30rem]">
            <div class="w-full text-center">
                <p x-show="loading_client" class="w-full text-center text-tbn-secondary dark:text-tbn-light">
                    <i class="text-tbn-primary animate-spin fa-solid fa-spinner"></i> <br> Cargando
                </p>
                <picture x-show="!loading_client" class="relative block mb-2">
                    <img src="{{ asset('storage/img/tbn-new-isologo.webp') }}" alt="avatar"
                        class="block dark:hidden w-[3rem] rounded-full mx-auto">
                    <img src="{{ asset('storage/img/tbn-white-isologo.webp') }}" alt="avatar"
                        class="hidden dark:block w-[3rem] rounded-full mx-auto">
                </picture>
                <h5 x-show="!loading_client" class="mb-2 text-lg text-tbn-primary">
                    Selecciona a un usuario
                </h5>
                <p x-show="!loading_client" class="text-xs text-tbn-secondary dark:text-tbn-light">
                    Inicia la configuración de usuarios en este panel.
                </p>
            </div>
        </div>
    @endif
</aside>
