@props(['logo_url'])

@php
    if (!$logo_url) {
        $logo_url = 'img/default.jpg';
    }
@endphp

<article
    class="w-full h-full flex sm:flex-row flex-col items-center sm:justify-start justify-center text-center sm:text-left
            bg-white shadow-md px-4 py-5 rounded-lg border border-gray-300 hover:border-tbn-primary cursor-pointer">
    <img alt="team" class="flex-shrink-0 rounded-lg w-16 h-16 object-cover object-center sm:mb-0 mb-4"
        src="{{ asset('storage/' . $logo_url) }}">
    <div class="flex-grow sm:pl-4 text-sm">
        <span class="text-xs font-normal text-gray-800">
            {{ $area }}
        </span>
        <h2 class="font-bold text-lg leading-6">{{ $title }}</h2>
        <h3 class="text-tbn-primary font-bold mb-1">{{ $company }}</h3>
        <div class="grid grid-cols-2 font-normal text-gray-800 text-sm">
            @if (isset($locations))
                <div>
                    <i class="fas fa-map-marker-alt text-red-500 pr-1"></i>
                    {{ $locations }}
                </div>
            @endif
            @if (isset($expiration))
                <div>
                    <i class="fas fa-calendar-alt text-red-500 pr-1"></i>
                    {{ $expiration }}
                </div>
            @endif
        </div>
    </div>
</article>
