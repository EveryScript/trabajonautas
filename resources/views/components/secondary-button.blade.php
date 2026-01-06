<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'text-white bg-tbn-secondary box-border border border-transparent hover:bg-neutral-500 focus:ring-2
            focus:ring-neutral-600 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none
            rounded-lg transition-colors duration-300 ease-in-out dark:hover:bg-neutral-800 dark:focus:ring-neutral-800
            disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-tbn-secondary disabled:hover:text-white'
    ]) }}>
    {{ $slot }}
</button>
