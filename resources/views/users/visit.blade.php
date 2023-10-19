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

                #app {
                    padding-bottom: 10px;
                }
            </style>
        @endslot

        <div class="d-flex flex-column">
            <div class="card mt-3">
                <div class="card-header">
                    <div class="d-flex flex-column w-25" style="float: left;">
                        <h3>Фильтр по дате</h3>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" id="date-range" class="form-control">
                                <button id="show-actions" class="btn btn-default btn-group"
                                        style="border-top-left-radius: 0px; border-bottom-left-radius: 0px;">
                                    Показать
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="history-actions" class="card-body">
                    <div class="d-flex">
                        <table id="actions-table" class="table table-striped border">
                            <thead>
                            <tr>
                                <th>Модуль</th>
                                <th>
                                    Время
                                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal;"><i
                                            class="fa fa-question-circle" style="color: grey;"></i> <span
                                            class="ui_tooltip __bottom"><span class="ui_tooltip_content"
                                                                              style="width: 400px;">
                                            Счётчик времени проведённого в модуле <br>
                                            Формат - часы:минуты:секунды
                                        </span></span></span></th>
                                <th>
                                    Количество обновлений страницы
                                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal;"><i
                                            class="fa fa-question-circle" style="color: grey;"></i> <span
                                            class="ui_tooltip __bottom"><span class="ui_tooltip_content"
                                                                              style="width: 400px;">
                                            Учитывается переход на страницу и её обновление
                                        </span></span></span></th>
                                <th>
                                    Количество действий
                                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal;"><i
                                            class="fa fa-question-circle" style="color: grey;"></i> <span
                                            class="ui_tooltip __bottom"><span class="ui_tooltip_content"
                                                                              style="width: 400px;">
                                            Учитывается нажатие кнопок для получения дополнительной инфомрации из бд и т.п.
                                        </span></span></span></th>
                                <th>Всего действий</th>
                                <th>Дата последнего визита</th>
                            </tr>
                            </thead>
                            <tbody id="history-actions-tbody">
                            <tr>
                                <td colspan="6" style="height: 40px; text-align: center; vertical-align: inherit">
                                    Используйте фильтр по дате
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="col-3">
                            <canvas id="history-doughnut-chart" style="position: relative; width: 100%;"></canvas>
                        </div>
                    </div>
                    <div class="mt-3">
                        <canvas id="another-linear-chart" style="position: relative; width: 50%; min-height: 350px;max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        @slot('js')
            <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
            <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
            <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
            <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>
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

                            try {
                                if (!found.el.hasClass('exist-position')) {
                                    found.el.addClass('exist-position');
                                }
                            } catch (e) {

                            }
                        });
                    })
                });

                let anotherChart
                let historyChart
                let colors = []
                let results = {!! $counterActions !!};

                $('#show-actions').on('click', function () {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('user.actions.history') }}",
                        data: {
                            dateRange: $('#date-range').val(),
                            userId: {{ $user->id }}
                        },
                        success: function (response) {
                            let doughnutLabels = []
                            let targetBody = $('#history-actions-tbody')
                            targetBody.html('')
                            let counters = response.info.counters;
                            let labels = response.info.labels;
                            let trs = ''
                            let sum = []

                            if (counters.length > 0) {
                                if ($.fn.DataTable.fnIsDataTable($('#actions-table'))) {
                                    $('#actions-table').dataTable().fnDestroy();
                                    targetBody.html('')
                                    historyChart.destroy()
                                    anotherChart.destroy()
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
                                        '<td class="border">' + searchLastProjectActivities(link, response.collection, response.lastActions) + '</td>' +
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

                                anotherChart = renderChart('another-linear-chart', response.counterActions, true)
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

                            if ($('#history-actions').is(':hidden')) {
                                $("#app > div > div > div.card-body > div > div.card.mt-3.collapsed-card > div.card-header > div.card-tools.d-inline > button:nth-child(1)").trigger('click')
                            }

                        }
                    });
                })

                $(document).ready(function () {
                    $('#show-actions').trigger('click')
                })

                function renderChart(chartId, data, returnChart = false) {
                    let graph = document.getElementById(chartId).getContext('2d');

                    let dataLine1 = {
                        label: 'Время проведённое в модулях (сек)',
                        data: data['time'],
                        borderColor: 'red',
                        fill: false
                    };

                    let dataLine2 = {
                        label: 'Количество действий',
                        data: data['actions'],
                        borderColor: 'blue',
                        fill: false
                    };

                    let dataLine3 = {
                        label: 'Количество обновлений страниц',
                        data: data['refresh'],
                        borderColor: 'green',
                        fill: false
                    };

                    let chartConfig = {
                        type: 'line',
                        data: {
                            labels: data['data'],
                            datasets: [dataLine1, dataLine2, dataLine3]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                },
                            },
                        },
                    };

                    if (returnChart) {
                        return new Chart(graph, chartConfig);
                    } else {
                        new Chart(graph, chartConfig);
                    }
                }

                function searchLastProjectActivities(link, projects, activitiesArray) {
                    let response
                    $.each(projects, function (key, item) {
                        if (item.project.link === link) {
                            response = activitiesArray[item.project.id]
                            return false
                        }
                    })

                    return response;
                }
            </script>
        @endslot
    @else
        {{ __('No records') }}
    @endif
@endcomponent
