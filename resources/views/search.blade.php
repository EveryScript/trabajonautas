<x-web-layout>
    <h4 class="font-baloo text-tbn-primary sm:text-3xl text-2xl font-bold title-font text-center pt-10">
        ¡Tu futuro laboral comienza aquí!</h4>
    <p class="text-tbn-dark dark:text-white text-sm text-center p-4">
        Inicia la búsqueda. La oportunidad de trabajo que estás buscando se encuentra aquí.</p>
    @livewire('web.search-announcement', ['title' => $title])
</x-web-layout>
