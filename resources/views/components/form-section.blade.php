@props(['submit'])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="my-6 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="grid grid-cols-1 gap-2 mb-4 md:gap-4 md:grid-cols-2">
                {{ $form }}
            </div>

            @if (isset($actions))
                {{ $actions }}
            @endif
        </form>
    </div>
</div>
