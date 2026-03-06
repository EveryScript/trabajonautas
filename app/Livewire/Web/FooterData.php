<?php

namespace App\Livewire\Web;

use App\Models\Area;
use App\Models\Profesion;
use Livewire\Component;

class FooterData extends Component
{
    public $profesions;
    public function mount()
    {
        $this->profesions = Profesion::inRandomOrder()->limit(5)->get();
    }

    public function render()
    {
        return <<<'HTML'
        <div class="grid max-w-6xl grid-cols-1 gap-8 mx-auto lg:grid-cols-3">
            <div class="text-white">
                <h5 class="text-lg font-bold">Contacto:</h5>
                <p class="mb-1 text-sm">CEO Ricardo Carlos Oropeza Zárate</p>
                <p class="mb-1 text-sm">CFO Carla Ximena Vargas Soto de Oropeza</p>
                <p class="mb-1 text-sm">73858162 - 69616052</p>
                <p class="mb-2 text-xs">&copy; {{ now()->year }} - Todos los derechos reservados</p>
            </div>
            <div class="text-white">
                <h5 class="mb-2 text-lg font-bold">Redes sociales:</h5>
                <nav class="flex flex-row gap-4 mb-10 text-sm list-none">
                    <li class="transition-colors duration-150 hover:text-tbn-light">
                        <a href="https://www.facebook.com/share/1DCXk3jMry/?mibextid=wwXIfr" target="_blank">
                            <i class="text-4xl fa-brands fa-facebook"></i></a>
                    </li>    
                    <li class="transition-colors duration-150 hover:text-tbn-light">
                        <a href="https://www.youtube.com/@Trabajonautas" target="_blank">
                            <i class="text-4xl fa-brands fa-youtube"></i></a>
                    </li>
                    <li class="transition-colors duration-150 hover:text-tbn-light">
                        <a href="https://www.instagram.com/trabajonautas/" target="_blank">
                            <i class="text-4xl fa-brands fa-instagram"></i></a>
                    </li>
                    <li class="transition-colors duration-150 hover:text-tbn-light">
                        <a href="https://www.tiktok.com/@trabajonautas" target="_blank">
                            <i class="text-4xl fa-brands fa-tiktok"></i></a>
                    </li>
                </nav>
            </div>
            
        </div>
        HTML;
    }
}
