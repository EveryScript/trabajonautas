@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-tbn-light']) }}>
    {{ $value ?? $slot }}
</label>
