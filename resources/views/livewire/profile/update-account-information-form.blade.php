<x-form-section submit="updateAccountInformation">
    <x-slot name="title">
        Información de la cuenta
    </x-slot>

    <x-slot name="description">
        Administra la información de la cuenta que creaste en Trabajonautas.com
    </x-slot>

    <x-slot name="form">
        <!-- Tipo de cuenta -->
        <div class="mb-4">
            <x-label for="account_type" value="{{ __('Tipo de cuenta') }}" />
            <x-select class="block w-full mt-1" wire:model="state.account_type_id" disabled>
                @foreach ($account_types as $account_type)
                    <option value="{{ $account_type->id }}">{{ $account_type->name }}</option>
                @endforeach
            </x-select>
            <x-input-error for="account_type" class="mt-2" />
        </div>
        <!-- Fecha de creación -->
        <div class="mb-4">
            <x-label for="created_at" value="{{ __('Fecha de creación') }}" />
            <x-input id="created_at" type="text" class="block w-full mt-1" wire:model="state.created_at" disabled
                autocomplete="name" />
            <x-input-error for="created_at" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
