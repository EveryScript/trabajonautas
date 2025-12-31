<div class="max-w-7xl mx-auto py-10">
    <div x-data="{
        init() {
            new Splide(this.$refs.splide, {
                type: 'loop',
                perPage: 3,
                gap: '1.5rem',
                breakpoints: {
                    1024: { perPage: 2 },
                    768: { perPage: 1 }
                }
            }).mount();
        }
    }" class="splide" x-ref="splide">

        <div class="splide__track">
            <ul class="splide__list">
                @foreach ($notices as $notice)
                    <li class="splide__slide" wire:key='{{ $notice->id }}'>
                        <x-notice-card title="{{ $notice->title }}" subtitle="{{ $notice->description }}"
                            image="{{ $notice->image }}" link="{{ $notice->link }}" />
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
