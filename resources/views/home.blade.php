@component('component.card', ['title' => __('Main page')])
@section('content')
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/home/css/style.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <body>
    <div id="tablecontents" class="row p-0 m-0">
        @foreach($posts as $post)
            <div class="card col-4 p-0" data-id="{{ $post->id }}">
                <div class="card-header">
                    <h5 class="card-title">{{ $post->title }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $post->description }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ $post->link }}" class="btn btn-primary">link to service</a>
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
                    var order = [];
                    var token = $('meta[name="csrf-token"]').attr('content');
                    $('div.card').each(function (index) {
                        order.push({
                            id: $(this).attr('data-id'),
                            position: index + 1
                        });
                    });

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ url('project-sortable') }}",
                        data: {
                            order: order,
                            _token: token
                        },
                    });
                }
            });
        </script>
    @endslot
@endsection
@endcomponent
