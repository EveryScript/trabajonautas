@props(['logo_url', 'pro'])

@php
    if (!isset($logo_url)) {
        $logo_url = 'img/default.jpg';
    }
    if (!isset($pro)) {
        $pro = false;
    }
@endphp

<article
    class="w-full h-full relative flex sm:flex-row flex-col items-center sm:justify-start justify-center text-center sm:text-left
            bg-white shadow-md px-6 py-5 rounded-lg border border-gray-300 hover:border-tbn-primary cursor-pointer">
    <img alt="team" class="flex-shrink-0 rounded-lg w-16 h-16 object-cover object-center sm:mb-0 mb-4"
        src="{{ asset('storage/' . $logo_url) }}">
    <div class="flex-grow sm:pl-4 text-sm">
        <span class="absolute top-4 right-6 {{ $pro ? '' : 'hidden' }}"><i
                class="fas fa-crown text-sm text-orange-500"></i></span>
        <span class="text-xs font-normal text-gray-800">
            {{ $area }}
        </span>
        <h2 class="font-bold text-lg leading-6 my-1">{{ $title }}</h2>
        <div class="w-full grid grid-cols-2 mt-1">
            <h3 class="text-tbn-primary font-bold">{{ $company }}</h3>
            @if (isset($locations))
                <div>
                    <i class="inline fas fa-map-marker-alt text-red-500 pr-1"></i>
                    {{ $locations }}
                </div>
            @endif
        </div>
    </div>
</article>
