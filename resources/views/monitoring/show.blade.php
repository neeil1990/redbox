@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    @endslot

    <div class="row mb-1">

        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Запрос</th>
                                <th>Страница</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($project->keywords as $keyword)
                            <tr>
                                <td>{{ $keyword->query }}</td>
                                <td>{{ $keyword->page }}</td>
                                <td>btn</td>
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

            $('[data-toggle="tooltip"]').tooltip();
        </script>
    @endslot


@endcomponent
