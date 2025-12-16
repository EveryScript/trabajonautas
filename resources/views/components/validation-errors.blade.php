@if ($errors->any())
    <div {{ $attributes }}>
        <div class="font-medium text-tbn-primary">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-2 list-inside text-sm text-tbn-primary list-none">
            @foreach ($errors->all() as $error)
                <li><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
