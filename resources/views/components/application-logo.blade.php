<a {{ $attributes->merge(['class' => 'flex title-font font-medium items-center mb-0 text-xl']) }}
    href="{{ route('welcome') }}" wire:navigate>
    <img class="w-5 h-5" src="{{ asset('storage/img/tbn-icon.ico') }}" alt="icon">
    <span class="ml-2">Trabajonautas</span>
</a>
