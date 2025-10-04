@props(['disabled' => false])

<div x-data="{ showPassword: false }" class="relative">
    <input :type="showPassword ? 'text' : 'password'" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
        'class' =>
            'w-full bg-white rounded border border-gray-300 focus:border-tbn-primary focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
    ]) !!}>
    <button type="button" @click="showPassword = !showPassword"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none" style="top: 0.2rem;">
        <i :class="showPassword ? 'text-sm fas fa-eye' : 'text-sm fas fa-eye-slash'"></i>
    </button>
</div>
