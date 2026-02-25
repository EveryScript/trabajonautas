<div wire:ignore x-data="{ pond: null }" x-init="pond = FilePond.create($refs.input, {
    allowMultiple: {{ $attributes->has('multiple') ? 'true' : 'false' }},
    server: {
        process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
            @this.upload('{{ $attributes['wire:model'] }}', file, load, error, progress);
        },
        revert: (filename, load) => {
            @this.removeUpload('{{ $attributes['wire:model'] }}', filename, load);
        },
    },
    labelIdle: 'Arrastra archivos o <span class=filepond--label-action>Explora</span>',
});" class="mt-1">
    <input type="file" x-ref="input" {{ $attributes->has('multiple') ? 'multiple' : '' }}>
</div>
