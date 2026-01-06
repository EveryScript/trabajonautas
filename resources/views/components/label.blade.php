@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-tbn-dark dark:text-tbn-light']) }}>
    {{ $value ?? $slot }}
</label>
