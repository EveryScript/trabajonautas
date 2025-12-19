<x-guest-layout>

    <x-authentication-card>
        <x-slot name="logo">
            <h1 class="text-2xl xl:text-2xl">
                <img src="{{ asset('storage/img/tbn-new-isologo.webp') }}" alt="logo"
                    class="w-[3rem] rounded-full mx-auto">
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

                    <div class="flex gap-4 items-center justify-between mt-8">
                        <x-button>{{ __('Register') }}</x-button>
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href="{{ route('login') }}" wire:navigate>
                            {{ __('Ya tengo una cuenta') }}
                        </a>
                    </div>
                </div>
            </form>
            <hr class="mx-auto max-w-xs my-4">
            <div class="mx-auto max-w-xs">
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
