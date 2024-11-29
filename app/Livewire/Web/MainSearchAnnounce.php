<?php

namespace App\Livewire\Web;

use Livewire\Attributes\Validate;
use Livewire\Component;

class MainSearchAnnounce extends Component
{
    #[Validate(['title' => 'required'], [], ['title' => 'profesión'])]
    public $title, $disabled_flag = true;

    public function showResults()
    {
        $this->validate();
        $this->redirectRoute('search', ['title' => $this->title], navigate: true);
    }

    public function render()
    {
        $this->disabled_flag = $this->title ? true : false;
        return <<<'HTML'
        <form wire:submit="showResults" class="w-full">
            <div x-data="content" class="w-full max-w-[40rem] flex flex-row gap-2 mx-auto">
                <div class="w-full">
                    <x-input x-model="search" wire:model="title"
                        class="border border-tbn-primary" placeholder="Ingresa la profesión que estás buscando"/>
                    <x-input-error for="title" class="mt-2" />
                </div>
                <div>
                    <x-button x-bind:disabled="search == ''">Buscar</x-button>
                </div>
            </div>
            @script
                <script>
                    Alpine.data('content', () => ({
                        search: ''
                    }))
                </script>
            @endscript
        </form>
        HTML;
    }
}
