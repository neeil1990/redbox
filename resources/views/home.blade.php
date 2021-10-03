@component('component.card', ['title' => __('Main page')])
@section('content')
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/home/css/style.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <body>
    <h4 class="pl-2">{{ __('On this page there are our services that you can use.') }}</h4>
    <p class="text-muted pl-2"> {{ __('You can drag and drop services, thereby setting up a convenient order for you') }}</p>
    <div id="tablecontents" class="row p-0 pl-2">
        @foreach($result as $item)
            <div class="card p-0 mr-2 ml-2" data-id="{{ $item['id'] }}">
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
    @slot('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script>
            $(function () {
                $("#tablecontents").sortable({
                    items: 'div.card',
                    cursor: 'move',
                    opacity: 0.6,
                    update: function () {
                        sendOrderToServer();
                    }
                });

                function sendOrderToServer() {
                    var orders = [];
                    var token = $('meta[name="csrf-token"]').attr('content');
                    $('div.card').each(function (index) {
                        orders.push({
                            id: $(this).attr('data-id'),
                        });
                    });

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ url('project-sortable') }}",
                        data: {
                            orders: orders,
                            _token: token
                        },
                    });
                }
            });
        </script>
    @endslot
@endsection
@endcomponent
