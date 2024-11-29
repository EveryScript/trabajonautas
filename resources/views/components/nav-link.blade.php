@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-tbn-primary text-sm font-medium leading-5 text-tbn-primary focus:outline-none focus:border-tbn-primary transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-tbn-dark hover:text-black hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-tbn-primary transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
