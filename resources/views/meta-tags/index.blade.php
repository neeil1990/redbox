@component('component.card', ['title' => __('Meta tags')])

    @slot('css')
        <style>
            a.accordion-title {
                color: #212529;
                font-size: 1rem;
            }

            .card-header-accordion:hover {
                background-color: rgba(0,0,0,.075);
            }

            [data-toggle=collapse] i.expandable-accordion-caret {
                transition: -webkit-transform .3s linear;
                transition: transform .3s linear;
                transition: transform .3s linear,-webkit-transform .3s linear;
            }

            [data-toggle=collapse][aria-expanded=true] i.expandable-accordion-caret[class*=right] {
                -webkit-transform: rotate(
                        90deg);
                transform: rotate(
                        90deg);
            }
        </style>
    @endslot

    <meta-tags :meta="{{ $meta }}"></meta-tags>

    @slot('js')

    @endslot


@endcomponent
