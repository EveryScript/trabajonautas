<?php

namespace App\Livewire\Panel;

use Livewire\Component;

class Disabled extends Component
{
    public function render()
    {
        return <<<'HTML'
            <section class="flex items-center justify-center min-h-screen bg-gradient-to-br">
                <div class="text-center animate-fadeIn">
                    <img src="{{ asset('storage/img/tbn-empty.webp') }}" alt="disabled-user" class="mx-auto w-20">
                    <h5 class="text-3xl font-extrabold text-tbn-primary mt-6">Usuario deshabilitado</h5>
                    <p class="text-sm text-gray-700 my-6">El administrador de Trabajonautas ha deshabilitado su acceso al sistema.
                    Comuníquese con él para recuperar su cuenta.</p>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                            <x-button-link href="{{ route('logout') }}" @click.prevent="$root.submit();">Cerrar sesión</x-button-link>
                    </form>
                </div>
            </section>
        HTML;
    }
}
