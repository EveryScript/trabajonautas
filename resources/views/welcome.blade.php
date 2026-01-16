<x-web-layout>
    <!-- Popup Ad Quimeras Bolivia -->
    <div
        class="fixed right-4 bottom-4 sm:bottom-1/2 top-11/12 sm:top-1/2 z-[100] translate-y-11/12 sm:-translate-y-1/2 outline-none">
        <div tabindex="0"
            class="group relative flex flex-col sm:flex-row h-16 w-16 cursor-pointer items-center justify-end overflow-hidden rounded-full bg-white shadow-2xl transition-all duration-500 ease-in-out border border-tbn-light 
           focus-within:h-[24rem] sm:focus-within:h-[15rem] focus-within:w-[18rem] sm:focus-within:w-[30rem] focus-within:rounded-2xl focus-within:cursor-default outline-none">
            <div
                class="absolute right-0 flex items-center justify-center w-16 h-16 p-1 transition-all duration-500 shrink-0 group-focus-within:top-8 sm:group-focus-within:top-0 sm:group-focus-within:left-4 group-focus-within:relative group-focus-within:h-32 group-focus-within:w-32 group-focus-within:scale-120 group-focus-within:p-4">
                <img src="https://quimerasbolivia.com/img/circle-quim.png" alt="Avatar"
                    class="object-cover w-full h-full rounded-full shadow-md" />
            </div>
            <div
                class="flex flex-col p-6 transition-opacity duration-300 opacity-0 pointer-events-none group-focus-within:opacity-100 group-focus-within:pointer-events-auto group-focus-within:delay-200">
                <div class="pr-16">
                    <h3 class="text-xl font-bold text-tbn-dark">¿Tienes problemas para mejorar tu currículum?</h3>
                    <p class="w-full mt-2 text-xs leading-relaxed text-tbn-dark">
                        Encuentra todos los cursos que necesitas para postular en las mejores empresas de Bolivia.
                    </p>
                </div>
                <div class="flex mt-4 sm:gap-3">
                    <a href="https://www.quimerasbolivia.com" target="_blank"
                        class="px-4 py-2 text-xs font-semibold text-white transition-all rounded-lg bg-tbn-primary hover:bg-tbn-secondary active:scale-95">
                        Ir a Quimeras Bolivia
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Landing -->
    <livewire:web.welcome-section />
    <!-- Latest announcements -->
    <section class="max-w-6xl px-3 py-24 mx-auto sm:px-6">
        <div class="py-5 text-center">
            <h4 class="mb-2 text-2xl font-bold text-center font-baloo text-tbn-primary sm:text-3xl title-font">
                Las mejores convocatorias laborales de Bolivia a tu alcance</h4>
            <p class="mb-5 text-center text-tbn-secondary dark:text-tbn-light">
                Cada convocatoria es una oportunidad única para ti y en Trabajonautas.com las tenemos todas.</p>
            @livewire('web.recent-announcement')
            <div class="mt-5">
                <x-button class="bg-tbn-primary" href="{{ route('search') }}" wire:navigate>
                    Ver más</x-button>
            </div>
        </div>
    </section>
    <!-- Latest Notices -->
    <section class="max-w-6xl px-3 py-24 mx-auto sm:px-6">
        <div class="py-5 text-center">
            <h4 class="mb-2 text-2xl font-bold text-center font-baloo text-tbn-primary sm:text-3xl title-font">
                Últimas noticias</h4>
            <p class="mb-5 text-center text-tbn-secondary dark:text-tbn-light">
                Tenemos las noticias más destacadas de las últimas horas</p>
            <!-- Livewire components -->
            <livewire:web.recent-notices />
        </div>
    </section>
    <!-- Photo section -->
    <section class="max-w-6xl px-3 py-12 mx-auto sm:px-6">
        <div class="py-24 mx-auto">
            <div class="flex flex-col items-center gap-12 md:flex-row">
                <div class="w-full md:w-1/2" data-aos="fade-up" data-aos-delay="200" data-aos-once="true">
                    <div class="relative">
                        <div class="absolute w-24 h-24 rounded-full -top-4 -left-4 bg-orange-500/10 blur-2xl"></div>
                        <img src="{{ asset('storage/img/tbn-photo-1.webp') }}" alt="photo-1"
                            class="relative z-10 object-cover border-b-8 shadow-2xl border-tbn-primary rounded-2xl" />
                        <div
                            class="absolute z-20 hidden p-6 shadow-lg bg-tbn-primary -bottom-6 -right-6 rounded-xl lg:block">
                            <p class="text-xl font-bold text-white">Tu portunidad</p>
                            <p class="text-sm text-orange-100">¡Ahora mismo!</p>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2" data-aos="zoom-in" data-aos-delay="400" data-aos-once="true">
                    <div
                        class="inline-block px-4 py-1.5 mb-4 text-sm font-semibold tracking-wider text-tbn-primary uppercase bg-orange-100 dark:text-white dark:bg-tbn-primary/70 rounded-full">
                        Oportunidades infinitas
                    </div>

                    <h2 class="mb-6 text-3xl font-extrabold leading-tight text-tbn-dark dark:text-white md:text-5xl">
                        ¡Tu profesión está lista para <span class="text-tbn-primary">despegar</span>!
                    </h2>

                    <p class="mb-8 text-lg leading-relaxed text-tbn-secondary dark:text-tbn-light">
                        Tu próximo gran desafío profesional no está a años luz de distancia. Explora cada una de las
                        convocatorias que tenemos para ti y aterriza en las mejores empresas e instituciones de Bolivia.
                    </p>
                </div>
            </div>
        </div>
        <div class="py-24 mx-auto">
            <div class="flex flex-col items-center gap-12 md:flex-row-reverse">
                <div class="w-full md:w-1/2" data-aos="fade-up" data-aos-delay="200" data-aos-once="true">
                    <div class="relative">
                        <div class="absolute w-24 h-24 rounded-full -top-4 -left-4 bg-orange-500/10 blur-2xl"></div>
                        <img src="{{ asset('storage/img/tbn-photo-2.webp') }}" alt="photo-2"
                            class="relative z-10 object-cover border-b-8 shadow-2xl border-tbn-primary rounded-2xl" />
                        <div
                            class="absolute z-20 hidden p-6 shadow-lg bg-tbn-primary -bottom-6 -right-6 rounded-xl lg:block">
                            <p class="text-xl font-bold text-white">Explora en el</p>
                            <p class="text-sm text-orange-100">universo</p>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2" data-aos="zoom-in" data-aos-delay="400" data-aos-once="true">
                    <div
                        class="inline-block px-4 py-1.5 mb-4 text-sm font-semibold tracking-wider text-tbn-primary uppercase bg-orange-100 dark:text-white dark:bg-tbn-primary/70 rounded-full">
                        convocatorias a tu alcance
                    </div>
                    <h2 class="mb-6 text-3xl font-extrabold leading-tight text-tbn-dark dark:text-white md:text-5xl">
                        Un gran paso para tu carrera <span class="text-tbn-primary">profesional</span>
                    </h2>
                    <p class="mb-8 text-lg leading-relaxed text-tbn-secondary dark:text-tbn-light">
                        Tenemos el radar configurado para detectar las mejores convocatorias de empleo. Deja de flotar
                        en la incertidumbre y encuentra tu base de operaciones ideal.
                    </p>
                </div>
            </div>
        </div>
        <div class="py-24 mx-auto">
            <div class="flex flex-col items-center gap-12 md:flex-row">
                <div class="w-full md:w-1/2" data-aos="fade-up" data-aos-delay="200" data-aos-once="true">
                    <div class="relative">
                        <div class="absolute w-24 h-24 rounded-full -top-4 -left-4 bg-orange-500/10 blur-2xl"></div>
                        <img src="{{ asset('storage/img/tbn-photo-3.webp') }}" alt="photo-3"
                            class="relative z-10 object-cover border-b-8 shadow-2xl border-tbn-primary rounded-2xl" />
                        <div
                            class="absolute z-20 hidden p-6 shadow-lg bg-tbn-primary -bottom-6 -right-6 rounded-xl lg:block">
                            <p class="text-xl font-bold text-white">Impulsa tus</p>
                            <p class="text-sm text-orange-100">capacidades</p>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2" data-aos="zoom-in" data-aos-delay="400" data-aos-once="true">
                    <div
                        class="inline-block px-4 py-1.5 mb-4 text-sm font-semibold tracking-wider text-tbn-primary uppercase dark:text-white dark:bg-tbn-primary/70 bg-orange-100 rounded-full">
                        Información actualizada
                    </div>
                    <h2 class="mb-6 text-3xl font-extrabold leading-tight text-tbn-dark dark:text-white md:text-5xl">
                        ¿Listo para dar el <span class="text-tbn-primary">gran salto</span>?
                    </h2>
                    <p class="mb-8 text-lg leading-relaxed text-tbn-secondary dark:text-tbn-light">
                        Un pequeño paso para tu carrera, un gran salto para tu vida profesional. Informate cada día de
                        las mejores convocatorias de empleo de Bolivia.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- Instrucions cards -->
    <section class="body-font">
        <div class="max-w-6xl px-2 py-24 mx-auto sm:px-0">
            <div class="px-5 mb-8">
                <h4 class="mb-2 text-2xl font-bold text-center font-baloo text-tbn-primary sm:text-3xl title-font"
                    data-aos="fade-up" data-aos-delay="200" data-aos-once="true">
                    Un universo de oportunidades laborales en tus manos.</h4>
                <p class="mb-10 text-center text-md text-tbn-dark dark:text-tbn-light" data-aos="fade-up"
                    data-aos-delay="400" data-aos-once="true">
                    Sigue las instrucciones y forma parte de la comunidad más grande de profesionales, deja de buscar y empieza a postular.</p>
            </div>
            <div class="grid grid-cols-1 gap-4 px-5 sm:grid-cols-2 dark:text-tbn-light">
                <div data-aos="fade-up" data-aos-delay="600" data-aos-once="true"
                    class="relative px-6 pt-10 pb-8 overflow-hidden transition-all duration-300 bg-white border border-white rounded-lg shadow-xl cursor-pointer group dark:bg-tbn-dark dark:border-tbn-secondary hover:-translate-y-1 hover:shadow-2xl sm:mx-auto sm:max-w-5xl sm:px-10">
                    <span
                        class="absolute top-10 z-0 h-20 w-20 rounded-full bg-tbn-primary transition-all duration-300 group-hover:scale-[15]"></span>
                    <div class="relative z-10 max-w-md mx-auto">
                        <span
                            class="grid w-20 h-20 transition-all duration-300 rounded-full place-items-center bg-tbn-primary group-hover:bg-tbn-primary">
                            <i class="text-2xl text-white fas fa-user"></i>
                        </span>
                        <div
                            class="p-5 space-y-6 text-base leading-7 transition-all duration-300 text-tbn-dark dark:text-tbn-light group-hover:text-white/90">
                            <h2 class="text-lg font-medium dark:text-white title-font">Registra tus datos</h2>
                            <p
                                class="text-sm leading-relaxed md:text-base dark:text-tbn-light dark:group-hover:text-white/90">
                                Ingresa tu correo y contraseña para que te enviemos
                                información sobre nuestras convocatorias diariamente.</p>
                        </div>
                    </div>
                </div>
                <div data-aos="fade-up" data-aos-delay="800" data-aos-once="true"
                    class="relative px-6 pt-10 pb-8 overflow-hidden transition-all duration-300 bg-white border border-white rounded-lg shadow-xl cursor-pointer group dark:bg-tbn-dark dark:border-tbn-secondary hover:-translate-y-1 hover:shadow-2xl sm:mx-auto sm:max-w-5xl sm:px-10">
                    <span
                        class="absolute top-10 z-0 h-20 w-20 rounded-full bg-tbn-primary transition-all duration-300 group-hover:scale-[15]"></span>
                    <div class="relative z-10 max-w-md mx-auto">
                        <span
                            class="grid w-20 h-20 transition-all duration-300 rounded-full place-items-center bg-tbn-primary group-hover:bg-tbn-primary">
                            <i class="text-2xl text-white fas fa-search"></i>
                        </span>
                        <div
                            class="p-5 space-y-6 text-base leading-7 transition-all duration-300 text-tbn-dark dark:text-tbn-light group-hover:text-white/90">
                            <h2 class="text-lg font-medium dark:text-white title-font">Inicia la búsqueda</h2>
                            <p
                                class="text-sm leading-relaxed md:text-base dark:text-tbn-light dark:group-hover:text-white/90">
                                Encuentra información sobre convocatorias laborales de toda Bolivia y de todo tipo de
                                empresas que trabajan con nosotros.</p>
                        </div>
                    </div>
                </div>
                <div data-aos="fade-up" data-aos-delay="1000" data-aos-once="true"
                    class="relative px-6 pt-10 pb-8 overflow-hidden transition-all duration-300 bg-white border border-white rounded-lg shadow-xl cursor-pointer group dark:bg-tbn-dark dark:border-tbn-secondary hover:-translate-y-1 hover:shadow-2xl sm:mx-auto sm:max-w-5xl sm:px-10">
                    <span
                        class="absolute top-10 z-0 h-20 w-20 rounded-full bg-tbn-primary transition-all duration-300 group-hover:scale-[15]"></span>
                    <div class="relative z-10 max-w-md mx-auto">
                        <span
                            class="grid w-20 h-20 transition-all duration-300 rounded-full place-items-center bg-tbn-primary group-hover:bg-tbn-primary">
                            <i class="text-2xl text-white fas fa-suitcase"></i>
                        </span>
                        <div
                            class="p-5 space-y-6 text-base leading-7 transition-all duration-300 text-tbn-dark dark:text-tbn-light group-hover:text-white/90">
                            <h2 class="text-lg font-medium dark:text-white title-font">Guarda tus resultados</h2>
                            <p
                                class="text-sm leading-relaxed md:text-base dark:text-tbn-light dark:group-hover:text-white/90">
                                Agrega tus convocatorias favoritas en tu perfil y compártelas en las redes sociales de
                                manera fácil y cómoda.</p>
                        </div>
                    </div>
                </div>
                <div data-aos="fade-up" data-aos-delay="1200" data-aos-once="true"
                    class="relative px-6 pt-10 pb-8 overflow-hidden transition-all duration-300 bg-white border border-white rounded-lg shadow-xl cursor-pointer group dark:bg-tbn-dark dark:border-tbn-secondary hover:-translate-y-1 hover:shadow-2xl sm:mx-auto sm:max-w-5xl sm:px-10">
                    <span
                        class="absolute top-10 z-0 h-20 w-20 rounded-full bg-tbn-primary transition-all duration-300 group-hover:scale-[15]"></span>
                    <div class="relative z-10 max-w-md mx-auto">
                        <span
                            class="grid w-20 h-20 transition-all duration-300 rounded-full place-items-center bg-tbn-primary group-hover:bg-tbn-primary">
                            <i class="text-2xl text-white fas fa-crown"></i>
                        </span>
                        <div
                            class="p-5 space-y-6 text-base leading-7 transition-all duration-300 text-tbn-dark dark:text-tbn-light group-hover:text-white/90">
                            <h2 class="text-lg font-medium dark:text-white title-font">Convocatorias PRO</h2>
                            <p
                                class="text-sm leading-relaxed md:text-base dark:text-tbn-light dark:group-hover:text-white/90">
                                Desbloquea todas las convocatorias para enterarte de las últimas contrataciones y
                                encontrar tu trabajo hoy mismo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-web-layout>
