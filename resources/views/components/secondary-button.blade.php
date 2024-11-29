<button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-gray-200 border-0 py-2 px-8 focus:outline-none rounded text-md transition-colors']) }}>
    {{ $slot }}
</button>
