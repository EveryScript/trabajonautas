@props(['disabled' => false])
<button
    {{ $attributes->merge([
        'type' => 'submit',
        'disabled' => $disabled,
        'class' => 'text-white bg-tbn-primary box-border border border-transparent hover:bg-orange-500 focus:ring-2
            focus:ring-orange-600 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none
            rounded-lg transition-colors duration-300 ease-in-out dark:hover:bg-orange-800 dark:focus:ring-orange-800
            disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-tbn-primary disabled:hover:text-white',
    ]) }}>
    {{ $slot }}
</button>
