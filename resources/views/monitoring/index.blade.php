@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-fixedheader/css/fixedHeader.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-select/css/select.bootstrap4.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

        <style>
            .table-card .card-body {
                min-height: 270px;
            }
            .add-user {
                display: none;
            }
            .toast {
                opacity: 1 !important;
            }

            .table-hover tbody tr:hover {
                background-color: #FFF;
            }

            .table-hover tbody tr.main:hover {
                color: #212529;
                background-color: rgba(0, 0, 0, .075);
            }

            .dataTables_processing {
                margin: 10px auto;
                z-index: 4;
            }

            .grow-color {
                background-color: rgb(153, 228, 185);
            }

            .shrink-color {
                background-color: rgb(251, 225, 223);
            }

            table.dataTable > thead .sorting:before, table.dataTable > thead .sorting_asc:before, table.dataTable > thead .sorting_desc:before, table.dataTable > thead .sorting_asc_disabled:before, table.dataTable > thead .sorting_desc_disabled:before {
                right: 0.5em;
            }

            table.dataTable > thead .sorting:after, table.dataTable > thead .sorting_asc:after, table.dataTable > thead .sorting_desc:after, table.dataTable > thead .sorting_asc_disabled:after, table.dataTable > thead .sorting_desc_disabled:after {
                right: 0em;
            }
            .dropdown-item {
                cursor: pointer;
            }
            .user-list .badge-success {
                display: none;
            }
            .user-list li:hover .badge-success {
                display: block;
            }

            .loader {
                border: 3px solid #f3f3f3; /* Light grey */
                border-top: 3px solid #3498db; /* Blue */
                border-radius: 50%;
                width: 20px;
                height: 20px;
                animation: spin 2s linear infinite;
                float: left;
                margin: 10px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            div.dataTables_wrapper div.dataTables_info {
                padding-top: .45em;
            }
        </style>

        @hasanyrole('Super Admin|admin')
        <style>
            .add-user {
                display: inline-block;
            }
        </style>
        @endhasanyrole
    @endslot

    @if($foreignProject->count())
    <div class="row">
        @include('monitoring.partials._approve')
    </div>
    @endif

    <div class="row mb-1">
        @include('monitoring.partials._buttons')
    </div>

    <div class="row">
        @include('monitoring.partials._table')
    </div>

    <div class="row mb-2">
        <div class="col-12">
            @include('monitoring.admin._btn')
        </div>
    </div>

    @include('monitoring.keywords.modal.main')

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('plugins/bootstrap-modal-form-templates/bootstrap-modal-form-templates.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-select/js/dataTables.select.js') }}"></script>
        <script src="{{ asset('plugins/datatables-select/js/select.bootstrap4.js') }}"></script>
        <script src="{{ asset('plugins/datatables/search.js') }}"></script>
        <!-- Moment js -->
        <script src="{{ asset('plugins/moment/moment-with-locales.min.js') }}"></script>
        <!-- Papa parse -->
        <script src="{{ asset('plugins/papaparse/papaparse.min.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

        <script>
            const PROJECTS_COUNT = '{{ $countApprovedProject }}';

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            let table = $('#projects').DataTable({
                dom: '<"card-header"<"card-title"i><"loader"><"float-right"f><"float-right"l>><"card-body p-0 overflow-auto"rt><"clear">',
                fixedHeader: true,
                paging: false,
                info: true,
                select: {
                    style: 'multi',
                    selector: 'td:not(:last-child)',
                },
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "{{ __('Search project') }}"
                },
                processing: false,
                order: [
                    [1, 'asc'],
                ],
                columns: [
                    {
                        orderable: false,
                        data: function(){
                            return '<a href="#" class="dt-control text-muted click_tracking" data-click="Show project positions"><i class="fas fa-plus-circle"></i></a>';
                        }
                    }, // 0
                    {
                        title: '{{ __('Domain') }}',
                        name: 'url',
                        className: 'text-nowrap',
                        data: function (row) {
                            return `<a class="text-muted" href="https://${row.url}" target="_blank">${row.url}</a>`;
                        },
                    }, // 1
                    {
                        title: 'Название',
                        name: 'name',
                        data: 'name',
                    }, // 2
                    {
                        orderable: false,
                        title: '{{ __('Users') }}',
                        name: 'users',
                        data: function (row) {
                            let ul = $('<ul />', { class : 'list-inline user-list'});

                            $.each(row.users, function(i, item){
                                let li = $('<li />', {class : 'list-inline-item position-relative tooltip-on', "user-id": item.id, title : item.name + ' ' + item.last_name}).append($('<img />', { class : 'table-avatar', src : item.image }));

                                if(item.pivot.admin)
                                    li.append($('<span />', {class : 'badge badge-danger navbar-badge'})
                                        .css({'left' : 0, 'right' : 0, 'top' : '-10px'}).text('ADMIN'));
                                else{
                                    if(row.pivot.admin){
                                        li.append($('<span />', {class : 'badge badge-secondary navbar-badge detach-user'}).css({
                                            cursor: 'pointer',
                                            top: '-5px',
                                            right: 0,
                                            "font-size": 'x-small',
                                        }).attr("data-id", item.id).html('<i class="fas fa-times"></i>'));
                                    }
                                }

                                li.append($('<span />', {class : 'badge badge-success navbar-badge'})
                                    .css({'right' : 0, 'left' : 0, 'top' : 'unset', 'bottom' : '-15px', cursor : 'pointer', "z-index" : 1})
                                    .text(item.status.code)
                                );

                                ul.append(li);
                            });

                            return ul[0].outerHTML;
                        },
                    }, // 3
                    {
                        orderable: false,
                        className: 'text-nowrap',
                        title: '<i class="fab fa-yandex fa-sm"></i> <i class="fab fa-google fa-sm"></i>',
                        name: 'engines',
                        data: 'engines',
                    }, // 4
                    {
                        title: '{{ __('Words') }}',
                        name: 'words',
                        data: 'words',
                    }, // 5
                    {
                        name: 'middle',
                        data: 'middle',
                    }, // 6
                    {
                        title: '3 %',
                        name: 'top3',
                        data: 'top3',
                    }, // 7
                    {
                        title: '5 %',
                        name: 'top5',
                        data: 'top5',
                    }, // 8
                    {
                        title: '10 %',
                        name: 'top10',
                        data: 'top10',
                    }, // 9
                    {
                        title: '30 %',
                        name: 'top30',
                        data: 'top30',
                    }, // 10
                    {
                        title: '100 %',
                        name: 'top100',
                        data: 'top100',
                    }, // 11
                    {
                        title: '{{ __('Budget') }}',
                        name: 'budget',
                        data: function (row) {
                            let sup = $('<sup />').css('color', 'green');
                            if(row.mastered_percent)
                                sup.text(row.mastered_percent + '%');

                            return currencyFormatRu(row.budget) + sup[0].outerHTML;
                        },
                    }, // 12
                    {
                        visible: false, searchable: false, data: function (row) {
                            let percent = Math.floor(row.mastered / (row.budget / 30) * 100);

                            return Number.isNaN(percent) ? 0 : percent;
                        }
                    }, // 13
                    {
                        "iDataSort": 13,
                        title: '{{ __('Mastered') }}',
                        name: 'mastered',
                        data: function (row) {
                            if(row.mastered > 0){
                                let small = $('<small />').css('color', 'green');
                                small.text(Math.floor(row.mastered / (row.budget / 30) * 100) + '%');

                                return currencyFormatRu(row.mastered) + "<br />" + small[0].outerHTML;
                            }

                            return currencyFormatRu(row.mastered);
                        },
                    }, // 14
                    {
                        orderable: false,
                        data: function (row) {

                            if(row.pivot.admin == false){
                                let view = $('<a />', {class: 'btn btn-primary btn-sm', href: '/monitoring/' + row.id}).append($('<i />', {class: 'fas fa-folder'})).append(' {{ __('View') }}');
                                return view[0].outerHTML;
                            }

                            let group = $('<div />', { class: "btn-group"});

                            let dropdown = $('<button />', {
                                type: 'button',
                                "data-toggle": 'dropdown',
                                "data-offset": '-170',
                                class: 'btn btn-info dropdown-toggle',
                            }).append($('<i />', { class: 'fas fa-bars'}));

                            let menu = $('<div />', {class: 'dropdown-menu'});

                            let addUser = $('<a />', {class: 'dropdown-item add-user', "data-id": row.id}).html('{{ __('Add user') }}').prepend($('<i/>').addClass('far fa-user mr-2'));

                            let exports = $('<a />', {
                                class: 'dropdown-item click_tracking',
                                "data-click": 'Export project',
                                "data-toggle": 'modal',
                                "data-target": '.modal',
                                "data-type": 'export-edit',
                                "data-id": row.id,
                            }).html('{{ __('Project export') }}').prepend($('<i/>').addClass('fas fa-file-download mr-2'));

                            let create = $('<a />', {
                                class: 'dropdown-item',
                                "data-toggle": 'modal',
                                "data-target": '.modal',
                                "data-type": 'create_keywords',
                                "data-id": row.id,
                            }).text('{{ __('Add keyword') }}').prepend($('<i/>').addClass('far fa-plus-square mr-2'));

                            let edit = $('<a />', {
                                class: 'dropdown-item',
                                href: `/monitoring/create#id=${row.id}`,
                            }).html('{{ __('Edit project') }}').prepend($('<i/>').addClass('fas fa-edit mr-2'));

                            let folder = $('<a />', {
                                class: 'dropdown-item',
                                href: '/monitoring/' + row.id + '/groups',
                                title: '',
                            }).html('{{ __('Project groups') }}').prepend($('<i/>').addClass('far fa-folder mr-2'));

                            let open = $('<a />', {
                                class: 'dropdown-item',
                                href: `/monitoring/${row.id}`,
                            }).html('{{ __('Open project') }}').prepend($('<i/>').addClass('far fa-folder-open mr-2'));

                            group.append([dropdown, menu]);
                            menu.append([open, $('<div />', {class: 'dropdown-divider'}), addUser, exports, create, edit, folder]);

                            return group[0].outerHTML;
                        },
                        class: 'project-actions text-right',
                    }, // 15
                ],
                headerCallback: function(thead, data, start, end, display) {
                    let api = this.api();
                    let columns = api.columns( ['top3:name', 'top5:name', 'top10:name', 'top30:name', 'top100:name'] ).header();

                    $.each(columns, function(i, col){
                        let column = $(col);
                        column.addClass('text-nowrap');
                        column.html(column.text() + ' <i class="far fa-question-circle" data-toggle="tooltip" title="{{ __('Percentage of keys in the top') }}"></i>');
                    });

                    let mastered = api.column( 'mastered:name' ).header();
                    $(mastered).addClass('text-nowrap').html('{{__('Mastered')}}  <i class="far fa-question-circle" data-toggle="tooltip" title="В этом столбце показывается освоенный бюджет за один календарный день на момент снятия последней позиции. Ниже показывается процент освоенности в расчете на 30 каледнарных дней."></i>');

                    $(api.column( 'middle:name' ).header()).addClass('text-nowrap').html('{{ __('Position') }} <i class="far fa-question-circle" data-toggle="tooltip" title="{{ __('Mid-position') }}"></i>');
                },
                initComplete: function () {
                    let api = this.api();

                    $('#filter-user-status').change(function(){
                        api.column('users:name').search($(this).val()).draw();
                    });

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
                            window.loading();

                            axios.get(`/monitoring/${data.id}/child-rows/get`).then(function (response) {
                                let content = $(response.data);

                                window.pleaseWait.finish();

                                $.each(content.find('.top'), function (i, el) {

                                    let str = $(el).text();

                                    if (str.indexOf('+') > 0)
                                        $(el).addClass('grow-color');

                                    if (str.indexOf('-') > 0)
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

                    axios.post('/monitoring/get/column/settings').then(function (response) {
                        $.each(response.data, function (i, col) {
                            if (col.state) {
                                table.column(col.column + ':name').visible(!col.state);
                                $(`.column-visible[data-column="${col.column}"]`).addClass('hover');
                            }
                        });
                    });
                },
                infoCallback: function (settings, start, end) {
                    if(end == PROJECTS_COUNT)
                    {
                        allProjectsUploaded();

                        return '{{ __('Projects count') }} ' + PROJECTS_COUNT;
                    }

                    return '{{ __('Loading projects') }}: ' + end;
                }
            });

            for(let i = 0; i < PROJECTS_COUNT; i++)
            {
                $.ajax({
                    type: 'POST',
                    url: '/monitoring/projects/get',
                    data:  {
                        length: 1,
                        start: i,
                    },
                    success: (response) =>
                    {
                        if(!response.length)
                            return false;

                        table.row.add(response[0]).draw(false);
                    },
                });
            }

            search(table);

            $('.column-visible').click(function (e) {
                e.preventDefault();

                let name = $(this).data('column');
                let column = table.column(name + ':name');
                let visible = column.visible();

                column.visible(!visible);

                $(this).toggleClass('hover', visible);

                axios.post('/monitoring/set/column/settings', {
                    column: name,
                    state: visible,
                });
            });

            $('.checkbox-delete').click(function () {

                let rows = table.rows({ selected: true });
                let data = rows.data();

                if (!data.length) {
                    toastr.error("{{ __('Selected project') }}");
                    return false;
                }

                if (!window.confirm("{{__('Do you really want to delete?')}}"))
                    return false;

                $.each(data, function (index, row) {
                    axios.delete(`monitoring/${row.id}`)
                });

                rows.remove().draw();
            });

            $('.modal').on('show.bs.modal', function (event) {

                let modal = $(this);
                let button = $(event.relatedTarget);
                let type = button.data('type');
                let projectId = button.data('id');

                if (type === 'create_keywords') {

                    axios.get(`/monitoring/keywords/${projectId}/create`).then(function (response) {

                        let content = response.data;

                        modal.find('.modal-content').html(content);
                        modal.find('#upload-queries').click(function () {

                            let self = $(this);
                            let csv = self.closest('.input-group').find('#upload');

                            if (csv[0].files.length && csv[0].files[0].type === 'text/csv') {

                                csv.parse({
                                    config: {
                                        skipEmptyLines: 'greedy',
                                        complete: function (result) {

                                            let value = '';
                                            $.each(result.data, function (i, item) {

                                                if (item[0])
                                                    value += item[0] + '\r\n';
                                            });

                                            modal.find('textarea[name="query"]').val(value);
                                        },
                                        download: 0
                                    }
                                });

                            } else {

                                toastr.error('Загрузите файл формата .csv');
                            }
                        });
                    }).then(function () {

                        let group = modal.find('.custom-select[name="monitoring_group_id"]');
                        if (group.length) {

                            group.select2({
                                theme: 'bootstrap4'
                            });

                            modal.find('#create-group').click(function () {
                                let el = $(this);
                                let input = el.closest('.input-group').find('input');

                                if (input.val()) {

                                    let id_project = input.data('id');

                                    axios.post('/monitoring/groups', {
                                        monitoring_project_id: id_project,
                                        type: "keyword",
                                        name: input.val(),
                                    }).then(function (response) {

                                        let newOption = new Option(response.data.name, response.data.id, false, true);
                                        group.append(newOption).trigger('change');

                                        input.val(null);
                                    }).catch(function (error) {
                                        toastr.error('Something is going wrong');
                                    });
                                }
                            });
                        }

                        modal.find('.save-modal').click(function (e) {
                            let self = $(this);
                            let form = self.closest('.modal-content').find('form');
                            let action = form.attr('action');
                            let method = form.attr('method');
                            let data = {};

                            $.each(form.serializeArray(), function (inc, item) {
                                $.extend(data, {[item.name]: item.value});
                            });

                            if (data.hasOwnProperty('monitoring_group_id') === false || data.monitoring_group_id.length < 1) {
                                e.preventDefault();
                                form.find('.invalid-feedback.monitoring_group_id').fadeIn().delay(3000).fadeOut();
                                return false;
                            }

                            if (data.query.length < 1) {
                                e.preventDefault();
                                form.find('.invalid-feedback.query').fadeIn().delay(3000).fadeOut();
                                return false;
                            }

                            axios({
                                method: method,
                                url: action,
                                data: data
                            }).then(function (response) {
                                table.draw(false);
                                self.closest('.modal').modal('hide');
                                toastr.success('Запросы добавлены');
                            }).catch(function (error) {
                                console.log(error);
                            });
                        });
                    });
                }

                if(type === 'export-edit'){
                    axios.get(`/monitoring/${projectId}/export/edit`).then(function (response) {
                        let content = response.data;
                        modal.find('.modal-content').html(content);

                        modal.find('select[name="mode"]').change(function(){
                            if($(this).val() === 'finance')
                                modal.find('#finance').removeClass('d-none');
                            else
                                modal.find('#finance').addClass('d-none');
                        });

                        //Date picker
                        modal.find('#startDatePicker, #endDatePicker').datetimepicker({
                            format: 'L',
                            locale: 'ru',
                        });
                    });
                }
            });

            $('.approve-project').click(function(){
                let self = $(this);
                let id = self.closest('tr').data('id');

                axios.post('{{ route('approve.project') }}', {
                    approve: 1,
                    id: id
                }).then(function (response) {
                    window.location.reload();
                });
            });

            $('.cancel-project').click(function(){
                let self = $(this);
                let id = self.closest('tr').data('id');

                axios.post('{{ route('approve.project') }}', {
                    approve: 0,
                    id: id
                }).then(function (response) {
                    toastr.success('{{ __('Request has been canceled') }}');
                    self.closest('tr').remove();
                });
            });

            $('#projects').on('click', '.add-user', function () {
                let self = $(this);
                let id = self.data('id');

                axios.get('/monitoring/get-user-status-options')
                    .then(function(response){
                        $('.modal').modal('show').BootstrapModalFormTemplates({
                            title: '{{ __('Add user to project') }}',
                            btnText: '{{ __('Invite') }}',
                            fields: [
                                {
                                    type: 'text',
                                    name: "email",
                                    label: '{{ __('Email user') }} (Если вы хотите добавить сразу несколько пользователей перечислите email через запятую)',
                                    params: [{
                                        val: "",
                                        placeholder: 'test@mail.ru, test2@mail.ru, test3@mail.ru',
                                    }]
                                },
                                {
                                    type: 'select',
                                    name: "status",
                                    label: '{{ __('User status') }}',
                                    params: response.data,
                                }
                            ],
                            onAgree: function (m) {
                                const formData = new FormData(m.find('form').get(0));
                                let email = formData.getAll('email')[0];
                                let status = formData.getAll('status')[0];

                                axios.post('{{ route('approve.attach') }}', {
                                    id: id,
                                    email: email,
                                    status: status,
                                }).then(function (response) {
                                    toastr.success('{{ __('Request has been sent') }}');
                                    table.draw(false);
                                    m.modal('hide');
                                }).catch(function (error) {
                                    toastr.error('{{ __('Wrong mail') }}');
                                });
                            }
                        });
                    })
                    .catch(function(error){
                        console.log(error);
                    });
            });

            $('#projects').on('click', '.detach-user', function(){
                let ProjectId = $(this).closest('tr').find('input[type="checkbox"]').val();
                let UserId = $(this).data('id');

                if (window.confirm("{{ __('Detach user from project?') }}")) {

                    axios.post('{{ route('user.detach') }}', {
                        project_id: ProjectId,
                        user_id: UserId,
                    }).then(function (response) {
                        toastr.success('{{ __('User deleted') }}');
                        table.draw(false);
                    }).catch(function (error) {
                        toastr.error('{{ __('Wrong request') }}');
                    });
                }

                return false;
            });

            $('#projects').on('click', '.user-list li', function(){
                let self = $(this);
                let user = self.attr('user-id');
                let project = $(this).closest('tr').find('input[type="checkbox"]').val();

                axios.get('/monitoring/get-user-status-options')
                    .then(function(response){
                        $('.modal').modal('show').BootstrapModalFormTemplates({
                            title: '{{ __('Set user status') }}',
                            btnText: '{{ __('Save') }}',
                            fields: [
                                {
                                    type: 'select',
                                    name: "status",
                                    label: '{{ __('User status') }}',
                                    params: response.data,
                                }
                            ],
                            onAgree: function (m) {
                                const formData = new FormData(m.find('form').get(0));
                                let status = formData.getAll('status')[0];

                                axios.post('{{ route('monitoring.user.project.status') }}', {
                                    user: user,
                                    project: project,
                                    status: status,
                                }).then(function (response) {
                                    toastr.success('{{ __('Saved') }}');
                                    table.draw(false);
                                    m.modal('hide');
                                }).catch(function (error) {
                                    toastr.error('{{ __('You must be administrator project.') }}');
                                });
                            }
                        });
                    })
                    .catch(function(error){
                        console.log(error);
                    });

                return false;
            });

            $('.checkbox-toggle').click(function(){

                let el = $(this);

                el.find('.far').toggleClass('fa-square');
                el.find('.far').toggleClass('fa-check-square');

                if(el.find('.far').hasClass('fa-check-square'))
                    table.rows().select();
                else
                    table.rows().deselect();
            });

            function allProjectsUploaded()
            {
                $('.loader').remove();

                $('[data-toggle="tooltip"]').tooltip({
                    animation: false,
                    trigger: 'hover',
                });
            }
        </script>
    @endslot

@endcomponent
