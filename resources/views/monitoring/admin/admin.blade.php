@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    @endslot

    <div class="row">
        <div class="col-6">
            @include('monitoring.admin._btn')

            {!! Form::open(['route' => ['monitoring.admin.settings.update']]) !!}
                @include('monitoring.admin.settings.global', ['settings' => $settings['global']])
            {!! Form::close() !!}
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <!-- InputMask -->
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.bundle.js') }}"></script>
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            $('.time').inputmask("hh:mm", {
                placeholder: moment().format('H:mm'),
            });

        </script>
    @endslot


@endcomponent
