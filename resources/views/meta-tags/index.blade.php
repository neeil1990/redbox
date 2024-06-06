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
                background-color: rgba(0, 0, 0, .075);
            }

            [data-toggle=collapse] i.expandable-accordion-caret {
                transition: -webkit-transform .3s linear;
                transition: transform .3s linear;
                transition: transform .3s linear, -webkit-transform .3s linear;
            }

            [data-toggle=collapse][aria-expanded=true] i.expandable-accordion-caret[class*=right] {
                -webkit-transform: rotate(
                    90deg);
                transform: rotate(
                    90deg);
            }

            .MetaTagsProject,
            .MetaTagsPages {
                background: oldlace;
            }
        </style>
    @endslot

    <meta-tags :meta="{{ $meta }}" :lang="{{ $lang }}"></meta-tags>

    <div class="row">
        <div class="col-12">
            @component('component.admin-card')

                @slot('description')

                @endslot

                @component('component.btn-app', ['href' => route('meta-tags.index'), 'class' => 'ml-0'])
                    <i class="fas fa-home"></i> {{ __('Home') }}
                @endcomponent

                @component('component.btn-app', ['href' => route('meta-tags.settings'), 'class' => ''])
                    <i class="fas fa-cog"></i> {{ __('Settings') }}
                @endcomponent

                @component('component.btn-app', ['href' => route('meta-tags.statistic'), 'class' => ''])
                        <i class="fas fa-bullhorn"></i> {{ __('Statistic') }}
                @endcomponent

            @endcomponent
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script src="{{ asset('plugins/datatables/search.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            search(null, false)
        </script>
    @endslot

@endcomponent
