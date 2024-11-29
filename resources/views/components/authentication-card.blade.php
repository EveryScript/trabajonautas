<section class="min-h-screen flex flex-col sm:justify-center items-center body-font">
    <div class="container px-5 py-24 mx-auto flex flex-wrap items-center">
        <div class="lg:w-2/5 md:w-1/2 md:pr-16 lg:pr-0 pr-0">
            {{ $logo }}
        </div>
        <div class="lg:w-2/5 md:w-1/2 bg-white shadow-md rounded-lg p-8 flex flex-col md:ml-auto w-full mt-10 md:mt-0">
            {{ $slot }}
        </div>
    </div>
</section>
