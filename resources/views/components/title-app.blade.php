<header class="flex flex-col gap-4 md:flex-row justify-between mb-8">
    <div class="flex-1">
        <h4 class="text-2xl text-tbn-primary font-medium">
            {{ $title_page }}
        </h4>
        <p class="text-tbn-dark dark:text-tbn-light text-sm">
            {{ $description_page }}
        </p>
    </div>
    @if (isset($search_field))
        {{ $search_field }}
    @endif
</header>
