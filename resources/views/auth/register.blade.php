<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <h1 class="text-2xl xl:text-2xl">
                <x-authentication-card-logo />
            </h1>
        </x-slot>

        <x-validation-errors class="my-2 min-w-xs" />

        <div class="flex-1 w-full mt-2">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div x-data="{
                    name: '',
                    email: '',
                    password: '',
                    get hasUppercase() { return /[A-Z]/.test(this.password) },
                    get hasNumber() { return /[0-9]/.test(this.password) },
                    get hasMinLength() { return this.password.length >= 8 }
                }" class="max-w-xs mx-auto">
                    <div class="relative mt-6">
                        <x-label for="name" value="{{ __('Nombre completo') }}" />
                        <x-input x-model="name" id="name" class="block w-full mt-1" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" />
                    </div>
                    <div class="relative mt-6">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input x-model="email" id="email" class="block w-full mt-1" type="email" name="email"
                            :value="old('email')" required autocomplete="username" />
                    </div>
                    <div class="relative mt-6">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input-password x-model="password" id="password" class="block w-full mt-1" type="password"
                            name="password" required autocomplete="new-password" />
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
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mt-4">
                            <x-label for="terms">
                                <div class="flex items-center">
                                    <x-checkbox name="terms" id="terms" required />

                                    <div class="text-xs ms-2">
                                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' =>
                                                '<a target="_blank" href="' .
                                                route('terms.show') .
                                                '" class="underline transition-colors duration-150 rounded-md hover:text-tbn-secondary focus:outline-none focus:ring-2 focus:ring-offset-2">' .
                                                __('Terms of Service') .
                                                '</a>',
                                            'privacy_policy' =>
                                                '<a target="_blank" href="' .
                                                route('policy.show') .
                                                '" class="underline transition-colors duration-150 rounded-md hover:text-tbn-secondary focus:outline-none focus:ring-2 focus:ring-offset-2">' .
                                                __('Privacy Policy') .
                                                '</a>',
                                        ]) !!}
                                    </div>
                                </div>
                            </x-label>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-4 mt-8">
                        <x-button
                            x-bind:disabled="!name || !email || !hasUppercase || !hasNumber || !hasMinLength">{{ __('Register') }}</x-button>
                        <a class="text-sm underline transition duration-150 rounded-md hover:text-tbn-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-primary"
                            href="{{ route('login') }}" wire:navigate>
                            {{ __('Ya tengo una cuenta') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
