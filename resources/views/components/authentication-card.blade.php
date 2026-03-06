{{-- Autentication card --}}
<div class="min-h-screen bg-gray-50 dark:bg-[#333333] text-gray-900 dark:text-tbn-light flex justify-center">
    <div class="flex justify-center flex-1 max-w-screen-xl gap-2 m-0 sm:m-10">
        <div class="min-w-full sm:min-w-[30rem] lg:w-1/2 xl:w-5/12 p-6 sm:p-10 bg-white dark:bg-tbn-dark rounded-lg sm:rounded-tl-lg sm:rounded-bl-lg shadow">
            <div class="flex flex-col items-center gap-1 mt-6">
                {{ $logo }}
                {{ $slot }}
            </div>
        </div>
        <div class="flex-1 hidden text-center lg:flex sm:rounded-lg">
            <div class="w-full bg-center bg-no-repeat bg-cover rounded-lg"
                style="background-image: url('{{ asset('storage/ajustes/astro-control.webp') }}');">
            </div>
        </div>
    </div>
</div>
