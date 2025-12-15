@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'w-full bg-white rounded border border-gray-300 focus:border-tbn-primary focus:ring-2 focus:ring-orange-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out cursor-pointer disabled:cursor-not-allowed',
]) !!}>
    {{ $slot }}
</select>
