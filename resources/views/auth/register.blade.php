<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <h1 class="text-2xl xl:text-2xl">
                <x-authentication-card-logo />
            </h1>
        </x-slot>

        <x-validation-errors class="mb-2" />

        <div class="w-full flex-1 mt-2">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mx-auto max-w-xs">
                    <div class="relative mt-6">
                        <x-label for="name" value="{{ __('Nombre completo') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" />
                    </div>
                    <div class="relative mt-6">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autocomplete="username" />
                    </div>
                    <div class="relative mt-6">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input-password id="password" class="block mt-1 w-full" type="password" name="password"
                            required autocomplete="new-password" />
                    </div>
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mt-4">
                            <x-label for="terms">
                                <div class="flex items-center">
                                    <x-checkbox name="terms" id="terms" required />

                                    <div class="ms-2 text-xs">
                                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' =>
                                                '<a target="_blank" href="' .
                                                route('terms.show') .
                                                '" class="underline hover:text-tbn-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150">' .
                                                __('Terms of Service') .
                                                '</a>',
                                            'privacy_policy' =>
                                                '<a target="_blank" href="' .
                                                route('policy.show') .
                                                '" class="underline hover:text-tbn-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150">' .
                                                __('Privacy Policy') .
                                                '</a>',
                                        ]) !!}
                                    </div>
                                </div>
                            </x-label>
                        </div>
                    @endif
                    <div class="flex gap-4 items-center justify-between mt-8">
                        <x-button>{{ __('Register') }}</x-button>
                        <a class="underline text-sm hover:text-tbn-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-primary transition duration-150"
                            href="{{ route('login') }}" wire:navigate>
                            {{ __('Ya tengo una cuenta') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
