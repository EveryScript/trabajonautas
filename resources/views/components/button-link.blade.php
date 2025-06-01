<a
    {{ $attributes->merge(['class' => 'text-white py-2 px-8 focus:outline-none rounded text-md
        border border-transparent hover:border-tbn-primary hover:bg-white hover:text-tbn-primary transition-all
        duration-500 ease-in-out']) }}>
    {{ $slot }}
</a>
