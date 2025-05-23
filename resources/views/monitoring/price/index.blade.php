@component('component.card', ['title' => __('Monitoring position groups')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-select/css/select.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-editor/css/editor.bootstrap4.min.css') }}">

        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

        <style>
            .DTE_Field div.multi-value,
            .DTE_Field div.multi-restore {
                border: 1px dotted #666;
                border-radius: 3px;
            }

            #select-region {
                margin-right: 0.5em;
                display: inline-block;
                width: auto;
            }
            .card-header label {
                margin-bottom: 0px!important;
            }
            .custom-select-sm {
                font-size: 86%!important;
            }
        </style>
    @endslot

    <div class="row">
        <div class="col-2 mb-3">
            <a href="{{ route('monitoring.show', request('id')) }}" class="btn btn-block btn-default">Вернутся в проект</a>
        </div>
        <div class="col-4">
            @can('update_budget_monitoring')
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                          {{ __('Budget') }}
                        </span>
                    </div>
                    <input type="number" min="0" step="0.01" placeholder="{{ __('Projects budget') }}" value="{{ $project['budget'] }}" class="form-control">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success budget">{{ __('Save') }}</button>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-12 card-table">
            <div class="card">
                <table class="table table-hover" id="prices"></table>
            </div>
            <!-- /.card -->
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
        <script src="{{ asset('plugins/datatables-select/js/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-editor/js/datatables_editor.min.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <!-- InputMask -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            let editor = new $.fn.dataTable.Editor( {
                ajax: {
                    url: "{{ route('prices.action', request('id')) }}",
                    data: function(data){
                        data.region = $('#select-region').val();
                    },
                },
                table: "#prices",
                fields: [
                    {
                        label: "TOP 1:",
                        name: "top1",
                    },
                    {
                        label: "TOP 3:",
                        name: "top3",
                    },
                    {
                        label: "TOP 5:",
                        name: "top5",
                    },
                    {
                        label: "TOP 10:",
                        name: "top10",
                    },
                    {
                        label: "TOP 20:",
                        name: "top20",
                    },
                    {
                        label: "TOP 50:",
                        name: "top50",
                    },
                    {
                        label: "TOP 100:",
                        name: "top100",
                    },

                ],
                i18n: {
                    "multi": {
                        "title": "Несколько значений",
                        "info": "Выбранные элементы содержат разные значения для этого входа. Чтобы отредактировать и установить для всех элементов этого ввода одинаковое значение, нажмите здесь, в противном случае они сохранят свои индивидуальные значения.",
                        "restore": "Отменить изменения",
                        "noMulti": "Этот вход можно редактировать индивидуально, но не как часть группы."
                    },
                }
            } );

            let editIcon = function ( data, type, row ) {
                if ( type === 'display' && '{{ auth()->user()->can('update_price_monitoring') }}') {
                    return data + ' <i class="fa fa-pencil" style="opacity: 0.5;font-size: 12px;cursor: pointer;"/>';
                }

                return data;
            };

            $('#prices').on( 'click', 'td i', function (e) {
                e.stopImmediatePropagation();

                editor.inline( $(this).parent(), {
                    onBlur: 'submit',
                } );
            } );

            editor.on('initEdit', function(event, row, data, el, type) {
                if (type === 'inline') {
                    setTimeout(() => el.find('input').select(), 50);
                }
            });

            $('#prices').DataTable( {
                dom: '<"card-header"<"card-title"B><"float-right"f><"float-right"l>><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                autoWidth: false,
                ordering: false,
                paging: true,
                lengthMenu: [10, 30, 50, 100, 500, 1000],
                pageLength: 500,
                pagingType: "simple_numbers",
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Найти запрос",
                    paginate: {
                        "first":      "«",
                        "last":       "»",
                        "next":       "»",
                        "previous":   "«"
                    },
                    processing: '<img src="/img/1485.gif" style="width: 50px; height: 50px;">',
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('prices.index', request('id')) }}",
                    type: 'GET',
                    data: function(data){
                        data.region = $('#select-region').val();
                    },
                },
                columns: [
                    {
                        title: 'Query',
                        data: "query"
                    },
                    {
                        title: 'TOP 1',
                        data: "top1",
                        render: editIcon,
                    },
                    {
                        title: 'TOP 3',
                        data: "top3",
                        render: editIcon,
                    },
                    {
                        title: 'TOP 5',
                        data: "top5",
                        render: editIcon,
                    },
                    {
                        title: 'TOP 10',
                        data: "top10",
                        render: editIcon,
                    },
                    {
                        title: 'TOP 20',
                        data: "top20",
                        render: editIcon,
                    },
                    {
                        title: 'TOP 50',
                        data: "top50",
                        render: editIcon,
                    },
                    {
                        title: 'TOP 100',
                        data: "top100",
                        render: editIcon,
                    },
                ],
                select: {
                    style: 'multi'
                },
                buttons: [
                    {
                        text: "Выбрать всё",
                        className: "btn-default btn-sm",
                        extend: "selectAll",
                    },
                    {
                        text: "Отменить выбранные",
                        className: "btn-default btn-sm",
                        extend: "selectNone",
                    },
                    {
                        text: "Редактировать выбранные",
                        className: "btn-default btn-sm",
                        extend: "edit",
                        editor: editor,
                        available: function () {
                            return {{ auth()->user()->can('update_price_monitoring') }}
                        }
                    },
                ],
                initComplete: function(){
                    let api = this.api();
                    let json = api.ajax.json();
                    let card = this.closest('.card');

                    let container = $('<div />').addClass('float-right');

                    let regions = $('<select />', { id: 'select-region'}).addClass('custom-select custom-select-sm');

                    $.each(json.regions, function (i, val) {
                        let option = $('<option />').val(val.id).text(val.name);
                        regions.append(option);
                    });

                    regions.change(() => api.ajax.reload());

                    card.find('.card-header').append(container.html(regions));
                },
            });

            $('.budget').click(function(){
                let budget = $(this).closest('.input-group').find('input').val();
                axios.post('prices/budget', {
                    budget: budget,
                }).then(function (response) {
                    toastr.success('{{ __('Saved') }}');
                }).catch(function (error) {
                    toastr.error('Something is going wrong');
                });
            });
        </script>
    @endslot


@endcomponent
