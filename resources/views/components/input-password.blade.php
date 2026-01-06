@props(['disabled' => false])

<div x-data="{ showPassword: false }" class="relative">
    <input :type="showPassword ? 'text' : 'password'" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
        'class' => 'bg-white border border-tbn-light text-gray-900 placeholder-gray-400
        focus:outline-none focus:border-tbn-primary focus:ring-tbn-primary
        dark:bg-tbn-dark dark:border-tbn-secondary dark:text-white dark:focus:ring-tbn-primary
        dark:focus:border-tbn-primary dark:placeholder-gray-500
        px-3 py-2 rounded-lg transition-colors duration-300',
    ]) !!}>
    <button type="button" @click="showPassword = !showPassword"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none" style="top: 0.2rem;">
        <i :class="showPassword ? 'text-sm fas fa-eye' : 'text-sm fas fa-eye-slash'"></i>
    </button>
</div>
