<div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4 mt-1 lg:my-5">
    @for ($i = 0; $i < 8; $i++)
        <div class="rounded-lg p-4 bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary animate-pulse">
            <div class="flex space-x-4 py-6">
                <div class="rounded-full bg-gray-300 dark:bg-tbn-secondary h-12 w-12"></div>
                <div class="flex-1 space-y-4 py-1">
                    <div class="h-4 bg-gray-300 dark:bg-tbn-secondary rounded w-3/4"></div>
                    <div class="space-y-2">
                        <div class="h-4 bg-gray-300 dark:bg-tbn-secondary rounded"></div>
                        <div class="h-4 bg-gray-300 dark:bg-tbn-secondary rounded w-5/6"></div>
                    </div>
                </div>
            </div>
        </div>
    @endfor
</div>
