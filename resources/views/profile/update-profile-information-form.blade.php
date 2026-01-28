<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        Información Personal
    </x-slot>

    <x-slot name="description">
        Actualiza tus datos personales.
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden" wire:model.live="photo" x-ref="photo"
                    x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                        class="object-cover w-20 h-20 rounded-full">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="mb-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="block w-full mt-1" wire:model="state.name" required
                autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mb-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="block w-full mt-1" wire:model="state.email" required
                autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) &&
                    !$this->user->hasVerifiedEmail())
                <p class="mt-2 text-sm">
                    {{ __('Your email address is unverified.') }}

                    <button type="button"
                        class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 text-sm font-medium text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <x-label for="phone" value="{{ __('Celular') }}" />
            <x-input id="phone" type="text" class="block w-full mt-1" wire:model="state.phone" disabled />
            <x-input-error for="phone" class="mt-2" />
        </div>

        <!-- Location -->
        <div class="mb-4">
            <x-label for="location" value="{{ __('Ubicación') }}" />
            <x-select class="block w-full mt-1" wire:model="state.location_id" disabled>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                @endforeach
            </x-select>
            <x-input-error for="location" class="mt-2" />
        </div>

        <!-- Profesion -->
        <div class="mb-4">
            <x-label for="profesion" value="{{ __('Profesion') }}" />
            <x-select class="block w-full mt-1" wire:model="state.profesion_id" disabled>
                @foreach ($profesions as $profesion)
                    <option value="{{ $profesion->id }}">{{ $profesion->profesion_name }}</option>
                @endforeach
            </x-select>
            <x-input-error for="profesion" class="mt-2" />
        </div>

        <!-- Grade -->
        <div class="mb-4">
            <x-label for="grade" value="{{ __('Grado académico') }}" />
            <x-select class="block w-full mt-1" wire:model="state.grade_profile_id" disabled>
                @foreach ($grade_profiles as $profile)
                    <option value="{{ $profile->id }}">{{ $profile->profile_name }}</option>
                @endforeach
            </x-select>
            <x-input-error for="grade" class="mt-2" />
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
