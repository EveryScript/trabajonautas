@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block py-2 px-3 text-white bg-tbn-primary rounded md:bg-transparent md:text-tbn-primary md:p-0 transition duration-150 ease-in-out'
            : 'block py-2 px-3 text-tbn-secondary dark:text-tbn-light rounded dark:hover:text-tbn-primary md:p-0 transition duration-150 ease-in-out';
@endphp

<li><a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a></li>
