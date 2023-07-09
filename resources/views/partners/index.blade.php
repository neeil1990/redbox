@component('component.card', ['title' => __('Партнёры')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
        <style>
            .card-header::after {
                display: none;
            }

            .fa {
                cursor: pointer;
                opacity: .5;
            }

            .fa:hover {
                opacity: 1;
            }

            .card-img-top {
                height: 180px;
                width: 100%;
                display: block;
                object-fit: contain;
            }
        </style>
    @endslot
    <div class="card-body">
        @if($admin)
            <div class="mb-3">
                <a href="{{ route('partners.add.group') }}" class="btn btn-outline-secondary">{{ __('Add group') }}</a>
                <a href="{{ route('partners.add.item') }}" class="btn btn-outline-secondary">{{ __('Add partner') }}</a>
                <a href="{{ route('partners.admin') }}"
                   class="btn btn-outline-secondary">{{ __('Partners (admins)') }}</a>
                <a href="{{ route('partners') }}" class="btn btn-outline-secondary">{{ __('Partners (users)') }}</a>
            </div>
        @endif
        @foreach($groups as $elem)
            <div class="row d-flex justify-content-center mb-5">
                <h2 class="text-muted w-100 text-center">{{ $elem['name_'. $lang] }}</h2>
                @foreach($elem['items'] as $item)
                    <div class="card @if(count($elem['items']) > 1) mr-3 @endif" style="width: 20rem;">
                        <img class="card-img-top" src="storage/{{ $item['image'] }}" alt="image">
                        <div class="card-footer d-flex flex-column h-100">
                            <div class="h-100 d-flex flex-column">
                                <div class="d-flex flex-column justify-content-start">
                                    <h3 class="card-title">{{ $item['name_'. $lang] }}</h3>
                                    <p class="card-text">{{ $item['description_'. $lang] }}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end click_tracking">
                                <a href="/partners/r/{{ $item['short_link_' . $lang] }}"
                                   class="btn btn-secondary click_tracking"
                                   target="_blank" data-click="{{ $item['name_'. $lang] }}"> >>> </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mt-5">
        {{ __('If you know a good service and are ready to share it, then write to ') }}
        <a href="mailto:info@redbox.su">info@redbox.su</a>
    </div>
@endcomponent
