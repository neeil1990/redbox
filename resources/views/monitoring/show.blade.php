@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

        <style>
            .dTable {
                display: none;
            }

            .dataTables_processing {
                margin: 10px auto;
                z-index: 4;
            }

            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }

            .popover {
                max-width: none;
            }

            .progress-spinner {
                position: absolute;
                top: 10%;
                width: 100%;
                text-align: center;
                z-index: 1;
            }

            .reset-zoom {
                position: absolute;
                top: 50px;
                right: 30px;
            }

            .dataTables_scrollHead {
                position: sticky !important;
                top: 0px;
                z-index: 1;
                background-color: white;
            }
        </style>
    @endslot

    <div class="row">
        @foreach($navigations as $navigation)
            <div class="col-lg-2 col-6">
                <a href="{{ $navigation['href'] }}" class="small-box {{ $navigation['bg'] }}" style="min-height: 137px">
                    <div class="inner">
                        @if($navigation['h3'])
                            <h3 class="mb-0">{{ $navigation['h3'] }}</h3>
                        @endif

                        {!! $navigation['content'] !!}

                        @isset($navigation['small'])
                            <small>{!! $navigation['small'] !!}</small>
                        @endisset

                        @isset($navigation['actions'])
                            <small>{!! $navigation['actions'] !!}</small>
                        @endisset
                    </div>
                    <div class="icon">
                        <i class="{{ $navigation['icon'] }}"></i>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @include('monitoring.partials.show.filter')

    <div class="row">
        <div class="col-12">
            @include('monitoring.partials.show.charts')
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <a href="{{ route('groups.index', $project->id) }}" class="btn btn-default">Управление группами проекта</a>

            @can('update_occurrence_monitoring')
                <a href="javascript:void(0)" id="occurrence-update" class="btn btn-default">Обновить частотность проекта</a>
            @endcan

            <a href="{{ route('prices.index', $project->id) }}" id="" class="btn btn-default">Цена запросов</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 card-table">
            <div class="card processing">
                <div class="dataTables_processing"><img src="/img/1485.gif" style="width: 50px; height: 50px;"></div>
            </div>
            <div class="card dTable">
                <table class="table table-hover table-responsive table-bordered text-center" id="monitoringTable"></table>
            </div>
            <!-- /.card -->
        </div>
    </div>

    @include('monitoring.keywords.modal.main')

    <div class="modal fade" id="setRelation" tabindex="-1" aria-labelledby="setRelationLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setRelationLabel">Свзязать проекты</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="this">Текущий проект</label>
                        <input type="text" class="form form-control" value="{{ $project->url }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="checklists">Чеклист</label>
                        <select name="checklists" id="checklists" class="custom-select">
                            @foreach(\App\Checklist::where('user_id', \Illuminate\Support\Facades\Auth::id())->get() as $checklist)
                                <option value="{{ $checklist->id }}">{{ $checklist->url }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="set-relation">{{ __('Save') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
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
        <!-- Select2 -->
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <!-- InputMask -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <!-- Papa parse -->
        <script src="{{ asset('plugins/papaparse/papaparse.min.js') }}"></script>

        <!-- Charts -->
        <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>
        <script src="{{ asset('plugins/chart.js/3.9.1/plugins/chartjs-plugin-crosshair.js') }}"></script>
        <script src="{{ asset('plugins/chart.js/3.9.1/plugins/chartjs-plugin-datalabels.js') }}"></script>

        <script>
            $(document).on('click', '#set-relation', function () {
                if ($('#checklists').val() == null) {
                    alert('Вам нужно указать чеклист')
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: "{{ route('checklist.monitoring.relation') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        checklistId: $('#checklists').val(),
                        monitoringId: {{ $project->id }}
                    },
                    success: function () {
                        window.location.reload()
                    },
                    error: function (response) {

                    }
                })
            })

            $(document).ready(function () {
                let $buttonElement = $('button.change-tag');
                let $aElement = $('<a></a>');
                $aElement.attr('href', $buttonElement.attr('href'));
                $aElement.attr('target', $buttonElement.attr('target'));
                $aElement.addClass($buttonElement.attr('class'));
                $aElement.text($buttonElement.text());
                $buttonElement.replaceWith($aElement);
            })
        </script>

        <script>
            const PROJECT_ID = '{{ $project->id }}';
            const PROJECT_NAME = '{{ $project->name }}';
            const REGION_ID = '{{ request('region', null) }}';
            const GROUP_ID = '{{ request('group', null) }}';
            const DATES = '{{ request('dates', null) }}';
            const MODE = '{{ request('mode', null) }}';
            const PAGE_LENGTH = '{{ $length }}';
            const LENGTH_MENU = JSON.parse('{{ $lengthMenu }}');
            const MAIN_COLUMNS_COUNT = 7;

            let table = $('#monitoringTable');

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            axios.post(`/monitoring/${PROJECT_ID}/table`, {
                length: PAGE_LENGTH,
                region_id: REGION_ID,
                dates_range: DATES,
                mode_range: MODE,
            }).then(function (response) {

                let columns = [];

                $.each(response.data.columns, function (i, item) {
                    if ('{{ !auth()->user()->can('edit_query_monitoring') }}' && '{{ !auth()->user()->can('delete_query_monitoring') }}' && i === "btn") {
                        return;
                    }

                    let width = null;
                    let orderable = false;

                    if (i === 'query') {
                        width = '300px';
                        orderable = true;
                    }

                    if (i === 'group') {
                        orderable = true;
                    }

                    columns.push({
                        'title': item,
                        'name': i,
                        'data': i,
                        'width': width,
                        'orderable': orderable,
                    });
                });

                let dTable = table.DataTable({
                    dom: '<"card-header d-flex align-items-center"<"card-title"><"float-right"l>><"card-body p-0"<"mailbox-controls">rt<"mailbox-controls">><"card-footer clearfix"p><"clear">',
                    scrollX: true,
                    lengthMenu: LENGTH_MENU,
                    pageLength: PAGE_LENGTH,
                    pagingType: "simple_numbers",
                    language: {
                        lengthMenu: "_MENU_",
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        paginate: {
                            "first": "«",
                            "last": "»",
                            "next": "»",
                            "previous": "«"
                        },
                        processing: '<img src="/img/1485.gif" style="width: 50px; height: 50px;">',
                    },
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `/monitoring/${PROJECT_ID}/table`,
                        type: 'POST',
                        data: {
                            region_id: REGION_ID,
                            dates_range: DATES,
                            mode_range: MODE,
                        },
                    },
                    columns: columns,
                    //rowReorder: true,
                    order: [
                        [columns.findIndex((elem) => elem.data === 'query'), 'asc'],
                    ],
                    columnDefs: [
                        {orderable: false, targets: '_all'},
                    ],
                    initComplete: function () {
                        let api = this.api();

                        let url = new URL(window.location.href);
                        let params = new URLSearchParams(url.search);

                        axios.get(`/monitoring/keywords/show/controls/${PROJECT_ID}`).then(function (response) {

                            let container = $('.mailbox-controls');
                            let content = response.data;

                            container.html(content);

                            let checkbox = container.find('.checkbox-toggle');

                            //Enable check and uncheck all functionality
                            checkbox.click(function () {
                                let clicks = $(this).data('clicks');
                                if (clicks) {
                                    //Uncheck all checkboxes
                                    $('.table tbody tr').find('input[type="checkbox"]').prop('checked', false);
                                    $('.far.fa-check-square', checkbox).removeClass('fa-check-square').addClass('fa-square');
                                } else {
                                    //Check all checkboxes
                                    $('.table tbody tr').find('input[type="checkbox"]').prop('checked', true);
                                    $('.far.fa-square', checkbox).removeClass('fa-square').addClass('fa-check-square');
                                }
                                $(this).data('clicks', !clicks)
                            });

                            let deletes = container.find('.delete-multiple');

                            deletes.click(function () {

                                let checkboxes = $('.table tbody tr').find('input[type="checkbox"]:checked');
                                if (checkboxes.length) {

                                    if (window.confirm("Do you really want to delete?")) {

                                        $.each(checkboxes, function (i, checkbox) {
                                            let id = $(checkbox).val();

                                            axios.delete(`/monitoring/keywords/${id}`);
                                        });

                                        window.location.reload();
                                    }
                                } else {
                                    toastr.error('Выберите хотя бы один элемент.');
                                }
                            });

                            container.find('.parse-positions').click(function () {
                                let select = $('#searchengines');

                                let params = [];
                                select.find('option[value!=""]').map((i, item) => {
                                    let region = $(item);
                                    params.push({
                                        val: region.val(),
                                        text: region.text(),
                                        checked: region.prop('selected')
                                    });
                                });

                                $('.modal.general').modal('show').BootstrapModalFormTemplates({
                                    title: "Обновить регионы",
                                    fields: [
                                        {
                                            type: 'checkbox',
                                            name: 'regions',
                                            label: '',
                                            params: params
                                        },
                                    ],
                                    onAgree: function (m) {
                                        const formData = new FormData(m.find('form').get(0));
                                        let regions = formData.getAll('regions');

                                        axios.get(`/monitoring/${PROJECT_ID}/count`).then(function (response) {
                                            let limits = (response.data.queries * regions.length);

                                            if (!limits || !window.confirm(`Будет списанно ${limits} лимитов`)) {
                                                m.modal('hide');
                                                return false;
                                            }

                                            axios.post('/monitoring/parse/positions/project', {
                                                projectId: PROJECT_ID,
                                                regions: regions,
                                            }).then(function (response) {
                                                m.modal('hide');
                                                if (response.data.status)
                                                    toastr.success(response.data.msg + " -" + response.data.count);
                                                else
                                                    toastr.error(response.data.error);
                                            });
                                        });
                                    }
                                });
                            });

                            container.find('.parse-positions-keys').click(function () {

                                let arrKeys = [];
                                let keys = $('.table tbody tr').find('input[type="checkbox"]:checked');
                                let region = $('#searchengines').val();

                                if (!region.length) {
                                    toastr.error('Нужно выбрать регион!');
                                    return false;
                                }

                                $.each(keys, function (i, item) {
                                    arrKeys.push($(item).val())
                                });

                                if (!window.confirm(`Будет списанно ${arrKeys.length} лимитов`)) {
                                    return false;
                                }

                                axios.post('/monitoring/parse/positions/project/keys', {
                                    projectId: PROJECT_ID,
                                    keys: arrKeys,
                                    region: region,
                                }).then(function (response) {
                                    if (response.data.status) {
                                        toastr.success(response.data.msg + " -" + response.data.count);
                                        keys.prop('checked', false);
                                    } else
                                        toastr.error(response.data.error);
                                });
                            });

                            container.find('.tooltip-on').tooltip({
                                animation: false,
                                trigger: 'hover',
                            });

                            container.find('.column-visible').click(function () {

                                let name = $(this).data('column');
                                let column = api.column(name + ':name');
                                let visible = column.visible();

                                column.visible(!visible);

                                $(`.column-visible[data-column="${name}"]`).toggleClass('hover', visible);

                                axios.post('/monitoring/project/set/column/settings', {
                                    monitoring_project_id: PROJECT_ID,
                                    name: name,
                                    state: !visible,
                                });
                            });

                            axios.post('/monitoring/project/get/column/settings', {monitoring_project_id: PROJECT_ID})
                                .then(function (response) {

                                    $.each(response.data, function (i, item) {
                                        let column = api.column(item.name + ':name');

                                        if (item.state === 0)
                                            column.visible(item.state);

                                        if (item.state)
                                            container.find(`.column-visible[data-column="${item.name}"]`).removeClass('hover');
                                        else
                                            container.find(`.column-visible[data-column="${item.name}"]`).addClass('hover');
                                    });
                                });
                        });

                        $('.search-button').click(function () {
                            let a = $(this);
                            let span = a.parent();
                            let b = span.find('b');
                            let input = span.find('input');

                            let toggleClass = 'd-none';

                            a.addClass(toggleClass);
                            b.addClass(toggleClass);

                            input.unbind("blur");

                            input.removeClass(toggleClass).focus().blur(function () {
                                $(this).addClass(toggleClass);
                                a.removeClass(toggleClass);
                                b.removeClass(toggleClass);
                            });
                        });

                        api.columns().every(function () {
                            let that = this;

                            $('input', this.header()).on('keyup change', function () {
                                if (that.search() !== this.value) {
                                    that.search(this.value).draw();
                                }
                            });
                        });

                        let filter = $('#filter');
                        filter.unbind('filtered');
                        filter.on('filtered', function (e, start, end) {

                            let form = $(this);

                            $.each(form.serializeArray(), function (i, item) {
                                let col = item.name;
                                let val = item.value;

                                console.log(col, val);

                                api.column(col + ':name').search(val).draw();
                            });
                        });

                        if (params.has('group')) {
                            setTimeout(() => api.column('group:name').search(params.get('group')).draw(), 1000);
                        }

                        let notValidateUrl = $('<div />', {
                            class: 'custom-control custom-switch'
                        }).css({
                            "margin-right": "2.25rem",
                        });

                        notValidateUrl.append($('<input />', {
                            type: "checkbox",
                            id: "notValidateUrl",
                            name: "url",
                            value: "1",
                            class: "custom-control-input",
                        }).click(function () {
                            let val = $(this).val();

                            if (val == "1")
                                $(this).val(0);
                            else
                                $(this).val(1);

                            api.column($(this).attr('name') + ':name').search(val).draw();
                        }));

                        notValidateUrl.append($('<label />', {
                            for: "notValidateUrl",
                            class: "custom-control-label",
                        }).text("Показать нецелевые URL"));

                        let dynamic = $('<div />', {
                            class: 'form-group'
                        }).css({
                            "margin-bottom": "0px",
                            "margin-right": "15px",
                        });

                        let dynamicOptions = [
                            {val: '', text: 'Динамика'},
                            {val: 'positive', text: 'Положительная'},
                            {val: 'negative', text: 'Отрицательная'},
                        ];

                        let dynamicSelect = $('<select />', {
                            class: 'custom-select',
                            name: 'dynamics'
                        });
                        $.each(dynamicOptions, function () {
                            dynamicSelect.append($("<option />").attr('value', this.val).text(this.text));
                        });

                        dynamicSelect.change(function () {
                            let self = $(this);
                            api.column(self.attr('name') + ':name').search(self.val()).draw();
                        });

                        dynamic.append(dynamicSelect);

                        if (response.data.region.length === 1) {
                            this.closest('.card').find('.card-header .card-title').after(dynamic);
                            this.closest('.card').find('.card-header .card-title').after(notValidateUrl);
                        }

                        this.closest('.card').find('.card-header label').css('margin-bottom', 0);
                        $('.dataTables_length').find('select').removeClass('custom-select-sm');
                        this.closest('.card').find('.card-header .card-title').addClass('flex-grow-1').text(PROJECT_NAME);
                    },
                    drawCallback: function () {
                        let api = this.api();
                        let data = api.data();
                        let card = table.closest('.card-table');
                        card.find('.processing').remove();
                        card.find('.dTable').css('display', 'block');

                        $('tbody > tr', table).each(function (i, item) {
                            let target = 0;
                            if ('target' in data[i]) {
                                target = $('<div />').html(data[i].target).text();
                            }
                            let positions = $(item).find('td span[data-position]');

                            $.each(positions, function (i, item) {
                                let current = $(item).data('position');
                                let nextTo = $(positions[i + 1]).data('position');

                                if (target >= current)
                                    $(item).closest('td').css('background-color', '#99e4b9');
                                else {
                                    if (target >= nextTo)
                                        $(item).closest('td').css('background-color', '#fbe1df');
                                }
                            });
                        });

                        $('.pagination').addClass('pagination-sm');

                        $('[data-toggle="popover"]').popover({
                            trigger: 'manual',
                            placement: 'right',
                            html: true,
                        }).on("mouseenter", function () {
                            $(this).popover("show");
                        }).on("mouseleave", function () {
                            let self = this;

                            let timeout = setTimeout(function () {
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

                $('.modal').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget);

                    let type = button.data('type');

                    let modal = $(this);

                    let request = null;

                    switch (type) {
                        case "edit_singular":

                            let id = button.data('id');

                            request = axios.get(`/monitoring/keywords/${id}/edit`).then(function (response) {

                                let content = response.data;

                                modal.find('.modal-content').html(content);
                            });
                            break;
                        case "edit_plural":

                            let checkboxes = $('.table tbody tr').find('input[type="checkbox"]:checked');

                            if (checkboxes.length) {

                                request = axios.get(`/monitoring/keywords/${PROJECT_ID}/edit-plural`).then(function (response) {

                                    let content = response.data;

                                    modal.find('.modal-content').html(content);
                                });

                            } else {
                                axios.get('/monitoring/keywords/empty/modal').then(function (response) {

                                    let content = response.data;

                                    modal.find('.modal-content').html(content);

                                    modal.find('h5').text('Выберите хотябы один элемент.');
                                    modal.find('p').text('Чтобы массово отредактировать элементы, нужно выбрать хотябы один элемент.');
                                });
                            }
                            break;
                        case "create_keywords":

                            request = axios.get(`/monitoring/keywords/${PROJECT_ID}/create`).then(function (response) {

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
                            });

                            break;
                    }

                    if (request) {
                        request.then(function () {

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

                                            toastr.success('Добавленно');

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

                                if (data.hasOwnProperty('query') && data.query.length < 1) {
                                    e.preventDefault();
                                    form.find('.invalid-feedback.query').fadeIn().delay(3000).fadeOut();
                                    return false;
                                }

                                let checkboxes = $('.table tbody tr').find('input[type="checkbox"]:checked');

                                if (checkboxes.length && method === 'POST') {
                                    $.extend(data, {id: []});
                                    $.each(checkboxes, function (i, checkbox) {
                                        data.id.push($(checkbox).val());
                                    });
                                }

                                axios({
                                    method: method,
                                    url: action,
                                    data: data
                                }).then(function (response) {

                                    dTable.draw(false);

                                    self.closest('.modal').modal('hide');
                                }).catch(function (error) {
                                    console.log(error);
                                });
                            });

                        });
                    }
                });
            });

            $('#reservation').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            let startDate = null;
            let endDate = null;
            if (DATES) {

                let dates = DATES.split(" - ");
                startDate = moment(dates[0]);
                endDate = moment(dates[1]);
            }

            let range = $('#date-range');
            range.daterangepicker({
                opens: 'left',
                startDate: startDate ?? moment().subtract(30, 'days'),
                endDate: endDate ?? moment(),
                ranges: {
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                locale: {
                    format: 'DD-MM-YYYY',
                    daysOfWeek: [
                        "Вс",
                        "Пн",
                        "Вт",
                        "Ср",
                        "Чт",
                        "Пт",
                        "Сб"
                    ],
                    monthNames: [
                        "Январь",
                        "Февраль",
                        "Март",
                        "Апрель",
                        "Май",
                        "Июнь",
                        "Июль",
                        "Август",
                        "Сентябрь",
                        "Октябрь",
                        "Ноябрь",
                        "Декабрь"
                    ],
                    firstDay: 1,
                }
            });

            range.on('apply.daterangepicker', function (ev, picker) {

                let dates = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');

                let url = new URL(window.location.href);
                let params = new URLSearchParams(url.search);

                params.set('dates', dates);

                let mode = picker.container.find('input[name="mode"]:checked', '.mode').val();

                params.set('mode', mode);

                window.location.search = params.toString();
            });

            range.on('show.daterangepicker', function (ev, picker) {
                //do something, like clearing an input
                let container = picker.container;

                if (container.find('.mode').length === 0) {

                    let ranges = $('<div />', {
                        class: "mode"
                    });
                    let ul = $('<ul />');

                    let settings = [
                        {id: 'range', name: 'Все дни', value: 'range', checked: true},
                        {id: 'datesFind', name: 'Две даты (фиксированные)', value: 'datesFind', checked: false},
                        {id: 'dates', name: 'Две даты (плавающие)', value: 'dates', checked: false},
                        {id: 'randWeek', name: 'Случайная дата 1 за неделю', value: 'randWeek', checked: false},
                        {id: 'randMonth', name: 'Случайная дата 1 за месяц', value: 'randMonth', checked: false},
                    ];

                    $.each(settings, function (i, item) {

                        let label = $('<label />', {class: "form-check-label", for: item.id}).text(item.name);
                        let radio = $('<input />', {
                            class: "form-check-input",
                            id: item.id,
                            type: "radio",
                            name: "mode",
                            value: item.value,
                            checked: item.checked
                        }).css('margin-top', 'auto');
                        let formCheck = $('<div />', {
                            class: "form-check"
                        });

                        ul.append($('<li />').html(formCheck.prepend(radio, label)));
                    });

                    if (MODE) {
                        ul.find('input[name="mode"]').prop('checked', false);
                        ul.find('input[value="' + MODE + '"]').prop('checked', true);
                    }

                    container.prepend(ranges.html(ul));
                }
            });

            range.on('updateCalendar.daterangepicker', function (ev, picker) {

                let container = picker.container;

                let leftCalendarEl = container.find('.drp-calendar.left tbody tr');
                let rightCalendarEl = container.find('.drp-calendar.right tbody tr');

                let leftCalendarData = picker.leftCalendar.calendar;
                let rightCalendarData = picker.rightCalendar.calendar;

                let showDates = [];

                for (let rows = 0; rows < leftCalendarData.length; rows++) {

                    let leftCalendarRowEl = $(leftCalendarEl[rows]);
                    $.each(leftCalendarData[rows], function (i, item) {

                        let leftCalendarDaysEl = $(leftCalendarRowEl.find('td').get(i));
                        if (!leftCalendarDaysEl.hasClass('off')) {

                            showDates.push({
                                date: item.format('YYYY-MM-DD'),
                                el: leftCalendarDaysEl,
                            });
                        }
                    });

                    let rightCalendarRowEl = $(rightCalendarEl[rows]);
                    $.each(rightCalendarData[rows], function (i, item) {

                        let rightCalendarDaysEl = $(rightCalendarRowEl.find('td').get(i));
                        if (!rightCalendarDaysEl.hasClass('off')) {

                            showDates.push({
                                date: item.format('YYYY-MM-DD'),
                                el: rightCalendarDaysEl,
                            });
                        }
                    });
                }

                axios.post('/monitoring/projects/get-positions-for-calendars', {
                    projectId: PROJECT_ID,
                    regionId: REGION_ID,
                    dates: showDates,
                }).then(function (response) {

                    $.each(response.data, function (i, item) {

                        let found = showDates.find(function (elem) {
                            if (elem.date === item.dateOnly)
                                return true;
                        });

                        if (!found.el.hasClass('exist-position'))
                            found.el.addClass('exist-position');
                    });
                }).catch(function (error) {

                    toastr.error('Something is going wrong');
                });
            });

            $('.table').on('click', '.delete-keyword', function () {
                let item = $(this);
                let id = item.data('id');

                if (window.confirm("{{__('Do you really want to delete?')}}")) {
                    axios.delete(`/monitoring/keywords/${id}`);
                    item.closest('tr').remove();
                }
            });

            let charts = {};
            if ($('#topPercent').length) {
                $.extend(charts, {
                    'top': {
                        el: $('#topPercent').get(0).getContext('2d'),
                        type: 'line',
                        chart: 'top',
                        options: {
                            title: {
                                display: true,
                                text: '% Ключевых слов в ТОП',
                                position: 'left',
                            },
                            maintainAspectRatio: false,
                            legend: {
                                display: true
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                    }
                                },
                                y: {
                                    ticks: {
                                        stepSize: 5
                                    }
                                }
                            },
                            plugins: {
                                crosshair: {
                                    sync: {
                                        enabled: false
                                    },
                                    snapping: {
                                        enabled: true,
                                    },
                                    zoom: {
                                        enabled: true,
                                        zoomButtonText: 'Reset',
                                        zoomButtonClass: 'reset-zoom btn btn-default btn-sm',
                                    },
                                    callbacks: {
                                        afterZoom: function () {
                                            charts.top.options.plugins.crosshair.zoom.enabled = false;
                                        }
                                    }
                                },
                                tooltip: {
                                    animation: false,
                                    mode: "index",
                                    intersect: false,
                                }
                            }
                        }
                    }
                });
            }

            if ($('#middlePosition').length) {
                $.extend(charts, {
                    'middle': {
                        el: $('#middlePosition').get(0).getContext('2d'),
                        type: 'line',
                        chart: 'middle',
                        options: {
                            title: {
                                display: true,
                                text: 'Средняя позиция',
                                position: 'left',
                            },
                            maintainAspectRatio: false,
                            legend: {
                                display: true
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                    }
                                },
                                y: {
                                    ticks: {
                                        stepSize: 5
                                    }
                                }
                            },
                            plugins: {
                                crosshair: {
                                    sync: {
                                        enabled: false
                                    },
                                    snapping: {
                                        enabled: true,
                                    },
                                    zoom: {
                                        enabled: true,
                                        zoomButtonText: 'Reset',
                                        zoomButtonClass: 'reset-zoom btn btn-default btn-sm',
                                    },
                                    callbacks: {
                                        afterZoom: function () {
                                            charts.middle.options.plugins.crosshair.zoom.enabled = false;
                                        }
                                    }
                                },
                                tooltip: {
                                    animation: false,
                                    mode: "index",
                                    intersect: false,
                                }
                            }
                        }
                    }
                });
            }

            if ($('#middlePositionRegions').length) {
                $.extend(charts, {
                    'regions_middle': {
                        el: $('#middlePositionRegions').get(0).getContext('2d'),
                        type: 'line',
                        chart: 'regions_middle',
                        options: {
                            title: {
                                display: true,
                                text: 'Средняя позиция',
                                position: 'left',
                            },
                            maintainAspectRatio: false,
                            legend: {
                                display: true
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                    }
                                },
                                y: {
                                    ticks: {
                                        stepSize: 5
                                    }
                                }
                            },
                            plugins: {
                                crosshair: {
                                    sync: {
                                        enabled: false
                                    },
                                    snapping: {
                                        enabled: true,
                                    },
                                    zoom: {
                                        enabled: true,
                                        zoomButtonText: 'Reset',
                                        zoomButtonClass: 'reset-zoom btn btn-default btn-sm',
                                    },
                                    callbacks: {
                                        afterZoom: function () {
                                            charts.middle.options.plugins.crosshair.zoom.enabled = false;
                                        }
                                    }
                                },
                                tooltip: {
                                    animation: false,
                                    mode: "index",
                                    intersect: false,
                                }
                            }
                        }
                    }
                });
            }

            let chartFilterPeriod = $('#chartFilterPeriod');

            $.each(charts, function (key, obj) {

                let chart = new Chart(obj.el, {
                    type: obj.type,
                    data: {},
                    options: obj.options
                });

                chartFilterPeriod.change(function () {
                    let range = $(this).val();
                    $('.progress-spinner').removeClass('d-none');

                    axios.get('/monitoring/charts', {
                        params: {
                            projectId: PROJECT_ID,
                            group: GROUP_ID,
                            regionId: REGION_ID,
                            dateRange: DATES,
                            range: range,
                            chart: obj.chart,
                        }
                    }).then(function (response) {
                        chart.data = response.data;
                        chart.update();

                        $('.progress-spinner').addClass('d-none');
                    });
                });
            });

            chartFilterPeriod.trigger('change');

            axios.get('/monitoring/charts', {
                params: {
                    projectId: PROJECT_ID,
                    group: GROUP_ID,
                    regionId: REGION_ID,
                    dateRange: DATES,
                    chart: 'distribution',
                }
            }).then(function (response) {

                if ($('#distributionByTop').length) {
                    new Chart($('#distributionByTop').get(0).getContext('2d'), {
                        type: 'doughnut',
                        data: response.data,
                        plugins: [ChartDataLabels],
                        options: {
                            maintainAspectRatio: false,
                            title: {
                                display: true,
                                text: 'Распределение по ТОП-100',
                                position: 'left',
                            },
                            plugins: {
                                crosshair: false,
                                datalabels: {
                                    anchor: 'center',
                                    color: '#fff',
                                    font: {
                                        size: 12,
                                        weight: 'bold',
                                    },
                                    formatter: (value, ctx) => {
                                        if (! value) {
                                            return null
                                        }

                                        return `${value}%`;
                                    },
                                },
                                legend: {
                                    position: 'left',
                                    labels: {
                                        font: {
                                            size: 24,
                                            style: "normal",
                                        },
                                        generateLabels: function (chart) {
                                            let data = chart.data;

                                            return data.labels.map(function (label, i) {
                                                let dsIndex = 0;
                                                let ds = data.datasets[0];

                                                let value = chart.config.data.datasets[dsIndex].data[i];

                                                return {
                                                    text: label + ": " + value + "%",
                                                    fillStyle: ds.backgroundColor[i],
                                                    strokeStyle: ds.backgroundColor[i],
                                                    hidden: ds.hidden,
                                                    index: i
                                                };
                                            });
                                        },
                                    },
                                },
                            }
                        }
                    });
                }
            });

            $('#showChartsBlock').click(function () {
                let btn = $(this);
                let charts = $('.card-charts');

                if (charts.hasClass('d-none')) {
                    charts.removeClass('d-none');
                    btn.text('Скрыть графики');
                } else {
                    charts.addClass('d-none');
                    btn.text('Показать графики');
                }

            });

            $('#occurrence-update').click(function () {
                let action = 'all';
                let YWCount = 3;

                axios.get(`/monitoring/${PROJECT_ID}/count`).then(function (response) {
                    let limits = (response.data.queries * response.data.region_yandex) * YWCount;

                    if (!limits || !window.confirm(`Будет списанно ${limits} лимитов`)) {
                        return false;
                    }

                    axios.post('/monitoring/occurrence', {
                        action: action,
                        id: PROJECT_ID,
                    }).then(function (response) {
                        if (response.data.status) {
                            toastr.success(response.data.msg + " -" + response.data.count);
                        } else
                            toastr.error(response.data.error);
                    });
                });
            });
        </script>
    @endslot

@endcomponent
