<x-app-layout>
    <header class="mb-4">
        <h2 class="text-xl font-semibold leading-tight text-tbn-dark dark:text-white">
            Configuración de usuario
        </h2>
        <span class="text-sm font-light text-tbn-secondary dark:text-tbn-light">
            Accede a la configuración de tus principales datos en el sistema de Trabajonautas.com
        </span>
    </header>

    <div class="px-6 py-5 mb-6 transition-all duration-300 bg-white shadow-lg dark:bg-tbn-dark rounded-xl hover:shadow-xl">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
        @endif
    </div>
    <div class="px-6 py-5 transition-all duration-300 bg-white shadow-lg dark:bg-tbn-dark rounded-xl hover:shadow-xl">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-account-information-form')
        @endif
    </div>

    <div class="hidden">
        <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
