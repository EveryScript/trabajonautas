@props([
    'title' => '',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'w-full text-center py-24']) }}>
    <picture>
        <img src="{{ asset('storage/img/tbn-empty.webp') }}" alt="empty" class="max-w-[5rem] mx-auto mb-2">
    </picture>
    <h5 class="font-medium text-lg my-4">{{ $title }}</h5>
    <p class="text-tbn-dark text-sm">{{ $description }}</p>
    {{ $slot }}
</div>
