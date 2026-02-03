@props(['submit'])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="my-4 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="grid grid-cols-1 gap-2 mb-4 md:gap-4 md:grid-cols-3">
                {{ $form }}
            </div>
            <div class="flex flex-row items-center gap-4">
                @if (isset($actions))
                    {{ $actions }}
                @endif
            </div>
        </form>
    </div>
</div>
