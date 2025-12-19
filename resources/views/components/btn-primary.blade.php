@props(['disabled' => false])
<button
    {{ $attributes->merge([
        'class' =>
            'inline-block text-white bg-tbn-primary border border-tbn-primary hover:text-tbn-primary hover:bg-gray-50 px-4 py-2 border text-white rounded disabled:hover:bg-tbn-primary disabled:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed',
    ]) }}
    {{ $disabled ? 'disabled' : '' }}>
    {{ $slot }}</button>
