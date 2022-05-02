@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    @endslot

    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $project->name }}</h3>

                    <p>{{ $project->url }}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="{{ route('monitoring.index') }}" class="small-box-footer">
                    Перейти к проектам <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-1">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ваши запросы</h3>

                    <div class="card-tools"></div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Запрос</th>
                                <th>Страница</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($project->keywords as $keyword)
                            <tr>
                                <td><b>{{$keyword->id}}</b></td>
                                <td><a href="{{ route('keywords.show', $keyword->id) }}">{{ $keyword->query }}</a></td>
                                <td>{{ $keyword->page }}</td>
                                <td>
                                    {!! Form::open(['route' => ['keywords.update', $keyword->id], 'method' => 'PATCH']) !!}
                                        {!! Form::submit('Обновить', ['class' => 'btn btn-block btn-success btn-xs']) !!}
                                    {!! Form::close() !!}
                                </td>
                                <td>
                                    <button type="button" data-id="{{$keyword->id}}" class="btn btn-block btn-info btn-xs adding-queue">Добавить в очередь</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
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
