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
            .table tr td:nth-child(4) {
               text-align: left;
            }
            .dataTables_processing {
                margin: 10px auto;
                z-index: 4;
            }
            .exist-position {
                color: #28a745!important;
                font-weight: bold;
            }
            .popover {
                max-width: none;
            }
            .progress-spinner{
                position: absolute;
                top: 20%;
                width: 100%;
                text-align: center;
                z-index: 1;
            }
            .reset-zoom {
                position: absolute;
                top: 50px;
                right: 30px;
            }

        </style>
    @endslot

    <div class="row">
        @foreach($navigations as $navigation)
        <div class="col-lg-2 col-6">
            <a href="{{ $navigation['href'] }}" class="small-box {{ $navigation['bg'] }}" style="min-height: 137px">
                <div class="inner">
                    <h3>{{ $navigation['h3'] }}</h3>
                    <p>{{ $navigation['p'] }}</p>
                    <small>{{ $navigation['small'] }}</small>
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

    {{-- @include('monitoring.testing') --}}

    @include('monitoring.keywords.modal.main')

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
            const PROJECT_ID = '{{ $project->id }}';
            const REGION_ID = '{{ request('region', null) }}';
            const DATES = '{{ request('dates', null) }}';
            const MODE = '{{ request('mode', null) }}';
            const PAGE_LENGTH = '{{ $length }}';
            const LENGTH_MENU = JSON.parse('{{ $lengthMenu }}');
            const MAIN_COLUMNS_COUNT = 8;

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

                    columns.push({
                        'title': item,
                        'name': i,
                        'data': i,
                    });
                });

                let dTable = table.DataTable({
                    dom: '<"card-header"<"card-title"><"float-right"l>><"card-body p-0"<"mailbox-controls">rt<"mailbox-controls">><"card-footer clearfix"p><"clear">',
                    scrollX: true,
                    lengthMenu: LENGTH_MENU,
                    pageLength: PAGE_LENGTH,
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
                    columnDefs: [
                        { orderable: true, className: 'reorder', targets: 0 },
                        { orderable: true, className: 'reorder', targets: 3 },
                        { orderable: false, targets: '_all' },
                        { "width": "350px", "targets": 3 },
                    ],
                    initComplete: function(){
                        let api = this.api();

                        axios.get(`/monitoring/keywords/show/controls`).then(function (response) {

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
                                if(checkboxes.length){

                                    if (window.confirm("Do you really want to delete?")) {

                                        $.each(checkboxes, function (i, checkbox) {
                                            let id = $(checkbox).val();

                                            axios.delete(`/monitoring/keywords/${id}`);
                                        });

                                        window.location.reload();
                                    }
                                }else{
                                    toastr.error('Выберите хотя бы один элемент.');
                                }
                            });

                            let parsePositions = container.find('.parse-positions');
                            parsePositions.click(function () {

                                if (window.confirm("Вы собираетесь добавить в очередь все запросы, подтвердите ваше действие")) {

                                    axios.post('/monitoring/parse/positions/project', {
                                        projectId: PROJECT_ID,
                                    }).then(function () {
                                        toastr.success('Задание добавленно в очередь.');
                                    });
                                }
                            });

                            let parsePositionsKeys = container.find('.parse-positions-keys');
                            parsePositionsKeys.click(function () {

                                let arrKeys = [];
                                let keys = $('.table tbody tr').find('input[type="checkbox"]:checked');

                                $.each(keys, function (i, item) {
                                    arrKeys.push($(item).val())
                                });

                                axios.post('/monitoring/parse/positions/project/keys', {
                                    projectId: PROJECT_ID,
                                    keys: arrKeys,
                                }).then(function (response) {
                                    toastr.success('Задание добавленно в очередь.');
                                });
                            });

                            container.find('.tooltip-on').tooltip();
                        });

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

                        let filter = $('#filter');
                        filter.unbind('filtered');
                        filter.on('filtered', function(e, start, end){

                            let form = $(this);

                            $.each(form.serializeArray(), function (i, item) {
                                let col = item.name;
                                let val = item.value;

                                console.log(col, val);

                                api.column(col + ':name').search(val).draw();
                            });
                        });

                        let notValidateUrl = $('<div />', {
                            class: 'custom-control custom-switch'
                        }).css({
                            float: "left",
                            "margin-left": "2.25rem",
                            "margin-top": "6px",
                        });

                        notValidateUrl.append($('<input />', {
                            type: "checkbox",
                            id: "notValidateUrl",
                            name: "url",
                            value: "1",
                            class: "custom-control-input",
                        }).click(function () {
                            let val = $(this).val();

                            if(val == "1")
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
                            float: "left",
                            "margin-left": "2.25rem",
                            "margin-bottom": "0px",
                        });

                        let dynamicOptions = [
                            {val : '', text: 'Динамика'},
                            {val : 'positive', text: 'Положительная'},
                            {val : 'negative', text: 'Отрицательная'},
                        ];

                        let dynamicSelect = $('<select />', {
                           class: 'custom-select',
                            name: 'dynamics'
                        });
                        $.each(dynamicOptions, function() {
                            dynamicSelect.append($("<option />").attr('value', this.val).text(this.text));
                        });

                        dynamicSelect.change(function () {
                            let self = $(this);
                            api.column(self.attr('name') + ':name').search(self.val()).draw();
                        });

                        dynamic.append(dynamicSelect);

                        let btnGroup = $('<div />', {
                            class: "btn-group"
                        });

                        for(let i = 0; i < MAIN_COLUMNS_COUNT; i++){

                            let column = columns[i];

                            if($(column.title).length)
                                column.title = $(column.title).text();

                            if(column.title){

                                let button = $('<button />', {
                                    class: "btn btn-default",
                                    type: "type",
                                    "data-column": column.name + ":name",
                                });
                                btnGroup.append(button.text(column.title));
                            }
                        }

                        btnGroup.find('.btn').click(function(){
                            let self = $(this);

                            let name = self.attr('data-column');

                            let column = api.column(name);

                            axios.post('/monitoring/project/set/column/settings', {
                                monitoring_project_id: PROJECT_ID,
                                name: name,
                                state: !column.visible(),
                            });

                            self.toggleClass('hover', column.visible());
                            column.visible(!column.visible());
                        });

                        axios.post('/monitoring/project/get/column/settings', {
                            monitoring_project_id: PROJECT_ID
                        }).then(function(response){
                            if(response.data.length){

                                $.each(response.data, function(i, item){

                                    if(item.state)
                                        return true;

                                    let column = api.column(item.name);

                                    btnGroup.find(`.btn[data-column="${item.name}"]`).toggleClass('hover', !item.state);
                                    column.visible(item.state);
                                });
                            }
                        });

                        if(!response.data.region.length){
                            this.closest('.card').find('.card-header').append(notValidateUrl);
                            this.closest('.card').find('.card-header').append(dynamic);
                        }
                        this.closest('.card').find('.card-header .card-title').html(btnGroup);
                        this.closest('.card').find('.card-header .card-title').prepend($('<h3 />', {class: "card-title"}).css({"line-height": '38px', "margin-right": '10px'}).text("Скрыть колонки:"));

                        this.closest('.card').find('.card-header label').css('margin-bottom', 0);
                        $('.dataTables_length').find('select').removeClass('custom-select-sm');
                    },
                    drawCallback: function(){
                        let api = this.api();

                        let card = table.closest('.card-table');
                        card.find('.processing').remove();
                        card.find('.dTable').css('display', 'block');

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

                            if(checkboxes.length){

                                request = axios.get(`/monitoring/keywords/${PROJECT_ID}/edit-plural`).then(function (response) {

                                    let content = response.data;

                                    modal.find('.modal-content').html(content);
                                });

                            }else{
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

                                    if(csv[0].files.length && csv[0].files[0].type === 'text/csv'){

                                        csv.parse({
                                            config: {
                                                skipEmptyLines: 'greedy',
                                                complete: function (result) {

                                                    let value = '';
                                                    $.each(result.data, function(i, item){

                                                        if(item[0])
                                                            value += item[0] + '\r\n';
                                                    });

                                                    modal.find('textarea[name="query"]').val(value);
                                                },
                                                download: 0
                                            }
                                        });

                                    }else{

                                        toastr.error('Загрузите файл формата .csv');
                                    }
                                });
                            });

                            break;
                    }

                    if(request){

                        request.then(function () {

                            let group = modal.find('.custom-select[name="monitoring_group_id"]');
                            if(group.length){

                                group.select2({
                                    theme: 'bootstrap4'
                                });

                                modal.find('#create-group').click(function(){
                                    let el = $(this);
                                    let input = el.closest('.input-group').find('input');

                                    if(input.val()){

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

                            modal.find('.save-modal').click(function () {
                                let self = $(this);
                                let form = self.closest('.modal-content').find('form');
                                let action = form.attr('action');
                                let method = form.attr('method');
                                let data = {};

                                $.each(form.serializeArray(), function (inc, item) {
                                    $.extend( data, {[item.name]: item.value} );
                                });

                                let checkboxes = $('.table tbody tr').find('input[type="checkbox"]:checked');

                                if(checkboxes.length && method === 'POST'){
                                    $.extend( data, {id: []} );
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
            if(DATES){

                let dates = DATES.split(" - ");
                startDate = moment(dates[0]);
                endDate = moment(dates[1]);
            }

            let range = $('#date-range');
            range.daterangepicker({
                opens: 'left',
                startDate: startDate ?? moment().subtract(30, 'days'),
                endDate  : endDate ?? moment(),
                ranges   : {
                    'Последние 7 дней' : [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Прошлый месяц'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
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

            range.on('apply.daterangepicker', function(ev, picker) {

                let dates = picker.startDate.format('YYYY-MM-DD')+ ' - ' + picker.endDate.format('YYYY-MM-DD');

                let url = new URL(window.location.href);
                let params = new URLSearchParams(url.search);

                params.set('dates', dates);

                let mode = picker.container.find('input[name="mode"]:checked', '.mode').val();

                params.set('mode', mode);

                window.location.search = params.toString();
            });

            range.on('show.daterangepicker', function(ev, picker) {
                //do something, like clearing an input
                let container = picker.container;

                if(container.find('.mode').length === 0){

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
                        let radio = $('<input />', {class: "form-check-input",id: item.id, type: "radio", name: "mode", value: item.value, checked: item.checked}).css('margin-top', 'auto');
                        let formCheck = $('<div />', {
                            class: "form-check"
                        });

                        ul.append($('<li />').html(formCheck.prepend(radio, label)));
                    });

                    if(MODE){
                        ul.find('input[name="mode"]').prop('checked', false);
                        ul.find('input[value="'+ MODE +'"]').prop('checked', true);
                    }

                    container.prepend(ranges.html(ul));
                }
            });

            range.on('updateCalendar.daterangepicker', function(ev, picker) {

                let container = picker.container;

                let leftCalendarEl = container.find('.drp-calendar.left tbody tr');
                let rightCalendarEl = container.find('.drp-calendar.right tbody tr');

                let leftCalendarData = picker.leftCalendar.calendar;
                let rightCalendarData = picker.rightCalendar.calendar;

                let showDates= [];

                for(let rows = 0; rows < leftCalendarData.length; rows++){

                    let leftCalendarRowEl = $(leftCalendarEl[rows]);
                    $.each(leftCalendarData[rows], function(i, item){

                        let leftCalendarDaysEl = $(leftCalendarRowEl.find('td').get(i));
                        if(!leftCalendarDaysEl.hasClass('off')){

                            showDates.push({
                                date: item.format('YYYY-MM-DD'),
                                el: leftCalendarDaysEl,
                            });
                        }
                    });

                    let rightCalendarRowEl = $(rightCalendarEl[rows]);
                    $.each(rightCalendarData[rows], function(i, item){

                        let rightCalendarDaysEl = $(rightCalendarRowEl.find('td').get(i));
                        if(!rightCalendarDaysEl.hasClass('off')){

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

                    $.each(response.data, function(i, item){

                        let found = showDates.find(function (elem) {
                            if(elem.date === item.dateOnly)
                                return true;
                        });

                        if(!found.el.hasClass('exist-position'))
                            found.el.addClass('exist-position');
                    });
                }).catch(function (error) {

                    toastr.error('Something is going wrong');
                });
            });

            $('.table').on('click', '.delete-keyword' ,function () {
                let item = $(this);
                let id = item.data('id');

                if (window.confirm("{{__('Do you really want to delete?')}}")) {
                    axios.delete(`/monitoring/keywords/${id}`);
                    item.closest('tr').remove();
                }
            });

            let charts = {
                'top' : {
                    el: $('#topPercent').get(0).getContext('2d'),
                    type: 'line',
                    options: {
                        title: {
                            display: true,
                            text: '% Ключевых слов в ТОП',
                            position: 'left',
                        },
                        maintainAspectRatio : false,
                        legend: {
                            display: true
                        },
                        scales: {
                            x: {
                                grid : {
                                    display : false,
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
                                    afterZoom: function() {
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
                },
                'middle' : {
                    el: $('#middlePosition').get(0).getContext('2d'),
                    type: 'line',
                    options: {
                        title: {
                            display: true,
                            text: 'Средняя позиция',
                            position: 'left',
                        },
                        maintainAspectRatio : false,
                        legend: {
                            display: true
                        },
                        scales: {
                            x: {
                                grid : {
                                    display : false,
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
                                    afterZoom: function() {
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
                },
            };

            let chartFilterPeriod = $('#chartFilterPeriod');

            $.each(charts, function(key, obj){

                let chart = new Chart(obj.el, {
                    type: obj.type,
                    data: {},
                    options: obj.options
                });

                chartFilterPeriod.change(function() {
                    let range = $(this).val();
                    $('.progress-spinner').removeClass('d-none');

                    axios.get('/monitoring/charts', {
                        params: {
                            projectId: PROJECT_ID,
                            dateRange: DATES,
                            range: range,
                            chart: key,
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
                    dateRange: DATES,
                    chart: 'distribution',
                }
            }).then(function (response) {

                new Chart($('#distributionByTop').get(0).getContext('2d'), {
                    type: 'doughnut',
                    data: response.data,
                    plugins: [ChartDataLabels],
                    options: {
                        maintainAspectRatio : false,
                        title: {
                            display: true,
                            text: 'Распределение по ТОП-100',
                            position: 'left',
                        },
                        plugins: {
                            crosshair: false,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label;
                                        let data = context.dataset.data;
                                        let dataItem = data[context.dataIndex];

                                        let sum = 0;
                                        data.map(data => { sum += data });
                                        let percent = Math.round((dataItem * 100 / sum));

                                        label += ': ' + dataItem + ' (' + percent + '%)';

                                        return label;
                                    }
                                }
                            },
                            datalabels: {
                                anchor: 'center',
                                color: '#fff',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                formatter: (value, ctx) => {
                                    let sum = 0;
                                    let dataArr = ctx.chart.data.datasets[0].data;
                                    dataArr.map(data => { sum += data });
                                    let percent = Math.round((value * 100 / sum));
                                    if(percent > 1)
                                        return `${percent}%`;
                                    else
                                        return null;
                                },
                            },
                            legend: {
                                position: 'left',
                                labels: {
                                    font: {
                                        size: 24,
                                        style: "normal",
                                    },
                                    generateLabels: function(chart){
                                        let data = chart.data;

                                        return data.labels.map(function(label, i) {
                                            let dsIndex = 0;
                                            let ds = data.datasets[0];

                                            let sum = 0;
                                            ds.data.map(data => { sum += data });

                                            let value = chart.config.data.datasets[dsIndex].data[i];
                                            let percent = Math.round((value * 100 / sum));

                                            return {
                                                text: label + ": " + percent + "%",
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
            });


            $('#showChartsBlock').click(function () {
                let btn = $(this);
                let charts = $('.card-charts');

                if(charts.hasClass('d-none')) {
                    charts.removeClass('d-none');
                    btn.text('Скрыть графики');
                }else {
                    charts.addClass('d-none');
                    btn.text('Показать графики');
                }

            });
        </script>
    @endslot


@endcomponent
