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
            .dataTables_filter label {
                margin-bottom: 0;
            }
            .dt-center {
                text-align: center;
            }
            .help-block {
                color: #b11f1f;
                font-size: 12px;
            }
            .btn-secondary:hover {
                border-color: #ddd;
            }
            .grow-color {
                background-color: rgb(153, 228, 185);
            }
            .shrink-color {
                background-color: rgb(251, 225, 223);
            }
            input[type="checkbox"] {
                vertical-align: sub;
            }
            div.DTE_Field_Type_checkbox div.DTE_Field_InputControl, div.DTE_Field_Type_checkbox div.controls {
                padding-top: 0px;
                margin-top: 0em;
            }
        </style>
    @endslot

    <div class="row">
        <div class="col-12 mb-3">
            <a href="{{ route('monitoring.show', request('id')) }}" class="btn btn-default">Вернутся в проект</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 card-table">
            <div class="card">
                <table class="table table-hover table-sm projects" id="groups"></table>
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
        <script src="{{ asset('plugins/datatables-editor/js/datatables_editor.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-select/js/dataTables.select.min.js') }}"></script>
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

            let fields = [
                {
                    name: "id",
                    type: "hidden",
                },
                {
                    label: "Группа:",
                    name: "name",
                    fieldInfo: 'Название группы',
                    def: "",
                },
            ];

            let dynamicHideFields = [
                {
                    label: "Перенести запросы в раздел:",
                    name: "groups_option",
                    type:  "select",
                },
                {
                    label: "Пользователи:",
                    name: "users_option",
                    type: "checkbox",
                    def: "{{ $owner['id'] }}",
                },
            ];

            $.merge( fields, dynamicHideFields );

            let editor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('groups.action', request('id')) }}",
                table: "#groups",
                fields: fields,
                i18n: {
                    create: {
                        button: "+ Создать новую группу",
                        submit: "Создать",
                    },
                    multi: {
                        "title": "Несколько значений",
                        "info": "Выбранные элементы содержат разные значения для этого входа. Чтобы отредактировать и установить для всех элементов этого ввода одинаковое значение, нажмите здесь, в противном случае они сохранят свои индивидуальные значения.",
                        "restore": "Отменить изменения",
                        "noMulti": "Этот вход можно редактировать индивидуально, но не как часть группы."
                    },
                },
            });

            let table = $('#groups').DataTable({
                dom: '<"card-header"<"card-title"B><"float-right"f><"float-right"l>><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                fixedHeader: true,
                paging: false,
                lengthMenu: [5, 10, 30],
                pageLength: 10,
                pagingType: "simple_numbers",
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Найти группу",
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
                    url: "{{ route('groups.index', request('id')) }}",
                    type: 'GET',
                },
                order: [
                    [2, 'asc'],
                ],
                columnDefs: [
                    { orderable: true, targets: [1, 2, 3, 4] },
                    { orderable: false, targets: '_all' },
                ],
                columns: [
                    {
                        orderable: false,
                        data: null,
                        defaultContent: '<a href="#" class="dt-control text-muted"><i class="fas fa-plus-circle"></i></a>',
                    },
                    {
                        title: 'ID',
                        data: 'id',
                        name: 'id',
                    },
                    {
                        title: 'Группы',
                        data: 'name',
                        name: 'name',
                    },
                    {
                        title: 'Запросы',
                        data: 'queries',
                        name: 'queries',
                    },
                    {
                        title: 'Добавлено',
                        data: 'created',
                        name: 'created_at',
                    },
                    {
                        title: 'Пользователи',
                        orderable: false,
                        data: function(row) {
                            let users = row.users;
                            let list = $('<ul />', { class: 'list-inline'});

                            $.each(users, function(i, val){
                                let li = $('<li />', {class: 'list-inline-item', title: val.name + ' ' + val.last_name})
                                    .append($('<img />', {class: 'table-avatar', src: val.image}));

                                list.append(li);
                            });

                            return list[0].outerHTML;
                        }
                    },
                    {
                        title: 'Открыть группу',
                        data: function(row) {
                            let icon = '<i class="fa fa-folder-open" />';
                            return '<a href="/monitoring/'+ row.monitoring_project_id +'?group='+ row.id +'" class="btn btn-sm btn-default" title="Открыть">'+ icon +'</a>';
                        },
                        className: "dt-center",
                        orderable: false
                    },
                    {
                        title: 'Редактировать',
                        data: function(row) {
                            let icon = '<i class="fas fa-pen" />';
                            return '<a href="javascript:void(0)" class="btn btn-sm btn-default" title="Редактировать">'+ icon +'</a>';
                        },
                        className: "dt-center editor-edit",
                        orderable: false
                    },
                    {
                        title: 'Удалить',
                        data: function(row) {
                            let icon = '<i class="fas fa-trash" />';
                            return '<a href="javascript:void(0)" class="btn btn-sm btn-default" title="Удалить">'+ icon +'</a>';
                        },
                        className: "dt-center editor-delete",
                        orderable: false
                    }
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
                        extend: "create",
                        editor: editor,
                        className: "btn-default btn-sm",
                        action: function() {
                            dynamicHideFields.map(obj => editor.field(obj.name).hide());
                            editor.create({
                                title: "Создать новую группу",
                                buttons: "Создать",
                            });
                        }
                    },
                    {
                        text: "Редактировать выбранные",
                        className: "btn-default btn-sm",
                        extend: "edit",
                        editor: editor
                    },
                ],
                headerCallback: function(thead, data, start, end, display) {
                    let api = this.api();

                    let count = data.reduce((s, c) => s + c.queries, 0);

                    $( api.column( 2 ).header() ).html('Группы: ' + (end-start));
                    $( api.column( 3 ).header() ).html('Запросы: ' + count);
                },
                initComplete: function () {
                    let api = this.api();

                    this.find('tbody').on('click', 'td .dt-control', function () {
                        let icon = $(this).find('i');
                        let tr = $(this).closest('tr');
                        let row = api.row(tr);

                        if (row.child.isShown()) {
                            // This row is already open - close it
                            row.child.hide();
                            tr.removeClass('shown');

                            icon.removeClass('fa-minus-circle');
                            icon.addClass('fa-plus-circle');
                        } else {
                            // Open this row
                            let data = row.data();
                            let projectId = data.monitoring_project_id;
                            let groupId = data.id;

                            axios.get(`/monitoring/${projectId}/child-rows/get/${groupId}`).then(function(response){

                                let content = $(response.data);

                                $.each(content.find('.top'), function(i, el){

                                    let str = $(el).text();

                                    if(str.indexOf('+') > 0)
                                        $(el).addClass('grow-color');

                                    if(str.indexOf('-') > 0)
                                        $(el).addClass('shrink-color');
                                });

                                row.child(content).show();

                                content.find('.tooltip-child-table').tooltip({
                                    animation: false,
                                    trigger: 'hover',
                                });
                            });

                            tr.addClass('shown');

                            icon.removeClass('fa-plus-circle');
                            icon.addClass('fa-minus-circle')
                        }

                        return false;
                    });
                },
            });

            // Edit record
            $('#groups').on('click', 'td.editor-edit', function (e) {
                e.preventDefault();

                dynamicHideFields.map(obj => editor.field(obj.name).show());

                editor.edit( $(this).closest('tr'), {
                    title: 'Редактировать группу',
                    buttons: 'Обновить',
                } );
            } );

            // Delete a record
            $('#groups').on('click', 'td.editor-delete', function (e) {
                e.preventDefault();

                editor.remove( $(this).closest('tr'), {
                    title: 'Удалить группу',
                    message: 'Вы уверены, что хотите удалить эту группу?',
                    buttons: 'Удалить'
                } );
            });

        </script>
    @endslot


@endcomponent
