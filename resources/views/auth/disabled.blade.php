<x-guest-layout>
    <section class="flex items-center justify-center min-h-screen bg-gradient-to-br">
        <div class="text-center max-w-xl">
            <img src="{{ asset('storage/img/lock.webp') }}" alt="disabled-user" class="mx-auto w-20">
            <h5 class="text-3xl font-extrabold text-tbn-primary mt-6 px-4">Usuario deshabilitado</h5>
            <p class="text-sm text-tbn-dark mt-3 mb-6 px-4">Hola {{ auth()->user()->name }}, Trabajonautas.com ha
                deshabilitado su acceso al sistema.
                Comuníquese con nosotros para recuperarla su cuenta.</p>
            <form class="px-4" method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <x-button-link type="button" class="inline-block mb-2 bg-tbn-primary"
                    href="https://api.whatsapp.com/send?phone=59173858162&text=Hola%20Trabajonautas%2C%20parece%20que%20mi%20cuenta%20ha%20sido%20desactivada%2C%20por%20que%20ha%20ocurrido%20esto%3F">
                    Contáctate con nosotros</x-button-link>
                <x-secondary-button href="{{ route('logout') }}" @click.prevent="$root.submit();">Cerrar
                    sesión</x-secondary-button>
            </form>
        </div>
    </section>
</x-guest-layout>
