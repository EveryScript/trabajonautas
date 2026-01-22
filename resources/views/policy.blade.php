<x-guest-layout>
    <div
        class="w-full max-w-4xl p-6 mx-auto overflow-hidden text-sm prose bg-gray-50 dark:bg-transparent dark:text-tbn-light">
        <x-application-logo class="mb-2" />
        <h3 class="text-2xl font-semibold text-tbn-dark dark:text-white">Políticas de privacidad</h3>
        <div class="[&_a]:text-tbn-primary [&_strong]:dark:text-white [&_strong]:text-neutral-900">
            {!! $policy !!}
        </div>
    </div>
</x-guest-layout>
