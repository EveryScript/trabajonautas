@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-tbn-primary md:p-0 transition duration-150 ease-in-out'
            : 'block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-tbn-primary md:p-0 transition duration-150 ease-in-out';
@endphp

<li><a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a></li>
