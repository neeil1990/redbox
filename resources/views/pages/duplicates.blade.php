@component('component.card', ['title' => __('Remove Duplicates')])

    @slot('css')
        <style>
            .Duplicates {
                background: oldlace;
            }
        </style>
    @endslot

    <remove-duplicates :names="{{ $options }}"
                       start="{{ __('remove characters at the beginning of a word') }}: +-!"
                       end="{{ __('remove characters at the end of a word') }}: .!?"
                       submit="{{ __('Remove duplicates') }}"
    ></remove-duplicates>

@endcomponent
