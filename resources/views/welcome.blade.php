<x-web-layout>
    <!-- Landing -->
    <section class="bg-cover" style="background-image: url({{ asset('storage/img/tbn-space.webp') }})">
        <div class="max-w-6xl md:h-[35rem] h-[30rem] flex flex-row justify-center items-center gap-4 mx-auto">
            <div class="lg:w-7/12 px-6 mx-auto">
                <h4 class="text-white text-center sm:text-left sm:text-5xl text-3xl font-bold title-font mb-2">
                    Un universo de oportunidades de empleo para toda Bolivia</h4>
                <p class="text-white text-center sm:text-left mb-5">
                    Bienvenido(a) al portal líder de oportunidades laborales en Bolivia. Encuentra la convocatoria
                    ideal
                    para tu perfil y da el siguiente
                    paso en tu carrera profesional con nosotros.</p>
                <div class="mx-auto sm:mx-0">
                    <div class="flex flex-col sm:flex-row gap-2 text-center sm:text-left">
                        <div>
                            <x-button-link class="bg-tbn-primary inline-block" href="{{ route('search') }}" wire:navigate>
                                Iniciar busqueda</x-button-link>
                        </div>
                        @if (auth()->user())
                            @if (in_array(env('CLIENT_ROLE'), auth()->user()->getRoleNames()->toArray()))
                                <div>
                                    <x-button-link
                                        class="bg-tbn-secondary inline-block {{ auth()->user()->account->account_type_id > 1 ? 'hidden' : '' }}"
                                        href="{{ route('purchase-cards') }}" wire:navigate>
                                        Comprar ahora</x-button-link>
                                </div>
                            @endif
                        @else
                            <div>
                                <x-button-link class="bg-tbn-secondary inline-block"
                                    href="{{ route('purchase-cards') }}" wire:navigate>
                                    Comprar ahora</x-button-link>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <picture class="lg:w-5/12 hidden lg:block">
                <img class="mx-auto md:max-w-[25rem]" src="{{ asset('storage/img/tbn-new-astro.webp') }}"
                    alt="">
            </picture>
        </div>
    </section>
    <!-- Latest announcements -->
    <section class="max-w-6xl sm:px-6 px-5 py-24 mx-auto">
        <div class="text-center py-5">
            <h4 class="font-baloo text-tbn-primary sm:text-3xl text-2xl font-bold title-font text-center mb-2">
                Las mejores convocatorias laborales de Bolivia a tu alcance</h4>
            <p class="text-center text-tbn-secondary mb-5">
                Cada convocatoria es una oportunidad única para ti y en Trabajonautas.com las tenemos todas.</p>
            @livewire('web.recent-announcement')
            <div class="mt-5">
                <x-button-link class="bg-tbn-primary" href="{{ route('search') }}" wire:navigate>
                    Ver más</x-button-link>
            </div>
        </div>
    </section>
    <!-- Latest Notices -->
    <section class="max-w-6xl sm:px-6 px-5 py-24 mx-auto">
        <div class="text-center py-5">
            <h4 class="font-baloo text-tbn-primary sm:text-3xl text-2xl font-bold title-font text-center mb-2">
                Últimas noticias</h4>
            <p class="text-center text-tbn-secondary mb-5">
                Tenemos las noticias más destacadas de las últimas horas</p>
            <!-- Livewire components -->
            <livewire:web.recent-notices />
        </div>
    </section>
    <!-- Photo section -->
    <section class="width-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 text-tbn-secondary">
            <picture class="h-auto">
                <img class="w-full" src="{{ asset('storage/img/tbn-photo-1.webp') }}" alt="photo-1">
            </picture>
            <div class="w-full lg:w-4/5 px-5 py-20 lg:px-20 lg:py-12 flex flex-col justify-center relative">
                <span class="text-tbn-primary bg-gray-50 p-9 rounded-full absolute -left-14 hidden lg:block">
                    <i class="fas fa-paper-plane text-5xl"></i>
                </span>
                <h3 class="text-3xl font-bold mb-2">Lorem ipsum</h3>
                <p class="text-sm text-tbn-primary italic mb-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                </p>
                <p class="text-md">Lorem ipsum dolor sit amet consectetur adipisicing elit. Minima dignissimos veritatis
                    magnam et laboriosam est ut atque id placeat! Quod dolores ipsum quaerat esse. Maxime accusamus,
                    esse accusantium sed recusandae veniam a fugit officia alias!</p>
            </div>
            <picture class="h-auto">
                <img class="w-full" src="{{ asset('storage/img/tbn-photo-2.webp') }}" alt="photo-1">
            </picture>
            <div class="w-full lg:w-4/5 px-5 py-20 lg:px-20 lg:py-12 flex flex-col justify-center relative">
                <span class="text-tbn-primary bg-gray-50 p-9 rounded-full absolute -left-14 hidden lg:block">
                    <i class="fas fa-rocket text-5xl"></i>
                </span>
                <h3 class="text-3xl font-bold mb-2">Lorem ipsum</h3>
                <p class="text-sm text-tbn-primary italic mb-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                </p>
                <p class="text-md">Lorem ipsum dolor sit amet consectetur adipisicing elit. Minima dignissimos veritatis
                    magnam et laboriosam est ut atque id placeat! Quod dolores ipsum quaerat esse. Maxime accusamus,
                    esse accusantium sed recusandae veniam a fugit officia alias!</p>
            </div>
            <picture class="h-auto">
                <img class="w-full" src="{{ asset('storage/img/tbn-photo-3.webp') }}" alt="photo-1">
            </picture>
            <div class="w-full lg:w-4/5 px-5 py-20 lg:px-20 lg:py-12 lg:flex flex-col justify-center hidden relative">
                <span class="text-tbn-primary bg-gray-50 p-9 rounded-full absolute -left-14 hidden lg:block">
                    <i class="fas fa-compass text-5xl"></i>
                </span>
                <h3 class="text-3xl font-bold mb-2">Lorem ipsum</h3>
                <p class="text-sm text-tbn-primary italic mb-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                </p>
                <p class="text-md">Lorem ipsum dolor sit amet consectetur adipisicing elit. Minima dignissimos veritatis
                    magnam et laboriosam est ut atque id placeat! Quod dolores ipsum quaerat esse. Maxime accusamus,
                    esse accusantium sed recusandae veniam a fugit officia alias!</p>
            </div>
        </div>
    </section>
    <!-- Instrucions -->
    <section class="body-font">
        <div class="max-w-6xl px-5 py-24 mx-auto">
            <div class="mb-8">
                <h4 class="font-baloo text-tbn-primary sm:text-3xl text-2xl font-bold title-font text-center mb-2">
                    Un mundo de oportunidades laborales en tus manos.</h4>
                <p class="text-tbn-dark text-center text-sm mb-10">
                    Sigue las instrucciones y forma parte de la comunidad más grande de profesionales para encontrar tu
                    próximo trabajo.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="mt-10 p-8 flex hover:shadow-2xl shadow-gray-900 transition-all duration-200 rounded-md relative">
                    <div
                        class="absolute -top-8 left-10 w-12 h-12 inline-flex items-center justify-center rounded-full bg-tbn-primary mb-4 flex-shrink-0">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-grow pl-2">
                        <h2 class=" text-lg title-font font-medium mb-2">Registra tus datos</h2>
                        <p class="leading-relaxed text-sm md:text-base text-tbn-dark">
                            Ingresa tu correo y contraseña para que te enviemos información sobre nuestras convocatorias
                            diariamente.</p>
                    </div>
                </div>
                <div
                    class="mt-10 p-8 flex hover:shadow-2xl shadow-gray-900 transition-all duration-200 rounded-md relative">
                    <div
                        class="absolute -top-8 left-10 w-12 h-12 inline-flex items-center justify-center rounded-full bg-tbn-primary mb-4 flex-shrink-0">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <div class="flex-grow pl-2">
                        <h2 class=" text-lg title-font font-medium mb-2">Inicia la busqueda</h2>
                        <p class="leading-relaxed text-sm md:text-base text-tbn-dark">
                            Encuentra información sobre convocatorias laborales de toda Bolivia y de todo tipo de
                            empresas que trabajan con nosotros.</p>
                    </div>
                </div>
                <div
                    class="mt-10 p-8 flex hover:shadow-2xl shadow-gray-900 transition-all duration-200 rounded-md relative">
                    <div
                        class="absolute -top-8 left-10 w-12 h-12 inline-flex items-center justify-center rounded-full bg-tbn-primary mb-4 flex-shrink-0">
                        <i class="fas fa-suitcase text-white"></i>
                    </div>
                    <div class="flex-grow pl-2">
                        <h2 class=" text-lg title-font font-medium mb-2">Guarda tus resultados</h2>
                        <p class="leading-relaxed text-sm md:text-base text-tbn-dark">
                            Agrega tus convocatorias favoritas en tu perfil y compártelas en las redes sociales de
                            manera fácil y cómoda.</p>
                    </div>
                </div>
                <div
                    class="mt-10 p-8 flex hover:shadow-2xl shadow-gray-900 transition-all duration-200 rounded-md relative">
                    <div
                        class="absolute -top-8 left-10 w-12 h-12 inline-flex items-center justify-center rounded-full bg-tbn-primary mb-4 flex-shrink-0">
                        <i class="fas fa-crown text-white"></i>
                    </div>
                    <div class="flex-grow pl-2">
                        <h2 class=" text-lg title-font font-medium mb-2">Convocatorias PRO</h2>
                        <p class="leading-relaxed text-sm md:text-base text-tbn-dark">
                            Desbloquea todas las convocatorias para enterarte de las últimas contrataciones y encontrar
                            tu trabajo hoy mismo.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-web-layout>
