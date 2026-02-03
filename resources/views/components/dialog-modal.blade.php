@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4">
        <div class="text-lg font-medium text-tbn-primary">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm text-tbn-secondary dark:text-tbn-light">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 text-end">
        {{ $footer }}
    </div>
</x-modal>
