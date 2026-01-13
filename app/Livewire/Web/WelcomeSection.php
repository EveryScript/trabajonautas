<?php

namespace App\Livewire\Web;

use App\Models\TbnSetting;
use Livewire\Component;

class WelcomeSection extends Component
{
    public $bg_web_image, $thumb_web_image;

    public function render()
    {
        $this->bg_web_image = TbnSetting::where('key', 'bg_web_image')->first();
        $this->thumb_web_image = TbnSetting::where('key', 'thumb_web_image')->first();

        return <<<'HTML'
        <section class="bg-bottom bg-cover"
            style="background-image: url({{ asset('storage/'.$bg_web_image->value) }})">
            <div class="z-10 max-w-6xl md:h-[35rem] h-[40rem] flex flex-col-reverse md:flex-row justify-center items-center gap-2 lg:gap-4 mx-auto">
                <div class="px-6 mx-auto lg:w-7/12">
                    <h4 class="mb-2 text-3xl font-bold text-center text-white sm:text-left sm:text-4xl lg:text-5xl title-font"
                        data-aos="fade-up" data-aos-delay="200" data-aos-once="true">
                        Un universo de oportunidades de empleo para toda Bolivia</h4>
                    <p class="mb-5 text-center text-white lg:max-w-sm sm:text-left" data-aos="fade-up" data-aos-delay="400"
                        data-aos-once="true">
                        Bienvenido(a) al portal líder de oportunidades laborales en Bolivia. Encuentra la convocatoria
                        ideal
                        para tu perfil y da el siguiente
                        paso en tu carrera profesional con nosotros.</p>
                    <div class="mx-auto sm:mx-0" data-aos="fade-up" data-aos-delay="600" data-aos-once="true">
                        <div class="flex flex-col gap-2 text-center sm:flex-row sm:text-left">
                            <div>
                                <x-button type="button" class="inline-block bg-tbn-primary" href="{{ route('search') }}"
                                    wire:navigate>
                                    Iniciar búsqueda</x-button>
                            </div>
                            @if (auth()->user())
                                @if (in_array(env('CLIENT_ROLE'), auth()->user()->getRoleNames()->toArray()))
                                    <div>
                                        <x-button type="button"
                                            class="bg-tbn-secondary inline-block {{ auth()->user()->account->account_type_id > 1 ? 'hidden' : '' }}"
                                            href="{{ route('purchase-cards') }}" wire:navigate>
                                            Comprar ahora</x-button>
                                    </div>
                                @endif
                            @else
                                <div>
                                    <x-secondary-button type="button" class="inline-block bg-tbn-secondary"
                                        href="{{ route('purchase-cards') }}" wire:navigate>
                                        Comprar ahora</x-secondary-button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <picture class="z-1 lg:w-5/12" data-aos="zoom-in" data-aos-delay="800"
                    data-aos-duration="1000" data-aos-once="true">
                    <img class="z-0 animate-astronaut mx-auto max-w-[9rem] md:max-w-[15rem] lg:max-w-[20rem]"
                        src="{{ asset('storage/'.$thumb_web_image->value) }}" alt="astronaut-image">
                </picture>
            </div>
        </section>
        HTML;
    }
}
