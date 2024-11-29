<div class="flex flex-row justify-between mb-4">
    <div class="flex-grow">
        <h4 class="text-2xl text-tbn-primary font-medium">
            {{ $title_page }}
        </h4>
        <p class="text-tbn-dark text-sm">
            {{ $description_page }}
        </p>
    </div>
    @if (isset($search_field))
        <div class="flex flex-row gap-2 min-w-[30rem] mt-2 justify-end">
            {{ $search_field }}
        </div>
    @endif
</div>
