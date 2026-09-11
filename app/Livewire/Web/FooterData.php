<?php

namespace App\Livewire\Web;

use App\Models\TbnSetting;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class FooterData extends Component
{
    public ?string $bgWebImageUrl = null;

    public function mount()
    {
        $images = Cache::remember('tbn-settings-web-images', 86400, function () {
            return TbnSetting::whereIn('key', ['bg_web_image', 'thumb_web_image'])
                ->pluck('value', 'key');
        });

        $this->bgWebImageUrl = $images->get('bg_web_image');
    }

    public function render()
    {
        return <<<'HTML'
            <footer class="relative overflow-hidden bg-no-repeat bg-cover block px-5 pt-16 h-[45rem] sm:h-[24rem] body-font"
            style="background-image: url({{ asset('storage/'.$bgWebImageUrl) }})">
                <picture class="block max-w-6xl mx-auto mb-6">
                    <img class="max-w-[16rem]" src="{{ asset('storage/img/tbn-white.webp') }}" alt="tbn-logo">
                </picture>
                <div class="grid max-w-6xl grid-cols-1 gap-8 mx-auto lg:grid-cols-3">
                    <div class="text-white">
                        <h5 class="text-lg font-bold">Contacto:</h5>
                        <p class="mb-1 text-sm">CEO Ricardo Carlos Oropeza Zárate</p>
                        <p class="mb-1 text-sm">CFO Carla Ximena Vargas Soto de Oropeza</p>
                        <p class="mb-1 text-sm">Email: soporte@trabajonautas.com</p>
                        <p class="mb-1 text-sm">Celular: {{ substr(env('SUPPORT_PHONE'), 3, 8) }}</p>
                        <p class="mb-2 text-xs">&copy; {{ now()->year }} - Todos los derechos reservados</p>
                    </div>
                    <div class="text-white">
                        <h5 class="mb-2 text-lg font-bold">Redes sociales:</h5>
                        <nav class="flex flex-row gap-4 mb-10 text-sm list-none">
                            <li class="w-12 h-12 p-3 text-center transition-colors duration-150 rounded-full bg-tbn-dark hover:text-tbn-light">
                                <a  href="https://www.facebook.com/share/1DCXk3jMry/?mibextid=wwXIfr" target="_blank">
                                    <i class="text-2xl fa-brands fa-facebook"></i></a>
                            </li>    
                            <li class="w-12 h-12 p-3 text-center transition-colors duration-150 rounded-full bg-tbn-dark hover:text-tbn-light">
                                <a  href="https://www.youtube.com/@Trabajonautas" target="_blank">
                                    <i class="text-2xl fa-brands fa-youtube"></i></a>
                            </li>
                            <li class="w-12 h-12 p-3 text-center transition-colors duration-150 rounded-full bg-tbn-dark hover:text-tbn-light">
                                <a  href="https://www.instagram.com/trabajonautas/" target="_blank">
                                    <i class="text-2xl fa-brands fa-instagram"></i></a>
                            </li>
                            <li class="w-12 h-12 p-3 text-center transition-colors duration-150 rounded-full bg-tbn-dark hover:text-tbn-light">
                                <a  href="https://www.tiktok.com/@trabajonautas" target="_blank">
                                    <i class="text-2xl fa-brands fa-tiktok"></i></a>
                            </li>
                        </nav>
                    </div>
                </div>
                <div class="absolute right-4 -bottom-52 lg:right-36">
                    <img src="{{ asset('storage/ajustes/astro-greeting.webp') }}" alt="Astronauta" class="object-contain h-auto"
                        style="width: 16rem">
                </div>
            </footer>
        HTML;
    }
}
