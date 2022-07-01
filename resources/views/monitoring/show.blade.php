@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

        <style>
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
            .dataTables_filter {
                display: none;
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

                <table class="table table-responsive table-bordered table-hover text-center">
                    <thead>
                        <tr>
                            @foreach($headers as $header)
                                <th>{!! $header !!}</th>
                            @endforeach
                        </tr>
                    </thead>

                </table>

            </div>
            <!-- /.card -->
        </div>
    </div>

    <h5 class="mb-2 mt-4">Testing</h5>

    <div class="row">
        {{--@foreach($table as $key => $rows)
            @if($key)
                <div class="col-2">
                    {!! Form::open(['route' => ['keywords.update', $rows[0]], 'method' => 'PATCH']) !!}
                    {!! Form::submit('Обновить id: ' . $rows[0], ['class' => 'btn btn-block btn-success btn-xs']) !!}
                    {!! Form::close() !!}
                </div>
            @endif
        @endforeach--}}
    </div>

    @include('monitoring.keywords.modal.edit')

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

        <script>

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            let table = $('.table').DataTable({
                dom: '<"card-header"<"card-title"><"float-right"f><"float-right"l>><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                "ordering": false,
                scrollX: true,
                lengthMenu: [5, 20, 30, 50, 100],
                pageLength: 5,
                pagingType: "simple_numbers",
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    paginate: {
                        "first":      "«",
                        "last":       "»",
                        "next":       "»",
                        "previous":   "«"
                    },
                },
                processing: false,
                serverSide: true,
                ajax: {
                    url: '/monitoring/{{ $project->id }}/table',
                },
                //rowReorder: true,
                columnDefs: [
                    //{ orderable: true, className: 'reorder', targets: 3 },
                    //{ orderable: false, targets: '_all' },
                    { "width": "350px", "targets": 3 }
                ],
                initComplete: function(){
                    let api = this.api();

                    $('.search-button').click(function () {
                        let a = $(this);
                        let span = a.parent();
                        let b = span.find('b');
                        let input = span.find('input');

                        let toggleClass = 'd-none';

                        a.addClass(toggleClass);
                        b.addClass(toggleClass);

                        input.unbind( "blur" );

                        input.removeClass(toggleClass).focus().blur(function () {
                            $(this).addClass(toggleClass);
                            a.removeClass(toggleClass);
                            b.removeClass(toggleClass);
                        });
                    });

                    api.columns().every(function() {
                        let that = this;

                        $('input', this.header()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });

                    $('#selected-checkbox').change(function () {
                        $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
                    });

                    this.closest('.card').find('.card-header .card-title').html("[{{$region->lr}}] {{ ucfirst($region->engine) }}, {{ $region->location->name }}");
                    this.closest('.card').find('.card-header label').css('margin-bottom', 0);
                },
                drawCallback: function(){

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

                    $('.pagination').addClass('pagination-sm');

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
                },
            });

            $('#edit-modal').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);

                let id = button.data('id');

                let modal = $(this);

                axios.get(`/monitoring/keywords/${id}/edit`).then(function (response) {

                    let content = response.data;

                    modal.find('.modal-body').html(content);
                });
            });

            $('#edit-modal').find('.save-modal').click(function () {
                let form = $(this).closest('.modal-content').find('form');
                let action = form.attr('action');
                let data = {};

                $.each(form.serializeArray(), function (inc, item) {
                    $.extend( data, {[item.name]: item.value} );
                });

                axios.patch(action, data)
                    .then(function (response) {
                        table.draw(false);

                        $('#edit-modal').modal('hide');
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });

            $('.table').on('click', '.delete-keyword' ,function () {
                let item = $(this);
                let id = item.data('id');

                if (window.confirm("Do you really want to delete?")) {

                    axios.delete(`/monitoring/keywords/${id}`);

                    item.closest('tr').remove();
                }
            });
        </script>
    @endslot


@endcomponent
