<x-web-layout>
    <!-- Landing -->
    <section class="mx-auto p-5"
        style="background-image: url('{{ asset('storage/img/tbn-landing.webp') }}'); background-size: cover; background-position: center;">
        <div class="max-w-[50rem] h-[30rem] flex flex-col justify-center items-center text-center mx-auto">
            <h4 class="text-tbn-light sm:text-3xl text-2xl font-bold title-font text-center mb-2">Más de 1000
                empresas trabajando con nosotros</h4>
            <p class="max-w-[50rem] mx-auto text-tbn-ligh mb-5">Bienvenido(a) al portal líder de
                oportunidades laborales en Bolivia. Encuentra la convocatoria ideal para tu perfil y da el siguiente
                paso en tu carrera profesional con nosotros.</p>
            <x-button-link href="{{ route('search') }}" wire:navigate>Iniciar busqueda</x-button-link>
        </div>
    </section>
    <!-- Latest announcements -->
    <section class="max-w-6xl sm:px-6 px-5 py-24 mx-auto">
        <div class="text-center py-5">
            <h4 class="text-tbn-light sm:text-3xl text-2xl font-bold title-font text-center mb-2">Las mejores
                convocatorias laborales de Bolivia a tu alcance</h4>
            <p class="text-center text-tbn-dark mb-5">Cada convocatoria es una oportunidad única para ti y en
                Trabajonautas.com las tenemos todas.</p>
            @livewire('web.recent-announcement')
            <div class="mt-5">
                <x-button-link href="{{ route('search') }}" wire:navigate>Ver más</x-button-link>
            </div>
        </div>
    </section>

    <!-- Instrucions -->
    <section class="body-font">
        <div class="max-w-6xl px-5 py-24 mx-auto">
            <h4 class="text-tbn-light sm:text-3xl text-2xl font-bold title-font text-center mb-2">Un mundo de
                oportunidades laborales en tus manos.</h4>
            <p class="text-center mb-10">Sigue las instrucciones y forma parte de la comunidad más grande de
                profesionales para
                encontrar tu próximo trabajo.</p>

            <div class="flex flex-wrap sm:-m-4 -mx-4 -mb-10 -mt-4 md:space-y-0 space-y-6">
                <div class="p-4 md:w-1/3 flex">
                    <div
                        class="w-12 h-12 inline-flex items-center justify-center rounded-full bg-tbn-medium text-tbn-light mb-4 flex-shrink-0">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="flex-grow pl-6">
                        <h2 class=" text-lg title-font font-medium mb-2">Registra tus datos</h2>
                        <p class="leading-relaxed text-base">Ingresa tu correo y contraseña para que te enviemos
                            información sobre nuestras convocatorias diariamente.</p>
                    </div>
                </div>
                <div class="p-4 md:w-1/3 flex">
                    <div
                        class="w-12 h-12 inline-flex items-center justify-center rounded-full bg-tbn-medium text-tbn-light mb-4 flex-shrink-0">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="flex-grow pl-6">
                        <h2 class=" text-lg title-font font-medium mb-2">Inicia la busqueda</h2>
                        <p class="leading-relaxed text-base">Encuentra información sobre convocatorias laborales de toda
                            Bolivia y de todo tipo de empresas que trabajan con nosotros.</p>
                    </div>
                </div>
                <div class="p-4 md:w-1/3 flex">
                    <div
                        class="w-12 h-12 inline-flex items-center justify-center rounded-full bg-tbn-medium text-tbn-light mb-4 flex-shrink-0">
                        <i class="fas fa-suitcase"></i>
                    </div>
                    <div class="flex-grow pl-6">
                        <h2 class=" text-lg title-font font-medium mb-2">Guarda tus resultados</h2>
                        <p class="leading-relaxed text-base">Agrega tus convocatorias favoritas en tu perfil y
                            compártelas en las redes sociales de manera fácil y cómoda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero -->
    <section class="bg-tbn-primary text-white body-font">
        <div class="max-w-6xl mx-auto flex px-5 md:flex-row flex-col items-center">
            <div class="lg:max-w-[20rem] lg:w-full md:w-1/2 w-5/6 mb-10 md:mb-0">
                <img class="object-cover object-center rounded pt-20" alt="hero"
                    src="{{ asset('storage/img/tbn-landing-2.webp') }}">
            </div>
            <div
                class="lg:flex-grow md:w-1/2 lg:pl-24 md:pl-16 flex flex-col md:items-start md:text-left items-center text-center">
                <h1 class="title-font sm:text-4xl text-3xl mb-4 font-bold">Tu futuro profesional es nuestra motivación
                    más grande.</h1>
                <p class="mb-8 leading-relaxed">Estamos comprometidos con el crecimiento y desarrollo profesional de los
                    bolivianos, conectándote con las mejores oportunidades laborales para impulsar tu carrera y alcanzar
                    tus metas.</p>
                <div class="flex justify-center">
                    <x-button-link href="{{ route('search') }}" class="bg-gray-800" wire:navigate>Iniciar
                        Busqueda</x-button-link>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="text-gray-600 body-font">
        <div class="max-w-6xl px-5 py-24 mx-auto">
            <div class="flex flex-wrap -m-4">
                <div class="lg:w-1/3 lg:mb-0 mb-6 p-4">
                    <div class="h-full text-center">
                        <img alt="testimonial"
                            class="w-20 h-20 mb-8 object-cover object-center rounded-full inline-block border-2 border-gray-200 bg-gray-100"
                            src="https://dummyimage.com/302x302">
                        <p class="leading-relaxed">"Gracias a esta plataforma, pude encontrar el trabajo que estaba
                            buscando en muy poco tiempo. Su sistema es fácil de usar y tienen convocatorias actualizadas
                            constantemente. ¡Recomendada! Sobre todo para profesionales"</p>
                        <span class="inline-block h-1 w-10 rounded bg-tbn-primary mt-6 mb-4"></span>
                        <h2 class="text-gray-900 font-medium title-font tracking-wider text-sm">HOLDEN CAULFIELD</h2>
                        <p class="text-gray-500">Ingeniero de Sistemas</p>
                    </div>
                </div>
                <div class="lg:w-1/3 lg:mb-0 mb-6 p-4">
                    <div class="h-full text-center">
                        <img alt="testimonial"
                            class="w-20 h-20 mb-8 object-cover object-center rounded-full inline-block border-2 border-gray-200 bg-gray-100"
                            src="https://dummyimage.com/300x300">
                        <p class="leading-relaxed">"La empresa se preocupa realmente por conectar a los profesionales
                            con empleadores de calidad en Bolivia. He tenido una excelente experiencia aplicando a
                            diversas vacantes y finalmente encontré una posición perfecta para mí."</p>
                        <span class="inline-block h-1 w-10 rounded bg-tbn-primary mt-6 mb-4"></span>
                        <h2 class="text-gray-900 font-medium title-font tracking-wider text-sm">ALPER KAMU</h2>
                        <p class="text-gray-500">Licenciado en minería</p>
                    </div>
                </div>
                <div class="lg:w-1/3 lg:mb-0 p-4">
                    <div class="h-full text-center">
                        <img alt="testimonial"
                            class="w-20 h-20 mb-8 object-cover object-center rounded-full inline-block border-2 border-gray-200 bg-gray-100"
                            src="https://dummyimage.com/305x305">
                        <p class="leading-relaxed">"Lo que más me gusta es la transparencia y el compromiso que tienen
                            con los candidatos. La plataforma es clara y confiable, y siempre cuentan con las mejores
                            ofertas laborales del país. Muy satisfecha con el servicio."</p>
                        <span class="inline-block h-1 w-10 rounded bg-tbn-primary mt-6 mb-4"></span>
                        <h2 class="text-gray-900 font-medium title-font tracking-wider text-sm">HENRY LETHAM</h2>
                        <p class="text-gray-500">Arquitecto profesional</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-web-layout>
