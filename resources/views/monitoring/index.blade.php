@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-fixedheader/css/fixedHeader.bootstrap4.min.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

        <style>
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

    @hasanyrole('Super Admin|admin')
    <div class="row mb-2">
        <div class="col-12">
            @include('monitoring.admin._btn')
        </div>
    </div>
    @endhasanyrole

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
            const LENGTH_MENU = JSON.parse('{{ $lengthMenu }}');
            const PAGE_LENGTH = '{{ $length }}';

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            const HIGHLIGHT_TR_CLASS = "table-success";

            $('.checkbox-toggle').click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $('.table tbody tr.main').removeClass(HIGHLIGHT_TR_CLASS);
                    $('.table tbody tr.main').find('.icheck-primary input[type="checkbox"]').prop('checked', false);
                    $('.checkbox-toggle .far.fa-check-square').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check all checkboxes
                    $('.table tbody tr.main').addClass(HIGHLIGHT_TR_CLASS);
                    $('.table tbody tr.main').find('.icheck-primary input[type="checkbox"]').prop('checked', true);
                    $('.checkbox-toggle .far.fa-square').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks)
            });

            $('[data-toggle="tooltip"]').tooltip({
                animation: false,
                trigger: 'hover',
            });

            window.loading();

            let table = $('#projects').DataTable({
                dom: '<"card-header"<"card-title"><"float-right"f><"float-right"l>><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                fixedHeader: true,
                lengthMenu: LENGTH_MENU,
                pageLength: PAGE_LENGTH,
                pagingType: "simple_numbers",
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "{{ __('Search project') }}",
                    paginate: {
                        "first": "«",
                        "last": "»",
                        "next": "»",
                        "previous": "«"
                    },
                },
                processing: false,
                serverSide: true,
                ajax: {
                    url: '/monitoring/projects/get',
                    type: 'POST',
                },
                order: [
                    [2, 'asc'],
                ],
                columnDefs: [
                    {orderable: true, "width": "150px", targets: 'name'},
                ],
                columns: [
                    {
                        orderable: false,
                        data: function (row, type, val, meta) {

                            let form = $('<div />', {
                                class: 'icheck-primary'
                            });

                            let input = $('<input />');

                            input.attr({
                                type: 'checkbox',
                                value: row.id,
                            });

                            return form.append(input)[0].outerHTML;
                        },
                    },
                    {
                        orderable: false,
                        data: null,
                        defaultContent: '<a href="#" class="dt-control text-muted click_tracking" data-click="Show project positions"><i class="fas fa-plus-circle"></i></a>',
                    },
                    {
                        title: '{{ __('Project') }}',
                        name: 'name',
                        data: function (row) {
                            return `<a href="/monitoring/${row.id}" class="text-bold">${row.name}</a>`;
                        },
                    },
                    {
                        title: '{{ __('Domain') }}',
                        name: 'url',
                        data: function (row) {
                            return `<a href="https://${row.url}" target="_blank" class="text-muted">${row.url} <i class="fas fa-external-link-square"></i></a>`;
                        },
                    },
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

                                let status = '{{ __('EMPTY') }}';

                                switch (item.pivot.status)
                                {
                                    case 1:
                                        status = '{{ __('TL') }}';
                                        break;
                                    case 2:
                                        status = '{{ __('SEO') }}';
                                        break;
                                    case 3:
                                        status = '{{ __('PM') }}';
                                        break;
                                }

                                li.append($('<span />', {class : 'badge badge-success navbar-badge'})
                                    .css({'right' : 0, 'left' : 0, 'top' : 'unset', 'bottom' : '-15px', cursor : 'pointer'})
                                    .text(status)
                                );

                                ul.append(li);
                            });

                            return ul[0].outerHTML;
                        },
                    },
                    {
                        orderable: false,
                        title: '{{ __('System') }}',
                        name: 'engines',
                        data: 'engines',
                    },
                    {
                        title: '{{ __('Words') }}',
                        name: 'words',
                        data: 'words',
                    },
                    {
                        title: '{{ __('Mid-position') }}',
                        name: 'middle',
                        data: 'middle',
                    },
                    {
                        title: '3 %',
                        name: 'top3',
                        data: function (row) {
                            let sup = subColorTag(row.diff_top3);

                            return row.top3 + sup;
                        },
                    },
                    {
                        title: '5 %',
                        name: 'top5',
                        data: function (row) {
                            let sup = subColorTag(row.diff_top5);

                            return row.top5 + sup;
                        },
                    },
                    {
                        title: '10 %',
                        name: 'top10',
                        data: function (row) {
                            let sup = subColorTag(row.diff_top10);

                            return row.top10 + sup;
                        },
                    },
                    {
                        title: '30 %',
                        name: 'top30',
                        data: function (row) {
                            let sup = subColorTag(row.diff_top30);

                            return row.top30 + sup;
                        },
                    },
                    {
                        title: '100 %',
                        name: 'top100',
                        data: function (row) {
                            let sup = subColorTag(row.diff_top100);

                            return row.top100 + sup;
                        },
                    },
                    {
                        title: '{{ __('Budget') }}',
                        name: 'budget',
                        data: function (row) {
                            let sup = $('<sup />').css('color', 'green');
                            if(row.mastered_percent)
                                sup.text(row.mastered_percent + '%');

                            return row.budget + sup[0].outerHTML;
                        },
                    },
                    {
                        title: '{{ __('Mastered') }}',
                        name: 'mastered',
                        data: function (row) {
                            let tops = JSON.parse(row.mastered_info);

                            if(tops && tops.total){
                                let small = $('<small />').css('color', 'green');
                                small.text(Math.floor(tops.total / (row.budget / 30) * 100) + '%');

                                return row.mastered + "<br />" + small[0].outerHTML;
                            }

                            return row.mastered;
                        },
                    },
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

                            let trash = $('<a />', {class: 'dropdown-item bg-danger'}).html('{{ __('Delete project') }}').prepend($('<i/>').addClass('fas fa-trash-alt mr-2'));
                            trash.attr('onclick', `onClickDeleteProject(${row.id})`);

                            let update = $('<a />', {
                                class: 'dropdown-item update-project',
                                "data-id": row.id,
                            }).html('{{ __('Update data') }}').prepend($('<i/>').addClass('fas fa-sync-alt mr-2'));

                            group.append([dropdown, menu]);
                            menu.append([update, addUser, exports, create, edit, folder, $('<div />', {class: 'dropdown-divider'}), trash]);

                            return group[0].outerHTML;
                        },
                        class: 'project-actions text-right',
                    },
                ],
                headerCallback: function(thead, data, start, end, display) {
                    let api = this.api();
                    let columns = api.columns( ['top3:name', 'top5:name', 'top10:name', 'top30:name', 'top100:name'] ).header();

                    $.each(columns, function(i, col){
                        let column = $(col);
                        column.addClass('text-nowrap');
                        column.html(column.text() + ' <i class="far fa-question-circle tooltip-on" title="{{ __('Percentage of keys in the top') }}"></i>');
                    });

                    let mastered = api.column( 'mastered:name' ).header();
                    $(mastered).addClass('text-nowrap').html('{{__('Mastered')}}  <i class="far fa-question-circle tooltip-on" title="В этом столбце показывается освоенный бюджет за один календарный день на момент снятия последней позиции. Ниже показывается процент освоенности в расчете на 30 каледнарных дней."></i>');
                },
                initComplete: function () {
                    let api = this.api();
                    let json = api.ajax.json();

                    this.find('tbody').on('click', 'tr.main', function () {
                        $(this).toggleClass(HIGHLIGHT_TR_CLASS);

                        if ($(this).hasClass(HIGHLIGHT_TR_CLASS)) {
                            $(this).find('.icheck-primary input[type="checkbox"]').prop('checked', true);
                        } else {
                            $(this).find('.icheck-primary input[type="checkbox"]').prop('checked', false);
                        }
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

                    // header card
                    this.closest('.card').find('.card-header .card-title').html("");
                    this.closest('.card').find('.card-header label').css('margin-bottom', 0);

                    let updatedDateText = json.updatedDate;
                    let dataTimeCache = $('<span />', {class: "data-time-cache"}).html(updatedDateText);
                    let CacheText = `{{ __('The summary data in the table is current as of the date:') }}${dataTimeCache[0].outerHTML} `;
                    let updateCacheIcon = $('<i />', {class: "fas fa-sync-alt"});
                    let updateCacheButton = $('<a />', {
                        class: "text-muted",
                        href: "javascript:void(0)"
                    }).html(updateCacheIcon);

                    updateCacheButton.click(function () {
                        $(this).hide();
                        window.loading();
                        axios.get('/monitoring/project/update-data-table/0')
                            .then(function () {
                                table.draw(false);
                                $('.data-time-cache').text(moment().format("DD.MM.YYYY H:mm"));
                            });
                        return false;
                    });

                    let updateCacheText = $('<div />', {class: "card-title ml-2"}).html(CacheText);
                    updateCacheText.append(updateCacheButton);
                    let updateCacheContainer = $('<div />', {class: "float-left"}).html(updateCacheText);
                    this.closest('.card').find('.card-header .card-title').after(updateCacheContainer);

                    axios.post('/monitoring/get/column/settings').then(function (response) {

                        $.each(response.data, function (i, col) {
                            if (col.state) {
                                table.column(col.column + ':name').visible(!col.state);
                                $(`.column-visible[data-column="${col.column}"]`).addClass('hover');
                            }
                        });
                    });

                    this.find('.tooltip-on').tooltip({
                        animation: false,
                        trigger: 'hover',
                        html: true,
                    });

                    this.on( 'processing.dt', function ( e, settings, processing ) {
                        let card = $(this).closest('#projects_wrapper');
                        let filter = card.find('.card-header .dataTables_filter .form-control');

                        if(filter.val().length > 0)
                            return;

                        if(processing) {
                            if(window.pleaseWait === undefined || window.pleaseWait.finishing)
                                window.loading();
                        } else{
                            if(window.pleaseWait)
                                window.pleaseWait.finish();
                        }
                    });
                },
                drawCallback: function () {
                    this.find('tbody tr').addClass('main');
                    $('.pagination').addClass('pagination-sm');
                },
            });

            search(table)

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

                let rows = table.rows('.' + HIGHLIGHT_TR_CLASS);
                let data = rows.data();

                if (!data.length) {
                    toastr.error("{{ __('Selected project') }}");
                    return false;
                }

                if (!window.confirm("{{__('Do you really want to delete?')}}"))
                    return false;

                $.each(data, function (index, row) {
                    deleteProject(row.id);
                });
            });

            function onClickDeleteProject(id) {

                if (!window.confirm("{{__('Do you really want to delete?')}}"))
                    return false;

                deleteProject(id);
            }

            function deleteProject(id) {
                if (id)
                    axios.delete(`monitoring/${id}`)
                        .then(function () {
                            table.draw(false);
                        });
                else
                    alert('Delete error');
            }

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

            function subColorTag(content) {
                if (!content)
                    return '';

                let color = (content.indexOf('+') > -1) ? 'green' : 'red';
                let sup = $('<sup />').css('color', color).text(content);

                return sup.prop('outerHTML');
            }

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
                            params: [
                                { text: '{{ __('Without status') }}', val: 'DEF' },
                                { text: '{{ __('Team Lead') }}', val: 'TL' },
                                { text: '{{ __('Seo') }}', val: 'SEO' },
                                { text: '{{ __('Project manager') }}', val: 'PM' },
                            ]
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

            $('#projects').on('click', '.update-project', function(){
                window.loading();
                let id = $(this).data('id');
                axios.get( '/monitoring/project/update-data-table/' + id ).then(function () {
                    table.draw(false);
                });
            });

            $('#projects').on('click', '.user-list li', function(){
                let self = $(this);
                let user = self.attr('user-id');
                let project = $(this).closest('tr').find('input[type="checkbox"]').val();

                $('.modal').modal('show').BootstrapModalFormTemplates({
                    title: '{{ __('Set user status') }}',
                    btnText: '{{ __('Save') }}',
                    fields: [
                        {
                            type: 'select',
                            name: "status",
                            label: '{{ __('User status') }}',
                            params: [
                                { text: '{{ __('Without status') }}', val: 'DEF' },
                                { text: '{{ __('Team Lead') }}', val: 'TL' },
                                { text: '{{ __('Seo') }}', val: 'SEO' },
                                { text: '{{ __('Project manager') }}', val: 'PM' },
                            ]
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

                return false;
            });
        </script>
    @endslot

@endcomponent
