@props(['disabled' => false])
<button
    {{ $attributes->merge([
        'class' =>
            'inline-block text-white bg-tbn-secondary border border-tbn-secondary hover:text-tbn-secondary hover:bg-gray-50 px-4 py-2 border rounded disabled:hover:bg-tbn-secondary disabled:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition',
    ]) }}
    {{ $disabled ? 'disabled' : '' }}>
    {{ $slot }}</button>
