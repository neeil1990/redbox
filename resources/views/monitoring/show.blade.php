@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

        <style>
            .table tr:first-child td {
                font-weight: bold;
            }

            .table tr td:nth-child(4) {
               text-align: left;
            }

            .table tr td:nth-child(4) {
                position: sticky;
                left: 0;
                background-color: #FFF;
                box-shadow: inset 0 0 0 9999px rgba(0, 0, 0, 0.019);
                z-index: 1;
            }
            .table tr:first-child td:nth-child(4) {
                box-shadow: none;
            }
        </style>
    @endslot

    <div class="row">
        @foreach($navigations as $navigation)
        <div class="col-lg-2 col-6">
            <a href="{{ $navigation['href'] }}" class="small-box {{ $navigation['bg'] }}">
                <div class="inner">
                    <h3>{{ $navigation['h3'] }}</h3>
                    <p>{{ $navigation['p'] }}</p>
                </div>
                <div class="icon">
                    <i class="{{ $navigation['icon'] }}"></i>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">[{{$region->lr}}] {{ ucfirst($region->engine) }}, {{ $region->location->name }}</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <!-- ./card-header -->
                <div class="card-body">
                    <table class="table table-responsive table-bordered table-hover text-center">
                        <tbody>
                            @foreach($table as $i => $rows)
                                <tr class="{{($i) ? 'body' : 'head'}}">
                                    @foreach($rows as $col)
                                    <td>{!! $col !!}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

    <h5 class="mb-2 mt-4">Testing</h5>

    <div class="row">
        @foreach($table as $key => $rows)
            @if($key)
                <div class="col-2">
                    {!! Form::open(['route' => ['keywords.update', $rows[0]], 'method' => 'PATCH']) !!}
                    {!! Form::submit('Обновить id: ' . $rows[0], ['class' => 'btn btn-block btn-success btn-xs']) !!}
                    {!! Form::close() !!}
                </div>
            @endif
        @endforeach
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script>

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            $('#selected-checkbox').change(function () {

                $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
            });

            $('.adding-queue').click(function () {
                let id = $(this).data('id');

                axios.post('/monitoring/keywords/queue', {
                    id: id,
                })
                    .then(function (response) {
                        if(response.data.id)
                            toastr.info('Выполните необходимые команды для запуска очереди.', 'Задание добавленно в очередь', {timeOut: 20000, closeButton: true});
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            });

            $('.table tr').each(function (i, item) {
                let target = $(item).find('.target').text();
                let positions = $(item).find('td span[data-position]');

                $.each(positions, function (i, item) {
                    let current = $(item).data('position');
                    let nextTo = $(positions[i + 1]).data('position');

                    let total = nextTo - current;

                    if(total){

                        if(total > 0)
                            total = '+' + total;

                        $(item).find('sup').text(total);
                    }

                    if(target >= current)
                        $(item).closest('td').css('background-color', '#99e4b9');
                    else{
                        if(target >= nextTo)
                            $(item).closest('td').css('background-color', '#fbe1df');
                    }
                });


            });

            $('[data-toggle="tooltip"]').tooltip({
                placement: 'right',
            });

            $('[data-toggle="popover"]').popover({
                trigger: 'manual',
                placement: 'right',
                html: true,
            }).on("mouseenter", function() {
                $(this).popover("show");
            }).on("mouseleave", function() {
                let self = this;

                let timeout = setTimeout(function(){
                    $(self).popover("hide");
                }, 300);

                $('.popover').hover(function () {
                    clearTimeout(timeout);
                }, function () {
                    $(self).popover("hide");
                });
            });
        </script>
    @endslot


@endcomponent
