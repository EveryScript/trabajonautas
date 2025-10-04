<x-guest-layout>

    <x-authentication-card>
        <x-slot name="logo">
            <h1 class="text-2xl xl:text-2xl">
                <x-authentication-card-logo />
            </h1>
            <span class="text-center text-sm text-tbn-dark">Ingresa tus datos y regístrate ahora.</span>
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
                        <x-input-password id="password" class="block mt-1 w-full" type="password" name="password" required
                            autocomplete="new-password" />
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
        </div>
        <!-- Google registration -->
        {{-- <div class="flex justify-evenly items-center space-x-2 w-80 mt-4">
            <span class="bg-gray-300 h-px flex-grow t-2 relative top-2"></span>
        </div>
        <div class="mt-4 w-full flex flex-col items-center gap-3">
            <div class="w-full flex-1 mt-4">
                <div class="flex flex-col items-center">
                    <button
                        class="w-full max-w-xs font-bold shadow-sm rounded-lg py-3 bg-indigo-100 text-gray-800 flex items-center justify-center transition-all duration-300 ease-in-out focus:outline-none hover:shadow focus:shadow-sm focus:shadow-outline">
                        <div class="bg-white p-2 rounded-full">
                            <i class="fab fa-google"></i>
                        </div>
                        <span class="ml-4">
                            Google
                        </span>
                    </button>
                </div>
            </div>
        </div> --}}
    </x-authentication-card>
</x-guest-layout>
