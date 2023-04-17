@component('component.card', ['title' =>  'Статистика пользователя '. $user->email ])
    @if(count($summedCollection) > 0)
        @slot('css')
            <!-- Toastr -->
            <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
            <!-- DataTables -->
            <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
            <link rel="stylesheet"
                  href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
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
        <div class="d-flex flex-column">
            <div class="d-flex w-100">
                <div class="col-8">
                    <h3>Статистика пользователя {{ $user->email }} за всё время</h3>
                    <table id="table" class="table table-striped no-footer border">
                        <thead>
                        <tr>
                            <th>Модуль</th>
                            <th>
                                Время
                                <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 400px">
                                            Счётчик времени проведённого в модуле <br>
                                            Формат - часы:минуты:секунды
                                        </span>
                                    </span>
                                </span>
                            </th>
                            <th>
                                Количество обновлений страницы
                                <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 400px">
                                            Учитывается переход на страницу и её обновление
                                        </span>
                                    </span>
                                </span>
                            </th>
                            <th>
                                Количество действий
                                <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 400px">
                                            Учитывается нажатие кнопок для получения дополнительной инфомрации из бд и т.п.
                                        </span>
                                    </span>
                                </span>
                            </th>
                            <th>Всего действий</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($summedCollection as $module)
                            <tr>
                                <td>
                                    <a href="{{ $module->project->link }}"
                                       target="_blank">{{ __($module->project->title) }}</a>
                                </td>
                                <td>{{ $module->time }}</td>
                                <td>{{ $module->refreshPageCounter }}</td>
                                <td>{{ $module->actionsCounter }}</td>
                                <td>{{ $module->actionsCounter + $module->refreshPageCounter }}</td>
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
                                <th>
                                    Время
                                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 400px">
                                            Счётчик времени проведённого в модуле <br>
                                            Формат - часы:минуты:секунды
                                        </span>
                                    </span>
                                </span>
                                </th>
                                <th>
                                    Количество обновлений страницы
                                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 400px">
                                            Учитывается переход на страницу и её обновление
                                        </span>
                                    </span>
                                </span>
                                </th>
                                <th>
                                    Количество действий
                                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 400px">
                                            Учитывается нажатие кнопок для получения дополнительной инфомрации из бд и т.п.
                                        </span>
                                    </span>
                                </span>
                                </th>
                                <th>Всего действий</th>
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
                        "order": [[3, 'desc']],
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
                let colors = []

                new Chart(document.getElementById("doughnut-chart"), {
                    type: 'doughnut',
                    data: {
                        labels: {!! $info['labels'] !!},
                        datasets: [
                            {
                                backgroundColor: {!! $info['colors'] !!},
                                data: {!! $info['counters'] !!}
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
                            let counters = response.info.counters;
                            let labels = response.info.labels;
                            let trs = ''
                            let sum = []

                            if (counters.length > 0) {
                                if ($.fn.DataTable.fnIsDataTable($('#actions-table'))) {
                                    $('#actions-table').dataTable().fnDestroy();
                                    targetBody.html('')
                                }

                                let iterator = 0;
                                $.each(labels, function (link, name) {
                                    let targetSum = counters[iterator]['refreshPageCounter'] + counters[iterator]['actionsCounter']
                                    sum.push(targetSum)
                                    doughnutLabels.push(name)

                                    trs += '<tr>' +
                                        '<td class="border">' +
                                        '    <a href="' + link + '" target="_blank">' + name + '</a>' +
                                        '</td>' +
                                        '<td class="border">' + response.info.time[iterator] + '</td>' +
                                        '<td class="border">' + counters[iterator]['refreshPageCounter'] + '</td>' +
                                        '<td class="border">' + counters[iterator]['actionsCounter'] + '</td>' +
                                        '<td class="border">' + targetSum + '</td>' +
                                        '<tr>'
                                    iterator++;
                                })
                                targetBody.append(trs)

                                $('#history-actions-tbody tr').each(function () {
                                    if (!$.trim($(this).text())) $(this).remove();
                                });

                                $('#actions-table').DataTable({
                                    "order": [[4, 'desc']],
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
                                                backgroundColor: response.info.colors,
                                                data: sum
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
            </script>
        @endslot
    @else
        Нет данных
    @endif
@endcomponent
