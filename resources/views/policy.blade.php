<x-guest-layout>
    <div class="py-6 bg-gray-100 dark:bg-[#333333] text-white">
        <div
            class="w-full sm:max-w-4xl mx-auto p-6 bg-white dark:bg-tbn-dark dark:text-tbn-light text-sm shadow-md overflow-hidden sm:rounded-lg prose">
            <x-application-logo class="mb-2" />
            <h3 class="text-tbn-dark dark:text-white font-semibold text-xl">Políticas de privacidad</h3>
            <div class="[&_a]:text-tbn-primary [&_strong]:dark:text-white [&_strong]:text-neutral-900">
                {!! $policy !!}
            </div>
        </div>
    </div>
</x-guest-layout>
