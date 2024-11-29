@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'w-full bg-white rounded border border-gray-300 focus:border-tbn-primary focus:ring-2 focus:ring-indigo-200 outline-none text-gray-700 py-1 px-3 leading-1 transition-colors duration-200 ease-in-out',
]) !!}>{{ $slot }}</textarea>
