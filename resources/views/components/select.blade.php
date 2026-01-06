@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'bg-white border border-tbn-light text-gray-900 placeholder-gray-400
        focus:outline-none focus:border-tbn-primary focus:ring-tbn-primary
        dark:bg-tbn-dark dark:border-tbn-secondary dark:text-white dark:focus:ring-tbn-primary
        dark:focus:border-tbn-primary dark:placeholder-gray-500
        px-3 py-2 rounded-lg transition-colors duration-300',
]) !!}>
    {{ $slot }}
</select>
