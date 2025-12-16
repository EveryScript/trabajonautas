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
                    <div class="relative mt-6 mb-2">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input-password id="password" class="block mt-1 w-full" type="password" name="password"
                            required autocomplete="current-password" />
                    </div>
                    @if (Route::has('password.request'))
                        <a class="underline text-xs hover:text-tbn-light rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high cursor-pointer"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                    <div class="relative mt-4">
                        <label for="remember_me" class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" />
                            <span class="ms-2 text-sm">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    <div class="flex gap-4 items-center justify-between mt-8 mb-6">
                        <x-button>{{ __('Log in') }}</x-button>
                        <a class="underline text-sm hover:text-tbn-light rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high cursor-pointer"
                            href="{{ route('register') }}" wire:navigate>
                            {{ __('Crear cuenta') }}
                        </a>
                    </div>
                </div>
            </form>
            <hr class="mx-auto max-w-xs my-4">
            <div class="mx-auto max-w-xs">
                {{-- <a href="{{ route('social.redirect', 'google') }}"
                    class="w-full flex items-center justify-center px-4 py-2 border border-tbn-primary rounded-md text-sm font-medium">
                    <i class="fa-brands fa-google text-tbn-primary mr-3"></i>
                    Iniciar sesión con <span class="text-tbn-primary font-bold ml-1">Google</span>
                </a> --}}
                <a href="{{ route('social.redirect', 'google') }}"
                    class="px-4 py-2 border flex justify-center gap-2 border-slate-200 rounded-lg text-slate-700 hover:border-slate-400  hover:text-slate-900 hover:shadow transition duration-150">
                    <img class="w-6 h-6" src="https://www.svgrepo.com/show/475656/google-color.svg" loading="lazy"
                        alt="google logo">
                    <span>Iniciar sesión con Google</span>
                </a>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
