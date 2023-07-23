@component('component.card', ['title' => __('Топ 100 проекта') . " $project->name"])

    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>

        <style>
            .kanban-item {
                margin-bottom: 0;
                min-height: 60px;
                max-height: 60px;
                justify-content: center;
                align-items: center;
            }

            .fixed-lines {
                word-wrap: break-word;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                cursor: pointer;
            }

            .kanban-card {
                min-width: 400px;
                max-width: 400px;
            }

            #filter-button {
                position: fixed;
                top: 130px;
                right: 15px;
                display: none;
                z-index: 1000;
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


    <div class="card w-50 ml-3">
        <div class="card-header">
            Настройки анализа
        </div>
        <div class="card-body">
            <div>
                <select class="form form-control" size="10"
                        name="project_keywords">
                    @foreach($project->keywords as $keyword)
                        <option value="{{ $keyword->query }}">{{ $keyword->query }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-footer">
            <div class="form-group">
                <label>Диапазон дат:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                    </div>
                    <input type="text" id="date-range" class="form-control float-right">
                </div>
            </div>
            <div class="form-group">
                <label for="region">Регион</label>
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
                    <button class="btn btn-secondary" id="analyse">Анализировать</button>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group ml-3 mt-3">
        <div id="progress" style="display: none">
            <img src="/img/1485.gif" style="width: 50px; height: 50px;">
            <br>
            Проанализированно <span id="analysed-days">0</span> из <span id="total-days">0</span> выбранных дат
        </div>
    </div>

    <button type="button" class="btn btn-flat btn-secondary" id="filter-button" data-toggle="modal"
            data-target="#configModal">
        <i class="fa-solid fa-filter"></i>
        Фильтр
    </button>

    <div class="modal fade" id="configModal" tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Дополнительные фильтры
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column">
                        <label>Отображать</label>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary active change-filter-name" data-action="URL">
                                <input type="radio" name="options" id="option_a1" autocomplete="off" checked="">
                                URL
                            </label>
                            <label class="btn btn-secondary change-filter-name" data-action="домену">
                                <input type="radio" name="options" id="option_a2" autocomplete="off">
                                Домен
                            </label>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="filter">Фильтр по <span id="filter-target">URL</span></label>
                        <input type="text" id="filter" name="filter" class="form form-control">
                    </div>
                    <div class="mt-3">
                        <label for="top">Отображать</label>
                        <select name="top" id="top" class="custom-select">
                            <option value="100">100</option>
                            <option value="50">50</option>
                            <option value="30">30</option>
                            <option value="20">20</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-secondary" id="set-filter" data-dismiss="modal">Применить фильтр</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="overflow-x: auto; width: 100%" class="d-flex">
        <div class="d-flex mt-5" style="display: flex; min-width: 100%"
             id="result">
        </div>
    </div>

    @slot('js')
        <!-- InputMask -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
        <script>
            $('select[name="project_keywords"]').bootstrapDualListbox({
                selectedListLabel: 'Анализируемые слова',
                nonSelectedListLabel: 'Ключевые слова проекта',
                preserveSelectionOnMove: '{{ __('Moved') }}',
                moveAllLabel: '{{ __('Move all') }}',
                removeAllLabel: '{{ __('Move all') }}'
            });

            $('#bootstrap-duallistbox-nonselected-list_project_keywords').addClass('form-control')
            $('#bootstrap-duallistbox-selected-list_project_keywords').addClass('form-control')
            $('.moveall').addClass('btn btn-default')
            $('.removeall').addClass('btn btn-default')

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

            let activeFilter = 'url'

            $('#analyse').on('click', function () {
                $(this).prop('disabled', true)
                let words = [];
                $.each($('#bootstrap-duallistbox-selected-list_project_keywords').children(), function (key, value) {
                    words.push($(this).attr('value'))
                })

                if (words.length === 0) {
                    errorMessage('Список анализируемых слов не может быть пустым')
                } else {
                    $('#filter-button').hide(300)
                    $('#result').html('')

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
                    $('#analyse').prop('disabled', false)
                    $('#progress').hide(300)
                    $('#filter-button').show(300)
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
                    $(function () {
                        $('[data-toggle="tooltip"]').tooltip()
                    })

                    setRelationShips()
                }, 1000)
            }

            async function processWordAndDates(word, dates) {
                let counter = 1;
                for (const date of dates) {
                    await sendAjaxRequest(word, date);
                    $('#analysed-days').html(counter)
                    counter++
                }
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
                    kanbanItems +=
                        '<div class="kanban-item w-100 border-bottom" data-index="' + v.position + '">' +
                        '    <div class="col-2 mt-2" style="float:left">' + v.position + ' </div>' +
                        '    <div class="col-9 fixed-lines mt-2" style="float:left"' +
                        ' data-url="' + v.url + '" ' +
                        ' data-domain="' + new URL(v.url)['origin'] + '"' +
                        ' data-toggle="tooltip" data-placement="top" title="' + v.url + '">' + v.url + ' </div>' +
                        '<div class="dropdown show" style="display: inline;" style="float:left">' +
                        '    <i id="dropdownMenuButton" data-toggle="dropdown" aria-expanded="true" class="fa fa-cogs mt-3" style="opacity: 0.6; cursor: pointer;"></i>' +
                        '    <div aria-labelledby="dropdownMenuButton" class="dropdown-menu hide" style="position: absolute; transform: translate3d(0px, 18px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">' +
                        '        <span class="dropdown-item" style="cursor: pointer;">' +
                        '            <a href="' + v.url + '" target="_blank">Перейти на сайт</a>' +
                        '        </span> ' +
                        '        <span class="dropdown-item" style="cursor: pointer;">' +
                        '            <a href="/redirect-to-text-analyzer/' + v.url.replaceAll('/', 'abc') + '" target="_blank">Анализировать</a>' +
                        '        </span> ' +
                        '        <span class="dropdown-item copy" style="cursor: pointer;" data-target="' + v.url + '">' +
                        '            <span>Копировать URL</span>' +
                        '        </span> ' +
                        '        <span class="dropdown-item copy" style="cursor: pointer;"  data-target="' + new URL(v.url)['origin'] + '">' +
                        '            <a>Копировать домен</a>' +
                        '        </span> ' +
                        '        <span class="dropdown-item set-relationships" style="cursor: pointer;" data-target="' + v.url + '">' +
                        '            Посмотреть позиции' +
                        '        </span> ' +
                        '</div>' +
                        '</div>' +
                        '</div>'
                })

                return '<div class="card card-row card-secondary kanban-card ml-3 mr-3">' +
                    '    <div class="card-header pl-2 w-100">' +
                    '        <span class="col-2">#</span>' +
                    '        <span class="col-10">' + date + '</span>' +
                    '    </div>' +
                    kanbanItems +
                    '</div>'
            }

            $('.change-filter-name').unbind().on('click', function () {
                activeFilter = $(this).attr('data-action')
                $('#filter-target').html(activeFilter)
                $('#filter').val('')
            })

            $('#set-filter').on('click', function () {
                let action = $('.btn.btn-secondary.change-filter-name.active').attr('data-action')

                if (action === 'URL') {
                    $.each($('.fixed-lines'), function () {
                        $(this).html($(this).attr('data-url'))
                    })
                } else {
                    $.each($('.fixed-lines'), function () {
                        $(this).html($(this).attr('data-domain'))
                    })
                }

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

            function randomInteger(min, max) {
                let rand = min + Math.random() * (max + 1 - min);
                return Math.floor(rand);
            }

            function drawConnect(from, to, color, id) {

                function createConnection() {
                    return $("<div />")
                        .addClass('connection ' + id)
                        .css('background', color);
                }

                var $from = from
                    , $to = to
                    , $main = $("#result");

                var mainTop = $main.offset().top  //Расстояние сверху от контейнера
                    , mainLeft = $main.offset().left //Расстояние сбоку от контейнера
                    , mainHeight = $main.outerHeight() //Высота контейнера
                    , fromLeft = $from.offset().left + $from.outerWidth() - mainLeft //Точка ИЗ (сбоку)
                    , toLeft = $to.offset().left - mainLeft //Точка В (сбоку)
                    , fromTop = $from.offset().top + $from.outerHeight() / 2 - mainTop //Точка ИЗ (сверху)
                    , toTop = $to.offset().top + $to.outerHeight() / 2 - mainTop //Точка В (сверху)
                    , width = toLeft - fromLeft
                    , height = toTop - fromTop;

                var w1 = Math.round(Math.abs(width / (randomInteger(20, 60) / 10))),
                    w2 = width - w1;

                createConnection()
                    .css('left', fromLeft + 'px')
                    .css('top', fromTop + 'px')
                    .css('width', w1 + 'px')
                    .appendTo($main);

                var $c = createConnection()
                    .css('left', fromLeft + w1 + 'px')
                    .css('height', Math.abs(height))
                    .appendTo($main);

                if (height >= 0) {
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
                    "rgb(203,60,25)",
                    "rgba(121, 25, 6, 1)",
                    "rgba(214, 96, 110, 0.6)",
                    "rgba(214, 96, 110, 1)",
                    "rgba(252, 170, 153, 0.6)",
                    "rgba(214, 2, 86, 0.6)",
                    "rgba(214, 2, 86, 1)",
                    "rgba(147,50,88, 1)",
                    "rgba(247, 220, 163, 1)",
                    "rgba(204, 118, 32, 0.6)",
                    "rgba(204, 118, 32, 1)",
                    "rgba(255,89,0,0.6)",
                    "rgba(255, 89, 0, 1)",
                    "rgba(164, 58 ,1, 1)",
                    "rgba(73, 28, 1, 0.6)",
                    "rgba(246, 223, 78, 1)",
                    "rgba(1, 253, 215, 0.6)",
                    "rgba(1, 253, 215, 1)",
                    "rgba(1, 79, 66, 0.6)",
                    "rgba(154, 205, 50, 1)",
                    "rgb(17, 255, 0)",
                    "rgba(151, 186, 229, 1)",
                    "rgba(0, 69, 255, 0.6)",
                    "rgba(1, 45, 152, 0.6)",
                    "rgba(6, 136, 165, 0.6)",
                    "rgba(64, 97, 206, 1)",
                    "rgba(19,212,224, 1)",
                    "rgba(2, 97, 214, 0.6)",
                    "rgba(239, 50, 223, 0.6)",
                    "rgba(239, 50, 223, 1)",
                    "rgba(209, 46, 127, 0.6)",
                    "rgba(209, 46, 127, 1)",
                    "rgba(194, 85, 237, 1)",
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

            function setRelationShips() {
                $('.set-relationships').unbind().on('click', function () {
                    let targetElement = $(this)
                    let targetUrl = targetElement.parents().eq(2).children('div').eq(1).html()
                    let blocks = $('.card.card-row.card-secondary.kanban-card.ml-3.mr-3')
                    let color = getRandomColor()
                    let id = randomInteger(0, 90000000)
                    let firstElem = false
                    let secondElem = false
                    let find = false

                    for (let i = 0; i < blocks.length; i++) {
                        let parent = $(blocks[i]).find(".fixed-lines:contains(" + targetUrl + ")").parent()

                        if (firstElem === false) {
                            if (parent.length !== 0 && parent.is(':visible')) {
                                firstElem = $(blocks[i]).find(".fixed-lines:contains(" + targetUrl + ")").parent()
                            }
                        } else if (firstElem !== false && secondElem === false) {
                            if (parent.length !== 0 && parent.is(':visible')) {
                                secondElem = $(blocks[i]).find(".fixed-lines:contains(" + targetUrl + ")").parent();
                            } else {
                                firstElem = false
                            }
                        }

                        if (firstElem !== false && secondElem !== false) {
                            drawConnect(firstElem, secondElem, color, id);
                            if (firstElem.children('div').eq(2).children('div').eq(0).find('.dropdown-item.remove-relationships').length === 0) {
                                firstElem.children('div').eq(2).children('div').eq(0).append(
                                    '<span class="dropdown-item remove-relationships" data-id="' + id + '">' +
                                    'Удалить связь позиций' +
                                    '</span>'
                                )
                            }
                            firstElem.children('div').eq(2).children('div').eq(0).find('.dropdown-item.set-relationships').remove()

                            if (secondElem.children('div').eq(2).children('div').eq(0).find('.dropdown-item.remove-relationships').length === 0) {
                                secondElem.children('div').eq(2).children('div').eq(0).append(
                                    '<span class="dropdown-item remove-relationships" data-id="' + id + '">' +
                                    'Удалить связь позиций' +
                                    '</span>'
                                )
                            }
                            secondElem.children('div').eq(2).children('div').eq(0).find('.dropdown-item.set-relationships').remove()

                            firstElem = secondElem
                            secondElem = false
                            find = true
                        }
                    }

                    if (find === false) {
                        errorMessage('Совпадений не найдено')
                    } else {
                        $('.remove-relationships').on('click', function () {
                            $('.' + $(this).attr('data-id')).remove()

                            $('.remove-relationships[data-id="' + $(this).attr('data-id') + '"]').parent().append(
                                '<span class="dropdown-item set-relationships" style="cursor: pointer;">' +
                                '    Посмотреть позиции' +
                                '</span> '
                            )
                            $('.remove-relationships[data-id="' + $(this).attr('data-id') + '"]').remove()
                            setRelationShips()
                        })

                        targetElement.remove()
                    }
                });
            }
        </script>
    @endslot

@endcomponent
