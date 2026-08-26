<?php

namespace App\Livewire\Web;

use App\Models\TbnSetting;
use App\Support\StoragePath;
use Livewire\Component;

class WelcomeSection extends Component
{
    public ?string $bgWebImageUrl = null;

    public ?string $thumbWebImageUrl = null;

    public function mount(): void
    {
        $images = TbnSetting::whereIn('key', ['bg_web_image', 'thumb_web_image'])
            ->pluck('value', 'key');

        $this->bgWebImageUrl = StoragePath::existingUrl($images->get('bg_web_image'));
        $this->thumbWebImageUrl = StoragePath::existingUrl($images->get('thumb_web_image'));
    }

    public function render()
    {
        return <<<'HTML'
        <section class="bg-bottom bg-cover bg-tbn-dark"
            @if ($bgWebImageUrl)
                style="background-image: url('{{ $bgWebImageUrl }}')"
            @endif>
            <div class="z-10 max-w-6xl md:h-[40rem] h-[45rem] flex flex-col-reverse md:flex-row justify-center items-center gap-2 lg:gap-4 mx-auto">
                <div class="px-6 mx-auto lg:w-7/12">
                    <h4 class="mb-2 text-3xl font-bold text-center text-white sm:text-left sm:text-4xl lg:text-5xl title-font"
                        data-aos="fade-up" data-aos-delay="200" data-aos-once="true">
                        Un universo de oportunidades de empleo para toda Bolivia</h4>
                    <p class="mb-5 text-center text-white lg:max-w-sm sm:text-left" data-aos="fade-up" data-aos-delay="400"
                        data-aos-once="true">
                        "Deja de buscar y empieza a postular". Somos la única plataforma que te envía solo las convocatorias laborales de las INSTITUCIONES PÚBLICAS, privadas, ONGs y empresas mixtas que encajan con tu perfil profesional y tu ubicación. Somos Trabajonautas.com.</p>
                    <div class="mx-auto sm:mx-0" data-aos="fade-up" data-aos-delay="600" data-aos-once="true">
                        <div class="flex flex-col gap-2 text-center sm:flex-row sm:text-left">
                            <div>
                                <x-button type="button" class="inline-block bg-tbn-primary" href="{{ route('search') }}"
                                    wire:navigate>
                                    Iniciar búsqueda</x-button>
                            </div>
                            @if (!auth()->user())
                                <div>
                                    <x-secondary-button type="button" class="inline-block bg-tbn-secondary"
                                        href="{{ route('purchase-cards') }}" wire:navigate>
                                        Comprar ahora</x-secondary-button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="z-1 lg:w-5/12" data-aos="zoom-in" data-aos-delay="800"
                    data-aos-duration="1000" data-aos-once="true">
                    @if ($thumbWebImageUrl)
                        <img class="z-0 animate-astronaut mx-auto max-w-[9rem] md:max-w-[15rem] lg:max-w-[20rem]"
                            src="{{ $thumbWebImageUrl }}" alt="Astronauta">
                    @else
                        <div class="flex items-center justify-center mx-auto text-white/70 w-36 h-36 md:w-60 md:h-60"
                            aria-label="Imagen del astronauta no configurada">
                            <i class="text-7xl fa-solid fa-user-astronaut" aria-hidden="true"></i>
                        </div>
                    @endif
                </div>
            </div>
        </section>
        HTML;
    }
}
