@props([
    'logo_flag' => true,
    'logo_url' => '',
    'area' => '',
    'title' => '',
    'pro' => false,
    'locations' => [],
])

<article
    class="w-full h-full relative flex md:flex-row flex-col justify-start gap-4 md:gap-6 text-start
            bg-white shadow-md px-6 py-5 rounded-lg border border-gray-300 hover:border-tbn-primary cursor-pointer">
    <img class="flex-shrink-0 rounded-lg w-16 h-16 object-cover object-center {{ $logo_flag ? '' : 'hidden' }}"
        src="{{ !empty($logo_url) ? asset('storage/' . $logo_url) : asset('storage/empresas/tbn-default.webp') }}">
    <div class="flex-1 text-sm">
        <p class="absolute top-4 right-6 {{ $pro ? '' : 'hidden' }}">
            <i class="fas fa-crown text-sm text-tbn-secondary"></i>
        </p>
        <p class="text-xs font-normal text-tbn-dark">
            <span class="pr-5">{{ $area }}</span>
        </p>
        <h2 class="font-bold text-lg leading-6 my-1">{{ $title }}</h2>
        <div class="w-full grid grid-cols-2 mt-1">
            @if (isset($company))
                <h3 class="text-tbn-primary font-bold">{{ $company }}</h3>
            @else
                <p class="text-tbn-dark text-sm">(sin empresa)</p>
            @endif
            <p class="relative">
                <i class="inline fas fa-map-marker-alt text-red-500 mr-2"></i>{{ $locations }}
            </p>
        </div>
    </div>
</article>
