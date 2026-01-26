{{-- @props([
    'logo_flag' => true,
    'logo_url' => '#',
    'title' => 'Mi título',
    'pro' => false,
    'locations' => 'Mis ubicaciones',
    'created_at' => ''
]) --}}
@props([
    'announce' => null,
    'client' => false,
])

<a href="{{ $announce->pro && !$client ? route('purchase-cards') : route('result', ['id' => $announce->id]) }}"
    wire:navigate>
    <article
        class="relative flex flex-col justify-start w-full h-full gap-4 px-6 py-5 transition-colors duration-150 bg-white border border-gray-300 rounded-lg shadow-md cursor-pointer md:flex-row md:gap-6 md:items-center text-start dark:bg-tbn-dark dark:text-tbn-light dark:border-tbn-secondary hover:border-tbn-primary">
        <img class="flex-shrink-0 object-cover object-center w-16 h-16 rounded-lg"
            src="{{ $announce->company ? asset('storage/' . $announce->company->company_image) : asset('storage/empresas/tbn-new-default.webp') }}">
        <div class="flex-1 text-sm">
            <p class="absolute top-4 right-6 {{ $announce->pro ? '' : 'hidden' }}">
                <i class="text-sm fas fa-crown text-tbn-primary"></i>
            </p>
            <p class="text-xs font-normal text-tbn-dark dark:text-tbn-light">
                <span class="pr-5">Publicado {{ Carbon\Carbon::parse($announce->created_at)->diffForHumans() }}</span>
            </p>
            <h2 class="my-1 text-lg font-bold leading-6 dark:text-white">{{ $announce->announce_title }}</h2>
            <div class="grid w-full grid-cols-2 mt-1">
                @if ($announce->company)
                    <h3 class="font-bold text-tbn-primary">{{ $announce->company->company_name }}</h3>
                @else
                    <p class="text-sm text-tbn-dark dark:text-tbn-light">(sin empresa)</p>
                @endif
                <p class="relative">
                    <i class="inline mr-2 fas fa-map-marker-alt text-tbn-primary"></i>
                    {{ $announce->locations[0]->location_name }}
                    @if ($announce->locations->count() > 1)
                        <span class="text-xs text-gray-400">
                            ({{ $announce->locations->count() - 1 }} más)
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </article>
</a>
