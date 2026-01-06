<x-guest-layout>
    <div class="pt-4 bg-gray-100 dark:bg-[#333333] text-white">
        <div
            class="w-full sm:max-w-2xl mx-auto my-6 p-6 bg-white dark:bg-tbn-dark dark:text-tbn-light text-sm shadow-md overflow-hidden sm:rounded-lg prose">
            <x-application-logo class="mb-2" />
            <h3 class="text-tbn-dark dark:text-white font-semibold text-xl">Políticas de privacidad</h3>
            {!! $policy !!}
        </div>
    </div>
</x-guest-layout>
