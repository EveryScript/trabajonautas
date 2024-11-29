@props(['disabled' => false])
<button
    {{ $attributes->merge([
        'type' => 'submit',
        'disabled' => $disabled,
        'class' => 'text-white bg-tbn-primary border-0 py-2 px-8 focus:outline-none
                hover:bg-tbn-dark rounded text-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed
                disabled:hover:bg-tbn-primary',
    ]) }}>
    {{ $slot }}
</button>
