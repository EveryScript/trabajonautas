@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-tbn-dark']) }}>
    {{ $value ?? $slot }}
</label>
