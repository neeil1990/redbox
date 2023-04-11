@component('component.card', ['title' =>  'Статистика пользователя '. $user->email ])
    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>

        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

        <style>
            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }

            #actions-table_wrapper {
                width: 75%;
            }
        </style>
    @endslot
    @if(count($summedCollection) > 0)
        <div class="d-flex flex-column">
            <div class="d-flex w-100">
                <div class="col-8">
                    <h3>Статистика пользователя {{ $user->email }} за всё время</h3>
                    <table id="table" class="table table-striped no-footer border">
                        <thead>
                        <tr>
                            <th>Модуль</th>
                            <th>
                                Количество действий
                                <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 400px">
                                            Учитываются обновления страницы, <br> нажатия кнопок для получения дополнительной инфомрации из бд и т.п.
                                        </span>
                                    </span>
                                </span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($summedCollection as $module)
                            <tr>
                                <td>
                                    <a href="{{ $module->project->link }}"
                                       target="_blank">{{ __($module->project->title) }}</a>
                                </td>
                                <td>{{ $module->counter }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-4">
                    <canvas id="doughnut-chart" style="position: relative; width: 100%"></canvas>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header d-flex">
                    <div class="d-flex flex-column">
                        <h3>Фильтр по дате</h3>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                                </div>
                                <input type="text" class="form-control" id="date-range">
                                <button id="show-actions" class="btn btn-default btn-group"
                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0">
                                    {{ __('show') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="history-actions" style="display: none">
                    <div class="d-flex">
                        <table id="actions-table" class="table table-hover border">
                            <thead>
                            <tr>
                                <th>Модуль</th>
                                <th>Количество действий</th>
                            </tr>
                            </thead>
                            <tbody id="history-actions-tbody">

                            </tbody>
                        </table>
                        <div class="col-3">
                            <canvas id="history-doughnut-chart" style="position: relative; width: 100%"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @else
                Нет данных
            @endif
        </div>

        @slot('js')
            <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
            <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
            <!-- date-range-picker -->
            <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

            <script>
                let startDate = null;
                let endDate = null;

                let range = $('#date-range');
                range.daterangepicker({
                    opens: 'left',
                    startDate: startDate ?? moment().subtract(30, 'days'),
                    endDate: endDate ?? moment(),
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

                    axios.get('/get-data-range-visit-statistics/' + {{ $user->id }}).then(function (response) {

                        $.each(response.data.dates, function (i, item) {
                            let found = showDates.find(function (elem) {
                                if (elem.date === item.date)
                                    return true;
                            });

                            if (!found.el.hasClass('exist-position'))
                                found.el.addClass('exist-position');
                        });
                    })
                });
            </script>
            @if(count($summedCollection) > 0)
                <!-- datatables -->
                <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
                <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
                <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
                <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
                <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
                <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

                <script src="{{ asset('plugins/datatables-buttons/js/buttons.excel.min.js') }}"></script>
                <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.js') }}"></script>
                <script>
                    $(document).ready(function () {
                        $('#table').DataTable({
                            "order": [[1, 'desc']],
                            lengthMenu: [10, 25, 50, 100],
                            pageLength: 10,
                            language: {
                                lengthMenu: "_MENU_",
                                search: "_INPUT_",
                                searchPlaceholder: "{{ __('Search') }}",
                                paginate: {
                                    "first": "«",
                                    "last": "»",
                                    "next": "»",
                                    "previous": "«"
                                },
                            },
                        })
                    })
                </script>

                <!-- Charts -->
                <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>

                <script>
                    let historyChart
                    let labels = [];
                    let colors = []
                    let colorsArray = getColorArray()
                    for (let i = 0; i < {{ count($summedCollection) }}; i++) {
                        colors.push(colorsArray.shift())
                    }

                    new Chart(document.getElementById("doughnut-chart"), {
                        type: 'doughnut',
                        data: {
                            labels: {!! $labels !!},
                            datasets: [
                                {
                                    backgroundColor: colors,
                                    data: {!! $counters !!}
                                }
                            ]
                        },
                        options: {
                            title: {
                                display: false,
                            }
                        }
                    });

                    $('#show-actions').on('click', function () {
                        $.ajax({
                            type: "POST",
                            url: "/user-actions-history",
                            data: {
                                dateRange: $('#date-range').val(),
                            },
                            success: function (response) {
                                let doughnutLabels = []
                                let targetBody = $('#history-actions-tbody')
                                let counters = response.counters;
                                let labels = response.labels;
                                let trs = ''

                                if (counters.length > 0) {
                                    if ($.fn.DataTable.fnIsDataTable($('#actions-table'))) {
                                        $('#actions-table').dataTable().fnDestroy();
                                        targetBody.html('')
                                    }

                                    let iterator = 0;

                                    $.each(labels, function (link, name) {
                                        doughnutLabels.push(name)
                                        trs += '<tr>' +
                                            '<td class="border">' +
                                            '    <a href="' + link + '" target="_blank">' + name + '</a>' +
                                            '</td>' +
                                            '<td class="border">' + counters[iterator] + '</td>' +
                                            '<tr>'
                                        iterator++;
                                    })
                                    targetBody.append(trs)

                                    $('#history-actions-tbody tr').each(function () {
                                        if (!$.trim($(this).text())) $(this).remove();
                                    });

                                    $('#actions-table').DataTable({
                                        "order": [[1, 'desc']],
                                        lengthMenu: [10, 25, 50, 100],
                                        pageLength: 10,
                                        language: {
                                            lengthMenu: "_MENU_",
                                            search: "_INPUT_",
                                            searchPlaceholder: "{{ __('Search') }}",
                                            paginate: {
                                                "first": "«",
                                                "last": "»",
                                                "next": "»",
                                                "previous": "«"
                                            },
                                        },
                                    })


                                    try {
                                        historyChart.destroy()
                                    } catch (e) {

                                    }

                                    historyChart = new Chart(document.getElementById("history-doughnut-chart"), {
                                        type: 'doughnut',
                                        data: {
                                            labels: doughnutLabels,
                                            datasets: [
                                                {
                                                    backgroundColor: colors,
                                                    data: counters
                                                }
                                            ]
                                        },
                                        options: {
                                            title: {
                                                display: false,
                                            }
                                        }
                                    });
                                } else {
                                    targetBody.append(
                                        '<tr>' +
                                        '<td>Нет данных</td>' +
                                        '<td>Нет данных</td>' +
                                        '</tr>')

                                    try {
                                        historyChart.destroy()
                                    } catch (e) {

                                    }
                                }

                                $('#history-actions').show()

                            }
                        });
                    })

                    function getColorArray() {
                        let colorArray = [
                            "rgba(220, 51, 10, 0.6)",
                            "rgb(203,60,25)",
                            "rgba(121, 25, 6, 1)",
                            "rgba(214, 96, 110, 0.6)",
                            "rgba(214, 96, 110, 1)",
                            "rgba(252, 170, 153, 0.6)",
                            "rgba(252, 170, 153, 1)",
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
                            "rgba(178, 135, 33, 0.6)",
                            "rgba(178, 135, 33, 1)",
                            "rgba(246, 223, 78, 1)",
                            "rgba(1, 253, 215, 0.6)",
                            "rgba(1, 253, 215, 1)",
                            "rgba(1, 148, 130, 0.6)",
                            "rgba(1, 79, 66, 0.6)",
                            "rgba(139, 150, 24, 0.6)",
                            "rgba(154, 205, 50, 0.6)",
                            "rgba(154, 205, 50, 1)",
                            "rgb(17, 255, 0)",
                            "rgba(151, 186, 229, 1)",
                            "rgba(0, 69, 255, 0.6)",
                            "rgba(0, 69, 255, 1)",
                            "rgba(1, 45, 152, 0.6)",
                            "rgba(157, 149, 226, 1)",
                            "rgba(6, 136, 165, 0.6)",
                            "rgba(64, 97, 206, 1)",
                            "rgba(19,212,224, 0.6)",
                            "rgba(19,212,224, 1)",
                            "rgba(2, 97, 214, 0.6)",
                            "rgba(159, 112, 216, 0.6)",
                            "rgba(239, 50, 223, 0.6)",
                            "rgba(239, 50, 223, 1)",
                            "rgba(209, 46, 127, 0.6)",
                            "rgba(209, 46, 127, 1)",
                            "rgba(194, 85, 237, 1)",
                            "rgba(252, 194, 243, 1)",
                            "rgba(244, 139, 200, 0.6)",
                            "rgba(244, 139, 200, 1)",
                            "rgba(87, 64, 64, 0.6)",
                            "rgba(239, 211, 211, 0.6)",
                            "rgba(163, 209, 234, 0.6)",
                            "rgba(234,163,163,0.6)",
                            "rgba(232,194,90,0.6)",
                        ]

                        return colorArray.sort(() => Math.random() - 0.5);
                    }
                </script>
            @endif
        @endslot
        @endcomponent
