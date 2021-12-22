@component('component.card', ['title' => __('Meta tags')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

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

    <meta-tags :meta="{{ $meta }}" :lang="{{ $lang }}"></meta-tags>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };
        </script>
    @endslot


@endcomponent
