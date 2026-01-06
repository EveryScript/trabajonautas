{{-- Autentication card --}}
<div class="min-h-screen bg-gray-50 dark:bg-[#333333] text-gray-900 dark:text-tbn-light flex justify-center">
    <div class="max-w-screen-xl m-0 sm:m-10 flex justify-center flex-1 gap-2">
        <div class="min-w-full sm:min-w-[30rem] lg:w-1/2 xl:w-5/12 p-6 sm:p-10 bg-white dark:bg-tbn-dark rounded-lg sm:rounded-tl-lg sm:rounded-bl-lg shadow">
            <div class="mt-6 flex flex-col items-center gap-1">
                {{ $logo }}
                {{ $slot }}
            </div>
        </div>
        <div class="flex-1 text-center hidden lg:flex sm:rounded-lg">
            <div class="w-full bg-cover bg-center bg-no-repeat rounded-lg"
                style="background-image: url('{{ asset('storage/img/tbn-space.webp') }}');">
            </div>
        </div>
    </div>
</div>
