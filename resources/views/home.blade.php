@component('component.card', ['title' => __('Main page')])
@section('content')
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/home/css/style.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h4 class="pl-2">{{ __('On this page there are our services that you can use.') }}</h4>
    <p class="text-muted pl-2"> {{ __('You can drag and drop services, thereby setting up a convenient order for you') }}</p>
    <div id="tablecontents" class="row d-flex justify-content-center pt-3">
        @foreach($result as $item)
            <div class="card p-0 mr-3 ml-3" data-id="{{ $item['id'] }}">
                <div class="card-header d-flex w-100">
                    <h5 class="card-title w-75">{{ __($item['title']) }}</h5>
                    <span class="handle ui-sortable-handle w-25 text-right">
                      <i class="fas fa-ellipsis-v"></i>
                      <i class="fas fa-ellipsis-v"></i>
                    </span>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ __($item['description']) }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ $item['link'] }}" class="btn btn-secondary" target="_blank"> >>> </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@endcomponent
