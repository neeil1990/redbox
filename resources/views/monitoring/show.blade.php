@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

        <style>
            .table tr:first-child td {
                font-weight: bold;
            }
        </style>
    @endslot

    <h5 class="mb-2 mt-4">Navigations</h5>

    <div class="row">
        @foreach($navigations as $navigation)
        <div class="col-lg-2 col-6">
            <div class="small-box {{ $navigation['bg'] }}">
                <div class="inner">
                    <h3>{{ $navigation['h3'] }}</h3>
                    <p>{{ $navigation['p'] }}</p>
                </div>
                <div class="icon">
                    <i class="{{ $navigation['icon'] }}"></i>
                </div>
                <a href="{{ $navigation['href'] }}" class="small-box-footer">
                    {{ $navigation['a'] }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expandable Table</h3>
                </div>
                <!-- ./card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <tbody>
                            @foreach($table as $rows)
                                <tr>
                                    @foreach($rows as $col)
                                    <td>{{$col}}</td>
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
