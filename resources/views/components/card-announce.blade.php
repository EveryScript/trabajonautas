@props(['logo_url', 'logo_flag', 'pro'])

@php
    if (!isset($logo_url)) {
        $logo_url = 'img/default.jpg';
    }
    if (!isset($logo_flag)) {
        $logo_flag = true;
    }
    if (!isset($pro)) {
        $pro = false;
    }
@endphp

<article
    class="w-full h-full relative flex flex-row justify-start gap-4 text-start
            bg-white shadow-md px-6 py-5 rounded-lg border border-gray-300 hover:border-tbn-primary cursor-pointer">
    <img alt="team"
        class="flex-shrink-0 rounded-lg w-16 h-16 object-cover object-center sm:mb-0 mb-4 {{ $logo_flag ? '' : 'hidden' }}"
        src="{{ asset('storage/' . $logo_url) }}">
    <div class="flex-grow md:pl-1 sm:pl-4 text-sm">
        <span class="absolute top-4 right-6 {{ $pro ? '' : 'hidden' }}"><i
                class="fas fa-crown text-sm text-tbn-secondary"></i></span>
        <span class="text-xs font-normal text-tbn-dark">
            <div class="pr-5">
                {{ $area }}
            </div>
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
