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
    class="w-full h-full relative flex md:flex-row flex-col justify-start gap-4 md:gap-6 text-start
            bg-white shadow-md px-6 py-5 rounded-lg border border-gray-300 hover:border-tbn-primary cursor-pointer">
    @if ($logo_flag)
        <picture class="w-10 h-10 md:w-16 md:h-16">
            <img alt="team" class="w-full rounded-md object-cover object-center {{ $logo_flag ? '' : 'hidden' }}"
                src="{{ asset('storage/' . $logo_url) }}">
        </picture>
    @endif
    <div class="flex-1 text-sm">
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
