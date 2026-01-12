<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h5 class="my-4 text-lg font-bold text-center lg:text-left">Confirmación de correo electrónico</h5>

        <div class="max-w-xs mb-4 text-sm text-tbn-dark dark:text-tbn-light">
            {{ __('Hemos enviado un enlace para verificar tu correo electrónico. Si aún no recibiste el enlace puedes volverlo a enviar.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="max-w-xs mb-4 text-sm font-medium text-green-600">
                {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
            </div>
        @endif

        <div class="flex flex-col items-center justify-between gap-4 mt-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button type="submit">
                        {{ __('Resend Verification Email') }}
                    </x-button>
                </div>
            </form>

            <div>
                <!--
                <a
                    href="{{ route('profile.show') }}"
                    class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    {{ __('Edit Profile') }}</a>
                -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="text-sm underline transition-colors duration-150 rounded-md text-tbn-dark hover:text-tbn-secondary dark:text-tbn-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
