{{-- Autentication card --}}
<div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
    <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
        <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
            <div class="mt-12 flex flex-col items-center">
                {{ $logo }}
                <br>
                {{ $slot }}
            </div>
        </div>
        <div class="flex-1 bg-indigo-100 text-center hidden lg:flex">
            <div class="w-full bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('storage/img/tbn-landing.webp') }}');">
            </div>
        </div>
    </div>
</div>
