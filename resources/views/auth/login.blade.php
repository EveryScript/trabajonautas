<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <h1 class="text-2xl xl:text-2xl">
                <x-authentication-card-logo />
            </h1>
        </x-slot>

        <x-validation-errors class="max-w-xs mb-2 text-tbn-primary" />

        @if (session('status'))
            <div class="max-w-xs p-4 my-4 text-xs text-white border rounded bg-tbn-secondary">
                {{ session('status') }}</div>
        @endif

        @if (session('session_expired'))
            <div class="max-w-xs p-4 my-4 text-xs text-white border rounded bg-tbn-secondary">
                {{ session('session_expired') }}</div>
        @endif

        <div class="flex-1 w-full">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="max-w-xs mx-auto">
                    <div class="relative mt-6">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block w-full mt-1" type="email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                    <div class="relative mt-6 mb-1">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input-password id="password" class="block w-full mt-1" type="password" name="password"
                            required autocomplete="current-password" />
                    </div>
                    @if (Route::has('password.request'))
                        <a class="text-xs underline transition duration-150 rounded-md cursor-pointer hover:text-tbn-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                    <div class="relative max-w-xs">
                        <span class="absolute py-2 px-2 text-xs -top-4 left-[47%] bg-tbn-dark text-tbn-secondary">
                            <i class="fa-solid fa-o"></i></span>
                        <hr class="mx-auto my-6 border-tbn-secondary">
                    </div>
                    <div class="max-w-xs mx-auto">
                        <a href="{{ route('social.redirect', 'google') }}"
                            class="flex justify-center gap-2 px-4 py-3 text-sm transition duration-150 border rounded-lg border-tbn-secondary text-tbn-dark dark:text-tbn-light dark:hover:text-white hover:shadow">
                            <img class="w-5 h-5" src="https://www.svgrepo.com/show/475656/google-color.svg"
                                loading="lazy" alt="google logo">
                            <span>Iniciar sesión con Google</span>
                        </a>
                    </div>
                    <p class="mt-4 text-xs text-tbn-dark dark:text-tbn-light">
                        Al iniciar sesión con Google, aceptas nuestros
                        <a href="{{ route('terms.show') }}" target="_blank"
                            class="text-xs underline transition duration-150 rounded-md cursor-pointer hover:text-tbn-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high">Términos
                            de servicio</a> y
                        <a href="{{ route('policy.show') }}" target="_blank"
                            class="text-xs underline transition duration-150 rounded-md cursor-pointer hover:text-tbn-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high">Política
                            de privacidad</a>.
                    </p>
                    <div class="relative mt-4">
                        <label for="remember_me" class="flex items-center cursor-pointer">
                            <x-checkbox id="remember_me" name="remember" />
                            <span class="text-sm ms-2">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    <div class="flex items-center justify-between gap-4 mt-8 mb-6">
                        <x-button>{{ __('Log in') }}</x-button>
                        <a class="text-sm underline transition duration-150 rounded-md cursor-pointer hover:text-tbn-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tbn-high"
                            href="{{ route('register') }}" wire:navigate>
                            {{ __('Crear cuenta') }}
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </x-authentication-card>
</x-guest-layout>
