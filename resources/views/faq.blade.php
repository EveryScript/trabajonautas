<x-guest-layout>
    <div
        class="w-full max-w-4xl p-6 mx-auto overflow-hidden text-sm prose bg-gray-50 dark:bg-transparent dark:text-tbn-light">
        <x-application-logo class="mb-2" />
        <h3 class="text-2xl font-semibold text-tbn-dark dark:text-white">Preguntas más frecuentes</h3>
        <div
            class="[&_h1]:dark:text-white [&_h2]:dark:text-white [&_h3]:dark:text-tbn-light [&_h4]:dark:text-tbn-light [&_h4]:text-lg [&_h4]:mb-2 [&_a]:text-tbn-primary [&_strong]:dark:text-white [&_strong]:text-neutral-900 pb-20">
            {!! $faq !!}
        </div>
    </div>
</x-guest-layout>
