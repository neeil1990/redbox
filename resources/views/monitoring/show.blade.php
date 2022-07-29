@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

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
            .dataTables_processing {
                margin: 10px auto;
                z-index: 4;
            }
            .dTable {
                display: none;
            }
            .exist-position {
                color: #28a745!important;
                font-weight: bold;
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

    @include('monitoring.partials.show.filter')

    <div class="row">
        <div class="col-12 alert-data"></div>
    </div>

    <div class="row">
        <div class="col-12 card-table">
            <div class="card processing">
                <div class="dataTables_processing"><img src="/img/1485.gif" style="width: 50px; height: 50px;"></div>
            </div>
            <div class="card dTable">
                <table class="table table-responsive table-bordered table-hover text-center"></table>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <h5 class="mb-2 mt-4">Testing</h5>

    {!! Form::open(['route' => ['keywords.set.test.positions', $project->id], 'method' => 'patch']) !!}

        <input type="hidden" name="search" value="{{ request('region', $project->searchengines[0]->id) }}">

        <div class="form-group">
        <label>[Year-month-day] Date range:</label>
        <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">
                  <i class="far fa-calendar-alt"></i>
              </span>
            </div>

            <input type="text" name="date" class="form-control float-right" id="reservation">

            <span class="input-group-append">
                <button type="submit" class="btn btn-info btn-flat">Вставить позиции.</button>
            </span>
        </div>
        <!-- /.input group -->
    </div>
    {!! Form::close() !!}

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

        <script>

            const PROJECT_ID = '{{ $project->id }}';
            const REGION_ID = '{{ request('region', null) }}';
            const DATES = '{{ request('dates', null) }}';
            const MODE = '{{ request('mode', null) }}';
            const PAGE_LENGTH = 5;

            let table = $('.table');

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            axios.post(`/monitoring/${PROJECT_ID}/table`, {
                length: PAGE_LENGTH,
                region_id: REGION_ID,
                dates_range: DATES,
                mode_range: MODE,
            }).then(function (response) {

                let Tablewidth = Object.keys(response.data.columns).length;

                if(Tablewidth <= 7){
                    $('.alert-data').append($('<div />', {
                        class: "callout callout-danger"
                    }).html($('<h5 />', { class: "mb-0" }).text('За данный период позиций нет')));
                }

                let region = response.data.region;
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
                        //{ orderable: true, className: 'reorder', targets: 3 },
                        //{ orderable: false, targets: '_all' },
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

                        this.closest('.card').find('.card-header .card-title').html("");
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
                //minDate: moment().subtract(90, 'days'),
                ranges   : {
                    'Последние 7 дней' : [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Прошлый месяц'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    //'Все дни'  : [moment().subtract(89, 'days'), moment()],
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
                },
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

                if(container.find('.ranges').length < 2){

                    let ranges = $('<div />', {
                        class: "ranges mode"
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

            range.on('showCalendar.daterangepicker', function(ev, picker) {

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

                if (window.confirm("Do you really want to delete?")) {

                    axios.delete(`/monitoring/keywords/${id}`);

                    item.closest('tr').remove();
                }
            });
        </script>
    @endslot


@endcomponent
