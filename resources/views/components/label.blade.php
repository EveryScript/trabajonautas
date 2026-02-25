@props(['value'])

<label {{ $attributes->merge(['class' => 'block mb-1 font-medium text-sm text-tbn-dark dark:text-tbn-light']) }}>
    {{ $value ?? $slot }}
</label>
