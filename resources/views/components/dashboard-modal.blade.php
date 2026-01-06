@props(['image', 'title', 'description', 'buttons', 'close'])
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
    style="backdrop-filter: blur(5px);">
    <div class="bg-white dark:bg-tbn-dark dark:text-tbn-light rounded-xl shadow-lg p-8 mx-2 max-w-md w-full text-center relative">
        <button class="absolute top-4 right-5" type="button">
            {{ $close }}</button>
        <picture class="block mb-4">
            {{ $image }}
        </picture>
        <h2 class="text-lg font-bold mb-2 text-tbn-primary">
            {{ $title }}
        </h2>
        <p class="mb-6 text-sm">
            {{ $description }}
        </p>
        <!-- Buttons area -->
        <div class="w-full">
            {{ $buttons }}
        </div>
    </div>
</div>
