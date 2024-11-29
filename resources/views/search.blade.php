<x-web-layout>
    <h4 class="sm:text-3xl text-xl font-bold title-font text-center mt-10">¡Tu futuro laboral comienza aquí!</h4>
    <p class="text-sm text-center mt-3">Inicia la busqueda. La oportunidad de trabajo que estás buscando se encuentra
        aqui.
    </p>
    @livewire('web.search-announcement', ['title' => $title])
</x-web-layout>
