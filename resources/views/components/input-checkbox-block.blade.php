@props([
    'checked' => false,
    'disabled' => false,
])

<div
    class="px-5 py-4 mb-4 transition-all duration-200 bg-white border border-gray-200 shadow-sm cursor-pointer dark:bg-tbn-dark dark:border-tbn-secondary/60 hover:border-tbn-primary/70 dark:hover:border-tbn-primary/70 rounded-xl hover:shadow-md">
    <label class="flex flex-row-reverse items-center justify-between w-full gap-4 cursor-pointer">
        <input type="checkbox" {{ $disabled ? 'disabled' : '' }} {{ $checked ? 'checked' : '' }} {!! $attributes->merge(['class' => 'sr-only peer']) !!}>
        <div
            class="relative min-w-12 w-12 h-7 {{ $disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }} bg-gray-200 dark:bg-tbn-secondary peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tbn-primary shrink-0">
        </div>
        <span class="text-sm font-medium text-gray-800 select-none dark:text-gray-200">
            {{ $slot }}
        </span>
    </label>
</div>
