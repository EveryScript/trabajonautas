<section>
    <!-- Account information -->
    <x-form-section submit="updateAccountInformation">
        <x-slot name="title">
            Información de la cuenta
        </x-slot>

        <x-slot name="description">
            Administra la información de la cuenta que creaste en Trabajonautas.com
        </x-slot>

        <x-slot name="form">
            @if ($client->account)
                <!-- Type -->
                <div class="mb-4">
                    <x-label for="created_at" value="{{ __('Tipo de cuenta') }}" />
                    <span
                        class="inline-block px-2 py-1 my-2 text-xs text-white {{ $client->account->account_type_id == 1 ? 'bg-green-600' : 'bg-tbn-primary' }} rounded-full tracking-wider">
                        <i class="mr-1 fas {{ $client->account->account_type_id == 1 ? 'fa-leaf' : 'fa-crown' }}"></i>
                        {{ $client->account->type->name }}
                    </span>
                </div>
                <!-- Created at -->
                <div class="mb-4">
                    <x-label for="created_at" value="{{ __('Fecha de registro') }}" />
                    <x-input id="created_at" type="text" class="block w-full mt-1"
                        value="{{ date('d/M/Y H:i', strtotime($client->account->created_at)) }}" disabled
                        autocomplete="name" />
                    <x-input-error for="created_at" class="mt-2" />
                </div>
                <!-- Updated at -->
                <div class="mb-4">
                    <x-label for="updated_at" value="{{ __('Última actualización') }}" />
                    <x-input id="updated_at" type="text" class="block w-full mt-1"
                        value="{{ date('d/M/Y H:i', strtotime($client->account->updated_at)) }}" disabled
                        autocomplete="name" />
                    <x-input-error for="updated_at" class="mt-2" />
                </div>
                <!-- Price -->
                <div class="mb-4">
                    <x-label for="price" value="{{ __('Costo de la cuenta') }}" />
                    <x-input id="price" type="text" class="block w-full mt-1"
                        value="{{ $client->account->type->price . ' Bs.' }}" disabled autocomplete="name" />
                    <x-input-error for="price" class="mt-2" />
                </div>
                <!-- Duration Days -->
                <div class="mb-4">
                    <x-label for="duration_days" value="{{ __('Duración de la cuenta') }}" />
                    <x-input id="duration_days" type="text" class="block w-full mt-1"
                        value="{{ intval($client->account->account_type_id) === 1 ? 'Para siempre' : $client->account->type->duration_days }}"
                        disabled autocomplete="name" />
                    <x-input-error for="duration_days" class="mt-2" />
                </div>
            @else
                <!-- Type -->
                <div class="mb-2">
                    <x-label for="created_at" value="{{ __('Tipo de cuenta') }}" />
                    <span
                        class="inline-block px-2 py-1 my-2 text-xs tracking-wider text-white rounded-full bg-tbn-secondary">
                        <i
                            class="mr-1 fas {{ $client->latestPendingSubscription->account_type_id == 1 ? 'fa-leaf' : 'fa-crown' }}"></i>
                        {{ $client->latestPendingSubscription->type->name }}
                    </span>
                </div>
                <!-- Verifing progress -->
                <div class="mb-2">
                    <x-label for="duration_days" value="{{ __('Verificación de la cuenta') }}" />
                    <p class="text-tbn-secondary dark:text-tbn-light animate-pulse">En progreso ... </p>
                </div>
                <!-- Created at -->
                <div class="mb-4">
                    <x-label for="created_at" value="{{ __('Fecha de solicitud') }}" />
                    <x-input id="created_at" type="text" class="block w-full mt-1"
                        value="{{ date('d/M/Y H:i', strtotime($client->latestPendingSubscription->created_at)) }}"
                        disabled autocomplete="name" />
                    <x-input-error for="created_at" class="mt-2" />
                </div>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-action-message class="me-3" on="saved">
                {{ __('Saved.') }}
            </x-action-message>

            <x-button wire:loading.attr="disabled" wire:target="account" hidden>
                <span wire:loading.remove wire:target="account"> Guardar</span>
                <span wire:loading wire:target="account" class="flex items-center gap-2">
                    <i class="mr-1 text-sm fa-solid fa-spinner animate-spin"></i> Guardando...
                </span>
            </x-button>
        </x-slot>
    </x-form-section>
</section>
