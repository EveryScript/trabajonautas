@if ($errors->any())
    <div
        {{ $attributes->merge(['class' => 'bg-red-50 dark:bg-tbn-dark max-w-sm border border-tbn-primary p-4 rounded-md shadow-sm mb-4 my-2 mt-4 min-w-xs']) }}>
        <div class="flex items-start">
            <div class="flex-shrink-0 text-tbn-primary mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path
                        d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold tracking-wide uppercase text-tbn-primary">
                    {{ __('Oops, ha ocurrido un error') }}
                </h3>
                <ul class="mt-2 text-sm text-tbn-primary space-y-1.5 font-medium">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor"
                                class="text-tbn-primary" viewBox="0 0 16 16">
                                <circle cx="8" cy="8" r="4" />
                            </svg>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
