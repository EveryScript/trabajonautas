<?php

namespace App\Livewire\Web;

use App\Models\Area;
use App\Models\Profesion;
use Livewire\Component;

class FooterData extends Component
{
    public $profesions;
    public $areas;
    public function mount()
    {
        $this->profesions = Profesion::inRandomOrder()->limit(4)->get();
        $this->areas = Area::inRandomOrder()->limit(4)->get();
    }

    public function render()
    {
        return <<<'HTML'
        <div class="flex-grow flex flex-wrap md:pl-20 -mb-10 md:mt-0 mt-10 text-white">
            <div class="lg:w-1/3 md:w-1/2 w-full px-4">
                <h2 class="title-font font-medium text-tbn-secondary tracking-widest text-sm mb-3 uppercase">Profesiones
                </h2>
                <nav class="list-none mb-10 text-sm">
                    @forelse($profesions as $profesion)
                        <li class="hover:text-tbn-secondary">
                            <a href="{{ route('search', ['title' => $profesion->profesion_name]) }}" wire:navigate>{{ $profesion->profesion_name }}</a>
                        </li>
                    @empty
                        <li class="text-gray-500">No hay profesiones</li>
                    @endforelse
                </nav>
            </div>
            <div class="lg:w-1/3 md:w-1/2 w-full px-4">
                <h2 class="title-font font-medium text-tbn-secondary tracking-widest text-sm mb-3 uppercase">Areas</h2>
                <nav class="list-none mb-10 text-sm">
                    @forelse($areas as $area)
                        <li class="hover:text-tbn-secondary">
                            <a href="{{ route('search', ['title' => $area->area_name]) }}">{{ $area->area_name }}</a>
                        </li>
                    @empty
                        <li class="text-gray-500">No hay areaes</li>
                    @endforelse
                </nav>
            </div>
            <div class="lg:w-1/3 md:w-1/2 w-full px-4">
                <h2 class="title-font font-medium text-tbn-secondary tracking-widest text-sm mb-3 uppercase">Redes
                    sociales</h2>
                <nav class="list-none mb-10 text-sm">
                    <li>
                        <a class="hover:text-tbn-high">Facebook</a>
                    </li>
                    <li>
                        <a class="hover:text-tbn-high">YouTube</a>
                    </li>
                    <li>
                        <a class="hover:text-tbn-high">TikTok</a>
                    </li>
                    <li>
                        <a class="hover:text-tbn-high">Instagram</a>
                    </li>
                </nav>
            </div>
        </div>
        HTML;
    }
}
