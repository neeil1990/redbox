@component('component.card', ['title' => __('Behavior')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

        <style>
            .behavior {
                background: oldlace;
            }
            .dt-buttons {
                margin: 10px;
            }
        </style>
    @endslot

    <div class="card">
        <div class="card-header border-0">
            <h3 class="card-title">Уникальные фразы проекта <a href="{{ route('behavior.index') }}">{{ $behavior->domain }}</a></h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle">
                <thead>
                <tr>
                    <th>Фраза</th>
                    <th>Количество</th>
                    <th>Выполнено</th>
                    <th>Не выполнено</th>
                </tr>
                </thead>

                <tbody>
                    @foreach($collect as $phrase)
                        <tr>
                            <td>{{ $phrase['phrase'] }}</td>
                            <td>{{ $phrase['count'] }}</td>
                            <td>{{ $phrase['success'] }}</td>
                            <td>{{ $phrase['fail'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap-modal-form-templates/bootstrap-modal-form-templates.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/html5.min.js') }}"></script>

        <script>
            let table = $('.table').DataTable({
                dom: 'tB',
                paging: false,
                ordering: false,
                searching: false,
                info: false,
                buttons: [{
                    extend: 'copy',
                    text: 'Скопировать фразы',
                    exportOptions: {
                        columns: [ 0 ]
                    },
                }]
            } );
        </script>
    @endslot

@endcomponent
