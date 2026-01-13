<x-guest-layout>
    <div class="py-6 bg-gray-100 dark:bg-[#333333] text-white">
        <div
            class="w-full p-6 mx-auto overflow-hidden text-sm prose bg-white shadow-md sm:max-w-4xl dark:bg-tbn-dark dark:text-tbn-light sm:rounded-lg">
            <x-application-logo class="mb-2" />
            <h3 class="text-xl font-semibold text-tbn-dark dark:text-white">Términos de servicio</h3>
            <div class="[&_a]:text-tbn-primary [&_strong]:dark:text-white [&_strong]:text-neutral-900">
                {!! $terms !!}
            </div>
        </div>
    </div>
</x-guest-layout>
