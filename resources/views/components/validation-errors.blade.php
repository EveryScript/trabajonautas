@if ($errors->any())
    <div {{ $attributes }}>
        <div class="font-medium text-tbn-primary">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-2 text-sm list-none list-inside text-tbn-primary">
            @foreach ($errors->all() as $error)
                <li> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="inline-block" viewBox="0 0 16 16">
                        <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                    </svg> {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif
