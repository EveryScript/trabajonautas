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
        <div class="max-w-6xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mx-auto">
            <div class="text-white">
                <h5 class="font-bold text-lg">Contactos</h5>
                <p class="text-sm my-1">CEO Ricardo Carlos Oropeza Zárate</p>
                <p class="text-sm my-1">CFO Carla Ximena Vargas Soto de Oropeza</p>
                <p class="text-sm my-1">73858162 - 69616052</p>
                <p class="text-xs my-2">&copy; {{ now()->year }} - Todos los derechos reservados</p>
            </div>
            <div class="text-white">
                <h5 class="font-bold text-lg">Profesiones</h5>
                <nav class="list-none mb-10 text-sm">
                    @forelse($profesions as $profesion)
                        <li class="hover:text-tbn-light">
                            <a href="{{ route('search', ['title' => $profesion->profesion_name]) }}" wire:navigate>{{ $profesion->profesion_name }}</a>
                        </li>
                    @empty
                        <li class="text-gray-500">No hay profesiones</li>
                    @endforelse
                </nav>
            </div>
            <div class="text-white">
                <h5 class="font-bold text-lg">Áreas</h5>
                <nav class="list-none mb-10 text-sm">
                    @forelse($areas as $area)
                        <li class="hover:text-tbn-light">
                            <a href="{{ route('search', ['title' => $area->area_name]) }}">{{ $area->area_name }}</a>
                        </li>
                    @empty
                        <li class="text-gray-500">No hay areaes</li>
                    @endforelse
                </nav>
            </div>
            <div class="text-white">
                <h5 class="font-bold text-lg">Redes sociales</h5>
                <nav class="list-none mb-10 text-sm">
                    <li class="hover:text-tbn-light">
                        <a href="www.facebook.com">Facebook</a>
                    </li>    
                    <li class="hover:text-tbn-light">
                        <a href="www.whatsapp.com">WhatsApp</a>
                    </li>
                    <li class="hover:text-tbn-light">
                        <a href="www.instagram.com">Instagram</a>
                    </li>
                    <li class="hover:text-tbn-light">
                        <a href="www.tiktok.com">Tiktok</a>
                    </li>
                </nav>
            </div>
        </div>
        HTML;
    }
}
