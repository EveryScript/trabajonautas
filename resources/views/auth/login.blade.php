<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <h1 class="text-2xl xl:text-2xl">
                <x-authentication-card-logo />
            </h1>
            <span class="text-center text-sm text-tbn-dark">Inicia sesión con tu email y tu contraseña</span>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
        @endif

        @if (session('session_expired'))
            <div class="mb-4 p-3 text-xs bg-yellow-100 border border-tbn-secondary text-yellow-700 rounded">
                {{ session('session_expired') }}
            </div>
        @endif

        <div class="w-full flex-1 mt-2">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mx-auto max-w-xs">
                    <div class="relative mt-6">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                    <div class="relative mt-6">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input-password id="password" class="block mt-1 w-full" type="password" name="password"
                            required autocomplete="current-password" />
                    </div>
                    <div class="relative mt-6">
                        <label for="remember_me" class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" />
                            <span class="ms-2 text-sm">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    <div class="flex gap-4 items-center justify-between mt-8">
                        <x-button>{{ __('Log in') }}</x-button>
                        <a class="underline text-sm hover:text-tbn-light rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high cursor-pointer"
                            href="{{ route('register') }}" wire:navigate>
                            {{ __('Crear cuenta') }}
                        </a>
                        {{-- @if (Route::has('password.request'))
                            <a
                                class="underline text-sm hover:text-tbn-light rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high cursor-pointer
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif --}}
                    </div>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
