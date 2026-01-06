@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
            <span>
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <button class="text-white bg-tbn-primary box-border border border-transparent shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none rounded-lg transition-colors duration-300 ease-in-out opacity-50 cursor-not-allowed">
                        {!! __('pagination.previous') !!}
                    </button>
                @else
                    @if(method_exists($paginator,'getCursorName'))
                        <button type="button" dusk="previousPage"
                            wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->previousCursor()->encode() }}"
                            wire:click="setPage('{{$paginator->previousCursor()->encode()}}','{{ $paginator->getCursorName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            class="text-white bg-tbn-primary box-border border border-transparent hover:bg-orange-500 focus:ring-2
                                focus:ring-orange-600 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none
                                rounded-lg transition-colors duration-300 ease-in-out dark:hover:bg-orange-800 dark:focus:ring-orange-800
                                disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-tbn-primary disabled:hover:text-white">
                                {!! __('pagination.previous') !!}
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" 
                            class="text-white bg-tbn-primary box-border border border-transparent hover:bg-orange-500 focus:ring-2
                                focus:ring-orange-600 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none
                                rounded-lg transition-colors duration-300 ease-in-out dark:hover:bg-orange-800 dark:focus:ring-orange-800
                                disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-tbn-primary disabled:hover:text-white">
                                {!! __('pagination.previous') !!}
                        </button>
                    @endif
                @endif
            </span>

            <span>
                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    @if(method_exists($paginator,'getCursorName'))
                        <button type="button"
                            dusk="nextPage"
                            wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->nextCursor()->encode() }}"
                            wire:click="setPage('{{$paginator->nextCursor()->encode()}}','{{ $paginator->getCursorName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                            class="text-white bg-tbn-primary box-border border border-transparent hover:bg-orange-500 focus:ring-2
                                focus:ring-orange-600 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none
                                rounded-lg transition-colors duration-300 ease-in-out dark:hover:bg-orange-800 dark:focus:ring-orange-800
                                disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-tbn-primary disabled:hover:text-white">
                                {!! __('pagination.next') !!}
                        </button>
                    @else
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                            class="text-white bg-tbn-primary box-border border border-transparent hover:bg-orange-500 focus:ring-2
                                focus:ring-orange-600 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none
                                rounded-lg transition-colors duration-300 ease-in-out dark:hover:bg-orange-800 dark:focus:ring-orange-800
                                disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-tbn-primary disabled:hover:text-white">
                                {!! __('pagination.next') !!}
                        </button>
                    @endif
                @else
                    <button class="text-white bg-tbn-primary box-border border border-transparent shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none rounded-lg transition-colors duration-300 ease-in-out opacity-50 cursor-not-allowed">
                        {!! __('pagination.next') !!}
                    </button>
                @endif
            </span>
        </nav>
    @endif
</div>
