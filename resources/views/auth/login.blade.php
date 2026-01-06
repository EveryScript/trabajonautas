<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <h1 class="text-2xl xl:text-2xl">
                <x-authentication-card-logo />
            </h1>
        </x-slot>

        <x-validation-errors class="max-w-xs mb-2 text-tbn-primary" />

        @if (session('status'))
            <div class="max-w-xs mb-4 p-4 text-xs bg-gray-500 border text-white rounded">
                {{ session('status') }}</div>
        @endif

        @if (session('session_expired'))
            <div class="max-w-xs mb-4 p-4 text-xs bg-gray-500 border text-white rounded">
                {{ session('session_expired') }}</div>
        @endif

        <div class="w-full flex-1">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mx-auto max-w-xs">
                    <div class="relative mt-6">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                    <div class="relative mt-6 mb-1">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input-password id="password" class="block mt-1 w-full" type="password" name="password"
                            required autocomplete="current-password" />
                    </div>
                    @if (Route::has('password.request'))
                        <a class="underline text-xs hover:text-tbn-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high cursor-pointer transition duration-150"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                    <hr class="mx-auto max-w-xs my-6 border-tbn-secondary">
                    <div class="mx-auto max-w-xs">
                        <a href="{{ route('social.redirect', 'google') }}"
                            class="px-4 py-3 text-sm border flex justify-center gap-2 border-tbn-secondary rounded-lg text-tbn-dark dark:text-tbn-light dark:hover:text-white hover:shadow transition duration-150">
                            <img class="w-5 h-5" src="https://www.svgrepo.com/show/475656/google-color.svg"
                                loading="lazy" alt="google logo">
                            <span>Iniciar sesión con Google</span>
                        </a>
                    </div>
                    <p class="mt-4 text-xs text-tbn-dark dark:text-tbn-light">
                        Al iniciar sesión con Google, aceptas nuestros
                        <a href="{{ route('terms.show') }}" target="_blank" class="underline text-xs hover:text-tbn-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high cursor-pointer transition duration-150">Términos de servicio</a> y
                        <a href="{{ route('policy.show') }}" target="_blank" class="underline text-xs hover:text-tbn-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high cursor-pointer transition duration-150">Política de privacidad</a>.
                    </p>
                    <div class="relative mt-4">
                        <label for="remember_me" class="flex items-center cursor-pointer">
                            <x-checkbox id="remember_me" name="remember" />
                            <span class="ms-2 text-sm">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    <div class="flex gap-4 items-center justify-between mt-8 mb-6">
                        <x-button>{{ __('Log in') }}</x-button>
                        <a class="underline text-sm hover:text-tbn-secondary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high transition duration-150 cursor-pointer"
                            href="{{ route('register') }}" wire:navigate>
                            {{ __('Crear cuenta') }}
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </x-authentication-card>
</x-guest-layout>
