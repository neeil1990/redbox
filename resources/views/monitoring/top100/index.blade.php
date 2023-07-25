@component('component.card', ['title' => __('Top of the project') . " $project->name"])

    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

        <style>
            .kanban-item {
                margin-bottom: 0;
                min-height: 60px;
                max-height: 60px;
                justify-content: center;
                align-items: center;
            }

            .fixed-lines {
                float: left;
                word-wrap: break-word;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                cursor: pointer;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                height: 100%;
            }

            .fixed-lines:hover {
                box-shadow: 0 0 6px grey;
            }

            .site-position {
                float: left;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
                width: 26px;
            }

            .dropdown.show {

            }

            .kanban-card {
                min-width: 400px;
                max-width: 400px;
            }

            .hide-element {
                display: none !important;
            }

            .connection {
                position: absolute;
                display: block;
                background: black;
                width: 2px;
                height: 2px;
            }

            #result {
                position: relative;
            }

            .dropdown-item a {
                color: black !important;
            }

            .dropdown-item {
                cursor: pointer;
            }

            .select2-selection.select2-selection--single {
                height: 42px;
            }

            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }

            .color-domain {
                background-color: #a8cae9;
            }

        </style>
    @endslot

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message success-message"></div>
        </div>
    </div>

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
                            <small>{{ $navigation['small'] }}</small>
                        @endisset
                    </div>
                    <div class="icon">
                        <i class="{{ $navigation['icon'] }}"></i>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Analysis Settings') }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="form-group col-4">
                    <label for="words-select">{{ __('Phrase') }}</label>
                    <select class="form form-control" id="words-select" size="10" name="words-select">
                        @foreach($project->keywords as $keyword)
                            <option value="{{ $keyword->query }}">{{ $keyword->query }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label>{{ __('Date range') }}:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" id="date-range" class="form-control float-right">
                    </div>
                </div>
                <div class="form-group col-4">
                    <label for="region">{{ __('Region') }}</label>
                    <br>
                    <div class="btn-group w-100">
                        <select name="region" class="custom-select" id="searchEngines">
                            @if($project->searchengines->count() > 1)
                                <option value="">{{ __('All search engine and regions') }}</option>
                            @endif

                            @foreach($project->searchengines as $search)
                                @if($search->id == request('region'))
                                    <option value="{{ $search->lr }}"
                                            selected>{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                        [{{$search->lr}}]
                                    </option>
                                @else
                                    <option
                                        value="{{ $search->lr }}">{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                        [{{$search->lr}}]
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <button class="btn btn-secondary" id="analyse">{{ __('Analyse') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-4">
                    <label for="top">{{ __('The maximum value of the top') }}</label>
                    <select name="top" id="top" class="custom-select">
                        <option value="100">100</option>
                        <option value="50">50</option>
                        <option value="30">30</option>
                        <option value="20">20</option>
                        <option value="10">10</option>
                    </select>
                </div>
                <div class="d-flex flex-column col-4">
                    <label>{{ __('Display') }}</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <button class="btn btn-secondary active change-filter-name" data-action="URL">
                            <input type="radio" name="options" autocomplete="off" checked="">
                            URL
                        </button>
                        <button class="btn btn-secondary change-filter-name" data-action="домену">
                            <input type="radio" name="options" autocomplete="off">
                            {{ __('Domain') }}
                        </button>
                    </div>
                </div>
                <div class="col-4">
                    <label for="filter">{{ __('Filter by') }}<span id="filter-target">URL</span></label>
                    <input type="text" id="filter" name="filter" class="form form-control" disabled>
                </div>
            </div>
            <div class="row">
                <div class="col-3 mt-4">
                    <button class="btn btn-outline-secondary"
                            data-action="color"
                            id="select-my-project"
                            data-target="{{ $project->url }}" disabled>{{ __('Select the project domain') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group ml-3 mt-3">
        <div id="progress" style="display: none">
            <img src="/img/1485.gif" style="width: 50px; height: 50px;">
            <br>
            {{ __('Analyzed') }} <span id="analysed-days">0</span> из <span
                id="total-days">0</span> {{ __('selected dates') }}
        </div>
    </div>

    <div style="overflow-x: auto; width: 100%" class="d-flex">
        <div class="d-flex mt-3" style="display: flex; min-width: 100%" id="result"></div>
    </div>

    @slot('js')
        <!-- InputMask -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('/plugins/select2/js/select2.min.js') }}"></script>
        <script>
            const COLORS = [
                "rgba(151, 186, 229, 1)",
                "rgba(214, 2, 86, 1)",
                "rgba(0, 69, 255, 0.6)",
                "rgba(239, 50, 223, 0.6)",
                "rgba(6, 136, 165, 0.6)",
                "rgba(214, 96, 110, 1)",
                "rgba(246, 223, 78, 1)",
                "rgba(220, 51, 10, 0.6)",
                "rgba(1, 253, 215, 1)",
                "rgba(1, 79, 66, 0.6)",
                "rgba(204, 118, 32, 0.6)",
                "rgba(255, 89, 0, 1)",
                "rgba(73, 28, 1, 0.6)",
                "rgba(154, 205, 50, 1)",
                "rgba(121, 25, 6, 1)",
                "rgb(17, 255, 0)",
                "rgba(214, 2, 86, 0.6)",
                "rgba(19,212,224, 1)",
                "rgba(239, 50, 223, 1)",
                "rgba(255,89,0,0.6)",
                "rgba(244, 139, 200, 1)",
                "rgba(87, 64, 64, 0.6)",
                "rgba(163, 209, 234, 0.6)",
                "rgba(232,194,90,0.6)",
                "rgba(252, 194, 243, 1)",
            ]
            $('#words-select').select2();
            let range = $('#date-range');
            range.daterangepicker({
                opens: 'left',
                startDate: moment().subtract(3, 'days'),
                endDate: moment(),
                ranges: {
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Последние 60 дней': [moment().subtract(59, 'days'), moment()],
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
                    projectId: {{ $project->id }},
                    regionId: null,
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
                })
            });

            $('#competitors-history-positions').unbind().on('click', function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('monitoring.competitors.history.positions') }}",
                    data: {
                        'projectId': PROJECT_ID,
                        'region': $('#searchEngines').val(),
                        'dateRange': $('#date-range').val(),
                    },
                    success: function (response) {
                        $('#empty-row').remove()
                        $('#changeDatesTbody').append(
                            '<tr id="analyse-in-queue-' + response.analyseId + '">' +
                            '   <td>' + $('#date-range').val() + '</td>' +
                            '   <td>' + String($('#searchEngines option:selected').text()).trim() + '</td>' +
                            '   <td class="text-center">' + "{{ __('In queue') }}" + ' <img src="/img/1485.gif" style="width: 20px; height: 20px;"></td>' +
                            '</tr>')
                        waitFinishAnalyse(response.analyseId)
                    },
                })
            })

            let activeFilter = 'URL'

            $('#analyse').on('click', function () {
                disableElements()
                let words = [$('#words-select').val()];

                if (words.length === 0) {
                    errorMessage('Выберите фразу')
                    enableElements()
                } else {
                    $('#result').html('')
                    $('#filter').val('')

                    $('#progress').show(300)
                    let days = getDates()
                    $('#total-days').html(days.length)
                    startAnalyse(words, days)
                }
            })

            function getDates() {
                let days = $('#date-range').val().split(' - ')
                let startDate = moment(days[0], "DD-MM-YYYY");
                let endDate = moment(days[1], "DD-MM-YYYY");
                let allDates = [];

                let currentDate = startDate;

                while (currentDate.isSameOrBefore(endDate)) {
                    allDates.push(currentDate.format("DD-MM-YYYY"));
                    currentDate.add(1, "days");
                }

                return allDates;
            }

            async function startAnalyse(words, dates) {
                for (const word of words) {
                    await processWordAndDates(word, dates);
                }
                setTimeout(() => {
                    $('#progress').hide(300)
                    enableElements()
                }, 2000)

                $(function () {
                    $('[data-toggle="tooltip"]').tooltip()
                })

                $('.copy').unbind().on('click', function () {
                    $(this).attr('data-target')
                    const tempInput = document.createElement('input');
                    tempInput.value = $(this).attr('data-target');
                    document.body.appendChild(tempInput);

                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);

                    $('.toast-top-right.success-message').show(300)

                    $('.toast-message.success-message').html('Скопированно в буфер обмена')
                    setTimeout(() => {
                        $('.toast-top-right.success-message').hide(300)
                    }, 3000)
                })
                setRelationShips()
                setRelationShipsFromLink()
            }

            async function processWordAndDates(word, dates) {
                let counter = 1;
                for (const date of dates) {
                    await sendAjaxRequest(word, date);
                    $('#analysed-days').html(counter)
                    counter++
                }

                maxTop()
                changeVisual()
                filter()
            }

            function sendAjaxRequest(word, date) {
                let currentDate = moment(date, "DD-MM-YYYY").format("YYYY-MM-DD");

                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: "{{ route('monitoring.get.top.sites') }}",
                        type: "POST",
                        data: {
                            word: word,
                            date: currentDate,
                            region: $('#searchEngines').val()
                        },
                        success: function (response) {
                            if (response.length === 100) {
                                $('#result').append(generateKanbanCard(response, date))
                            }
                            resolve();
                        },
                        error: function (error) {
                            console.error("Ошибка при выполнении запроса:", error);
                            resolve();
                        }
                    });
                });
            }

            function generateKanbanCard(items, date) {
                let kanbanItems = ''

                $.each(items.reverse(), function (k, v) {
                    let url
                    if (activeFilter === 'URL') {
                        url = v.url
                    } else {
                        url = new URL(v.url)['origin']
                    }

                    let top = $('#top').val()
                    let hide = ''
                    if (v.position > top) {
                        hide = 'hide-element'
                    }

                    kanbanItems +=
                        '<div class="kanban-item w-100 border-bottom ' + hide + '" data-index="' + v.position + '" data-toggle="tooltip" data-placement="top" title="' + v.url + '">' +
                        '    <div class="site-position">' + v.position + ' </div>' +
                        '    <div class="col-10 fixed-lines" data-url="' + v.url + '" data-domain="' + new URL(v.url)['origin'] + '">' + url + ' </div>' +
                        '    <div class="dropdown show" style="float:left">' +
                        '        <i id="dropdownMenuButton" data-toggle="dropdown" aria-expanded="true" class="fa fa-cogs mt-3 ml-2" style="opacity: 0.6; cursor: pointer;"></i>' +
                        '        <div aria-labelledby="dropdownMenuButton" class="dropdown-menu hide" style="position: absolute; transform: translate3d(0px, 18px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">' +
                        '            <span class="dropdown-item" style="cursor: pointer;">' +
                        '                <a href="' + v.url + '" target="_blank">Перейти на сайт</a>' +
                        '            </span> ' +
                        '            <span class="dropdown-item" style="cursor: pointer;">' +
                        '                <a href="/redirect-to-text-analyzer/' + v.url.replaceAll('/', 'abc') + '" target="_blank">{{ __('Analyse') }}</a>' +
                        '            </span> ' +
                        '            <span class="dropdown-item copy" style="cursor: pointer;" data-target="' + v.url + '">' +
                        '                <span>{{ __('Copy URL') }}</span>' +
                        '            </span> ' +
                        '            <span class="dropdown-item copy" style="cursor: pointer;"  data-target="' + new URL(v.url)['origin'] + '">' +
                        '                <a>{{ __('Copy domain') }}</a>' +
                        '            </span> ' +
                        '            <span class="dropdown-item set-relationships" style="cursor: pointer;" data-target="' + v.url + '">' +
                        '                {{ __('View positions') }}' +
                        '            </span> ' +
                        '    </div>' +
                        '</div>' +
                        '</div>'
                })

                return '<div class="card card-row card-secondary kanban-card mr-5 border">' +
                    '    <div class="card-header pl-2 w-100">' +
                    '        <span class="col-2">#</span>' +
                    '        <span class="col-10">' + date + '</span>' +
                    '    </div>' +
                    kanbanItems +
                    '</div>'
            }

            function randomInteger(min, max) {
                let rand = min + Math.random() * (max + 1 - min);
                return Math.floor(rand);
            }

            function drawConnect(from, to, color, id, extra = false) {

                function createConnection() {
                    return $("<div />")
                        .addClass('connection ' + id)
                        .css('background', color);
                }

                let $from = $(from)
                    , $to = $(to)
                    , $main = $("#result");

                let mainTop = $main.offset().top  //Расстояние сверху от контейнера
                    , mainLeft = $main.offset().left //Расстояние сбоку от контейнера
                    , mainHeight = $main.outerHeight() //Высота контейнера
                    , fromLeft = $from.offset().left + $from.outerWidth() - mainLeft //Точка ИЗ (сбоку)
                    , toLeft = $to.offset().left - mainLeft //Точка В (сбоку)
                    , fromTop = ($from.offset().top + $from.outerHeight() / 2 - mainTop) - 20 //Точка ИЗ (сверху)
                    , toTop = $to.offset().top + $to.outerHeight() / 2 - mainTop //Точка В (сверху)
                    , width = toLeft - fromLeft
                    , height = toTop - fromTop;

                let position = $from.children('div').eq(0).html().trim() - $to.children('div').eq(0).html().trim()
                if (position === 0) {
                    position = ''
                } else if (position >= 1) {
                    position = '+' + position
                } else {
                    position = '' + position
                }

                if (extra) {
                    fromTop += 30
                }

                let w1 = Math.round(Math.abs(width / 2)),
                    w2 = width - w1;

                createConnection()
                    .css('left', fromLeft + 'px')
                    .css('top', fromTop + 'px')
                    .css('width', w1 + 'px')
                    .html(position)
                    .appendTo($main);

                let $c = createConnection()
                    .css('left', fromLeft + w1 + 'px')
                    .css('height', Math.abs(height))
                    .appendTo($main);

                if (height === 0) {
                    $c.css('top', fromTop + "px");
                } else if (height >= 0) {
                    $c.css('top', fromTop + "px");
                } else {
                    $c.css('bottom', mainHeight - fromTop - 2 + "px");
                }

                $c.appendTo($main);

                createConnection()
                    .css('left', fromLeft + w1 + 'px')
                    .css('top', fromTop + height + 'px')
                    .css('width', w2)
                    .appendTo($main);

                return id;
            }

            function getRandomColor() {
                let colorArray = [
                    "rgba(220, 51, 10, 0.6)",
                    "rgba(148,67,49,0.6)",
                    "rgba(121, 25, 6, 1)",
                    "rgb(169,112,99)",
                    "rgb(148,127,131)",
                    "rgba(214, 2, 86, 0.6)",
                    "rgba(214, 2, 86, 1)",
                    "rgba(204, 118, 32, 0.6)",
                    "rgba(255,89,0,0.6)",
                    "rgba(255, 89, 0, 1)",
                    "rgba(73, 28, 1, 0.6)",
                    "rgba(246, 223, 78, 1)",
                    "rgb(243,211,27)",
                    "rgb(100,84,0)",
                    "rgba(1, 253, 215, 1)",
                    "rgba(1, 79, 66, 0.6)",
                    "rgba(154, 205, 50, 1)",
                    "rgb(17, 255, 0)",
                    "rgb(150,252,141)",
                    "rgb(10,103,3)",
                    "rgba(151, 186, 229, 1)",
                    "rgba(0, 69, 255, 0.6)",
                    "rgba(6, 136, 165, 0.6)",
                    "rgba(19,212,224, 1)",
                    "rgba(239, 50, 223, 0.6)",
                    "rgba(239, 50, 223, 1)",
                    "rgba(252, 194, 243, 1)",
                    "rgba(244, 139, 200, 1)",
                    "rgba(87, 64, 64, 0.6)",
                    "rgba(163, 209, 234, 0.6)",
                    "rgba(232,194,90,0.6)",
                ]

                return colorArray.sort(() => Math.random() - 0.5)[0];
            }

            function errorMessage(message) {
                $('#toast-container').show(300)
                $('#toast-message').html(message)

                setTimeout(() => {
                    $('#toast-container').hide(300)
                }, 5000)
            }

            function getElements(domain) {
                let elements = [];
                $.each($('.card.card-row.card-secondary.kanban-card.mr-5'), function (key, value) {
                    elements.push($($(value).find(".fixed-lines[data-domain='" + domain + "']")).parent())
                })

                return elements
            }

            function setRelationShips() {
                let colorArray = COLORS

                $('.set-relationships').unbind().on('click', function () {
                    let color = colorArray.shift()
                    let targetUrl = $(this).parents().eq(2).children('div').eq(1).attr('data-domain')
                    let id = randomInteger(0, 90000000)
                    let find = false

                    let elements = getElements(targetUrl)
                    for (let i = 0; i < elements.length; i++) {
                        if (elements[i + 1] !== undefined) {
                            for (let j = 0; j < elements[i].length; j++) {
                                let will = []
                                for (let k = 0; k < elements[i + 1].length; k++) {
                                    find = true
                                    let extra = will.includes(elements[i][j])
                                    drawConnect(elements[i][j], elements[i + 1][k], color, id, extra);
                                    changeActions(elements[i][j], id)
                                    changeActions(elements[i + 1][k][j], id)
                                    will.push(elements[i][j])
                                }
                            }
                        }
                    }

                    let last = elements.length - 1
                    for (let i = 0; i < elements[last].length; i++) {
                        changeActions(elements[last][i], id)
                    }

                    if (find === false) {
                        errorMessage("{{ __('No matches found') }}")
                    } else {
                        $('.remove-relationships').on('click', function () {
                            $('.' + $(this).attr('data-id')).remove()

                            $('.remove-relationships[data-id="' + $(this).attr('data-id') + '"]').parent().append(
                                '<span class="dropdown-item set-relationships" style="cursor: pointer;">' +
                                '    {{ __('View positions') }}' +
                                '</span> '
                            )
                            $('.remove-relationships[data-id="' + $(this).attr('data-id') + '"]').remove()
                            setRelationShips()
                            setRelationShipsFromLink()
                        })

                        $(this).remove()
                    }
                });
            }

            function setRelationShipsFromLink() {
                let colorArray = COLORS

                $('.fixed-lines').unbind().on('click', function () {

                    let targetElement = $(this)
                    if (targetElement.parent().children('div').eq(2).children('div').eq(0).children('span.dropdown-item.remove-relationships').eq(0).length > 0) {
                        targetElement.parent().children('div').eq(2).children('div').eq(0).children('span.dropdown-item.remove-relationships').eq(0).trigger('click')
                    } else {
                        let color = colorArray.shift()
                        let targetUrl = $(this).attr('data-domain')
                        let id = randomInteger(0, 90000000)
                        let find = false

                        let elements = getElements(targetUrl)
                        for (let i = 0; i < elements.length; i++) {
                            if (elements[i + 1] !== undefined) {
                                for (let j = 0; j < elements[i].length; j++) {
                                    let will = []
                                    for (let k = 0; k < elements[i + 1].length; k++) {
                                        find = true
                                        let extra = will.includes(elements[i][j])
                                        drawConnect(elements[i][j], elements[i + 1][k], color, id, extra);
                                        changeActions(elements[i][j], id)
                                        changeActions(elements[i + 1][k][j], id)
                                        will.push(elements[i][j])
                                    }
                                }
                            }
                        }

                        let last = elements.length - 1
                        for (let i = 0; i < elements[last].length; i++) {
                            changeActions(elements[last][i], id)
                        }

                        if (find === false) {
                            errorMessage("{{ __('No matches found') }}")
                        } else {
                            $('.remove-relationships').on('click', function () {
                                $('.' + $(this).attr('data-id')).remove()

                                $('.remove-relationships[data-id="' + $(this).attr('data-id') + '"]').parent().append(
                                    '<span class="dropdown-item set-relationships" style="cursor: pointer;">' +
                                    '{{ __('View positions') }}' +
                                    '</span> '
                                )
                                $('.remove-relationships[data-id="' + $(this).attr('data-id') + '"]').remove()
                                setRelationShips()
                                setRelationShipsFromLink()
                            })
                        }
                    }
                });
            }

            function changeActions(element, id) {
                let $element = $(element)

                if ($element.children('div').eq(2).children('div').eq(0).find('.dropdown-item.remove-relationships').length === 0) {
                    $element.children('div').eq(2).children('div').eq(0).append(
                        '<span class="dropdown-item remove-relationships" data-id="' + id + '">' +
                        '{{ __('Delete a link of positions') }}' +
                        '</span>'
                    )
                }
                $element.children('div').eq(2).children('div').eq(0).find('.dropdown-item.set-relationships').remove()
            }

            function filter() {
                $('#filter').unbind().on('input', function () {
                    let filterValue = $('#filter').val().trim().toLowerCase()
                    if (filterValue !== '') {
                        $.each($('.fixed-lines'), function () {
                            if ($(this).html().toLowerCase().indexOf(filterValue) === -1) {
                                $(this).parent().addClass('hide-element')
                            } else {
                                $(this).parent().removeClass('hide-element')
                            }
                        });
                    } else {
                        $('.hide-element').removeClass('hide-element')
                    }
                })
            }

            function maxTop() {
                $('#top').unbind().on('change', function () {
                    let top = $('#top').val()

                    $('[data-index].kanban-item').each(function () {
                        if (parseInt($(this).attr('data-index')) > top) {
                            $(this).hide()
                        } else {
                            $(this).show()
                        }
                    });

                    $('.remove-relationships').trigger('click')
                })
            }

            function changeVisual() {
                $('.change-filter-name').unbind().on('click', function () {
                    activeFilter = $(this).attr('data-action')
                    $('#filter-target').html(activeFilter)

                    if (activeFilter === 'URL') {
                        $.each($('.fixed-lines'), function () {
                            $(this).html($(this).attr('data-url'))
                        })
                    } else {
                        $.each($('.fixed-lines'), function () {
                            $(this).html($(this).attr('data-domain'))
                        })
                    }
                })
            }

            function disableElements() {
                $('#select-my-project').prop('disabled', true)
                $('#top').prop('disabled', true)
                $('.change-filter-name').prop('disabled', true)
                $('#filter').prop('disabled', true)
                $('#analyse').prop('disabled', true)

                $('#select-my-project').attr('data-action', 'color')
                $('#select-my-project').html('{{ __('Select the project domain') }}')
            }

            function enableElements() {
                $('#select-my-project').prop('disabled', false)
                $('#top').prop('disabled', false)
                $('.change-filter-name').prop('disabled', false)
                $('#filter').prop('disabled', false)
                $('#analyse').prop('disabled', false)

                selectProject()
            }

            function selectProject() {
                $('#select-my-project').unbind().on('click', function () {
                    if ($(this).attr('data-action') === 'color') {
                        let find = false
                        let target = $(this).attr('data-target');

                        $.each($('.fixed-lines'), function () {
                            if ($(this).html().toLowerCase().indexOf(target) !== -1) {
                                if (!$(this).parent().hasClass()) {
                                    $(this).parent().addClass('color-domain')
                                    find = true;
                                }
                            }
                        });

                        if (find) {
                            $(this).attr('data-action', 'uncolor')
                            $(this).html('{{ __('Remove project selection') }}')
                            if ($(".color-domain").first().children('div').eq(2).children('div').eq(0).find('span.set-relationships').length > 0) {
                                $(".color-domain").first().children('div').eq(1).trigger('click')
                            }
                        } else {
                            errorMessage('{{ __('Domain not found') }}')
                        }

                    } else {
                        if ($(".color-domain").first().children('div').eq(2).children('div').eq(0).find('span.remove-relationships').length > 0) {
                            $(".color-domain").first().children('div').eq(1).trigger('click')
                        }
                        $('.color-domain').removeClass('color-domain')
                        $(this).attr('data-action', 'color')
                        $(this).html('{{ __('Select the project domain') }}')
                    }
                })
            }
        </script>
    @endslot

@endcomponent
