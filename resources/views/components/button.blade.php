@props(['disabled' => false])
<button
    {{ $attributes->merge([
        'type' => 'submit',
        'disabled' => $disabled,
        'class' => 'text-white bg-tbn-primary border border-transparent py-2 px-8 focus:outline-none
                hover:border-tbn-primary hover:bg-white hover:text-tbn-primary transition-all duration-500 ease-in-out
                rounded text-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-tbn-primary disabled:hover:text-white',
    ]) }}>
    {{ $slot }}
</button>
