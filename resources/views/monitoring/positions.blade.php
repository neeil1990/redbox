@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    @endslot

    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $data['query']['id'] }}</h3>
                    <p>{{ $data['query']['query'] }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-word"></i>
                </div>
                <a href="{{ route('monitoring.show', $data['query']['monitoring_project_id']) }}" class="small-box-footer">
                    Перейти к запросам <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-1">
        @foreach($data['positions'] as $d)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{$d['header']['region']}} ({{$d['header']['engine']}})</h3>

                    <div class="card-tools"></div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID REGION</th>
                            <th>Дата</th>
                            <th>Позиция</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($d['item'] as $position)
                            <tr>
                                <td>{{$position['id']}}</td>
                                <td>{{$position['engine_id']}}</td>
                                <td>{{$position['created_at']}}</td>
                                <td>{{$position['position']}}</td>
                                <td>
                                    {!! Form::open(['route' => ['keywords.destroy', $position['id']], 'method' => 'DELETE']) !!}
                                        {!! Form::submit('Удалить', ['class' => 'btn btn-block btn-danger btn-xs']) !!}
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
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

            $('[data-toggle="tooltip"]').tooltip();
        </script>
    @endslot


@endcomponent
