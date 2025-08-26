@props([
    'title' => '',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'w-full text-center py-24']) }}>
    <picture class="block mb-2">
        <img src="{{ asset('storage/img/empty.webp') }}" alt="empty" class="max-w-[8rem] mx-auto mb-2">
    </picture>
    <h5 class="font-medium text-lg mb-2">{{ $title }}</h5>
    <p class="text-tbn-dark text-sm">{{ $description }}</p>
    {{ $slot }}
</div>
