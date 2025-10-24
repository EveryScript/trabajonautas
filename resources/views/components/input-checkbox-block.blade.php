@props([
    'checked' => false,
    'disabled' => false,
    'color' => null,
])

<div class="px-4 py-3 border bg-white rounded-lg mb-4 border-{{ $color ? $color : 'tbn-primary' }}">
    <label class="inline-flex items-center">
        <input type="checkbox" {{ $disabled ? 'disabled' : '' }} {{ $checked ? 'checked' : '' }} {!! $attributes->merge([
            'class' => 'sr-only peer',
        ]) !!}>
        <div
            class="relative w-12 h-7 {{ $disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }} bg-gray-400 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tbn-primary">
        </div>
        {{ $slot }}
    </label>
</div>
