@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

        <style>
            .table tr:first-child td {
                font-weight: bold;
            }

            .table tr td {
                width: 80px;
            }
            .table tr td:nth-child(1) {
                width: 40px;
                text-align: center;
            }
            .table tr td:nth-child(2) {
                width: 40px;
            }
            .table tr td:nth-child(3) {
                width: 100px;
            }
            .table tr td:nth-child(4) {
                width: 230px;
            }
            .table tr.body td:nth-child(4) {
                font-size: 12px;
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
                    <table class="table table-sm table-bordered table-hover" style="width: auto">
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
                    {!! Form::open(['route' => ['keywords.update', $rows[1]], 'method' => 'PATCH']) !!}
                    {!! Form::submit('Обновить id: ' . $rows[1], ['class' => 'btn btn-block btn-success btn-xs']) !!}
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

            $('[data-toggle="tooltip"]').tooltip();
        </script>
    @endslot


@endcomponent
