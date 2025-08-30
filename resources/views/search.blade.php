<x-web-layout>
    <h4 class="font-baloo text-tbn-primary sm:text-3xl text-2xl font-bold title-font text-center mt-10 mx-4">
        ¡Tu futuro laboral comienza aquí!</h4>
    <p class="text-sm text-center mt-2 mx-4">
        Inicia la busqueda. La oportunidad de trabajo que estás buscando se encuentra aqui.</p>
    @livewire('web.search-announcement', ['title' => $title])
</x-web-layout>
