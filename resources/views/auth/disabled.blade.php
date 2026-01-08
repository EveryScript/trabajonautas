<x-guest-layout>
    <section class="flex items-center justify-center min-h-screen bg-gradient-to-br">
        <div class="max-w-xl text-center">
            <img src="{{ asset('storage/img/lock.webp') }}" alt="disabled-user" class="w-20 mx-auto">
            <h5 class="px-4 mt-6 text-3xl font-semibold text-tbn-primary">Usuario deshabilitado</h5>
            <p class="px-4 mt-3 mb-6 text-tbn-dark dark:text-white">Hola {{ auth()->user()->name }}, Trabajonautas.com ha
                deshabilitado su acceso al sistema. Comuníquese con nosotros para recuperarla su cuenta.</p>
            <form class="px-4" method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a href="https://wa.me/59173858162?text=Hola%20Trabajonautas.com%2C%20parece%20que%20mi%20cuenta%20ha%20sido%20desactivada%2C%20por%20que%20ha%20ocurrido%20esto%3F"
                    target="_blank">
                    <x-button type="button" class="mb-2">
                        Comunícate con nosotros</x-button>
                </a>
                <x-secondary-button href="{{ route('logout') }}" @click.prevent="$root.submit();">
                    Cerrar sesión</x-secondary-button>
            </form>
        </div>
    </section>
</x-guest-layout>
