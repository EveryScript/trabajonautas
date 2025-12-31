@props(['title', 'subtitle', 'link', 'image'])
<a href="{{ $link }}" target="_blank" class="group relative block h-96 rounded-2xl overflow-hidden shadow-xl text-left">
    <img src="{{ asset('storage/' . $image) }}" alt="bg-post"
        class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-110" />
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/60 to-transparent"></div>
    <div class="absolute bottom-0 left-0 px-6 py-10 text-white">
        <h3 class="text-2xl font-bold mt-3 transition-colors duration-300">
            {{ $title }}
        </h3>
        <p class="text-white text-sm"> {{ $subtitle }} </p>
    </div>
    <div
        class="absolute top-6 right-6 p-2 bg-white/80 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 scale-90 group-hover:scale-100">
        <i class="fa-solid fa-arrow-right text-tbn-primary mx-1"></i>
    </div>
</a>
