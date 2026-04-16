<header class="flex flex-col justify-between gap-4 mb-8 md:flex-row">
    <div class="flex-1">
        <h4 class="text-2xl font-medium text-tbn-primary">
            {{ $title_page }}
        </h4>
        <p class="text-sm text-tbn-dark dark:text-tbn-light">
            {{ $description_page }}
        </p>
    </div>
    @if (isset($search_field))
        {{ $search_field }}
    @endif
</header>
