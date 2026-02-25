<div
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-60 dark:bg-opacity-80">
    <div {{ $attributes->merge(['class' => 'px-6 py-5 mx-4 bg-white rounded-lg shadow-lg dark:bg-tbn-dark']) }}
        x-transition>
        {{ $slot }}
    </div>
</div>
