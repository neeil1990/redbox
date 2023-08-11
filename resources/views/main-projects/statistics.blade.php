@component('component.card', ['title' =>  'Статистика модуля "' . __($project->title) . '"'])
    @if(count($result) > 0)
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

                .dt-buttons.btn-group.flex-wrap {
                    float: left;
                    margin-bottom: 5px;
                }

                #actionsTable > th {
                    width: 150px !important;
                }
            </style>

        @endslot

        <div class="mb-5">
            <a class="btn btn-default" href="{{ $project->link }}" target="_blank">Перейти в модуль</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Общая статистика</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="table" class="table table-striped no-footer border">
                    <thead>
                    <tr>
                        <th class="col-1">Дата</th>
                        <th class="col-1">
                            Общее время
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
                        <th class="col-2">
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
                        <th class="col-2">
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
                        <th class="col-2">Всего действий</th>
                        <th class="col-4">Пользователи</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($result as $date => $info)
                        <tr>
                            <td data-order="{{ strtotime($date) }}">{{ $date }}</td>
                            <td>{{ $info['time'] }}</td>
                            <td>{{ $info['refreshPageCounter'] }}</td>
                            <td>{{ $info['actionsCounter'] }}</td>
                            <td>{{ $info['refreshPageCounter'] + $info['actionsCounter'] }}</td>
                            <td>
                                <button class="btn btn-default" type="button" data-toggle="collapse"
                                        data-target="#collapseExample{{ $date }}" aria-expanded="false"
                                        aria-controls="collapseExample{{ $date }}">
                                    Пользователи
                                </button>

                                <div class="collapse mt-3" id="collapseExample{{ $date }}">
                                    <table class="table table-striped no-footer border">
                                        <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Время</th>
                                            <th>Обновления страницы</th>
                                            <th>Другие действия</th>
                                            <th>Всего действий</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($info['users'] as $user)
                                            <tr>
                                                <td>{{ $user['email'] }}</td>
                                                <td>{{ $user['time'] }}</td>
                                                <td>{{ $user['refreshPageCounter'] }}</td>
                                                <td>{{ $user['actionsCounter'] }}</td>
                                                <td>{{ $user['refreshPageCounter'] + $user['actionsCounter'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card collapsed-card">
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

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse" id="chart-activator">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none">
                <canvas id="linear-chart" style="position: relative; width: 50%; min-height: 350px; max-height: 350px;"></canvas>
            </div>
        </div>

        @include('main-projects.templates.clicks_buttons_table', ['id' => $project->id, 'columns' => json_decode($project->buttons)])

        @slot('js')
            <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
            <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
            <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

            <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

            <script src="{{ asset('plugins/datatables-buttons/js/buttons.excel.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.js') }}"></script>

            <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>
            <script>
                $(document).ready(function () {
                    let startDate = null;
                    let endDate = null;
                    let lineChart = '';

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

                        axios.get('/get-data-range-module-statistics/' + {{ $project->id }}).then(function (response) {

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

                    $('#table').DataTable({
                        order: [[0, 'desc']],
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

                    $('#show-actions').on('click', function () {
                        if ($('#app > div > div > div.card-body > div:nth-child(3) > div.card-body').is(':hidden')) {
                            $('#chart-activator').trigger('click')
                        }

                        $.ajax({
                            type: "POST",
                            url: "{{ route('module.actions.history') }}",
                            data: {
                                dateRange: $('#date-range').val(),
                                projectId: {{ $project->id }}
                            },
                            success: function (response) {
                                if (lineChart !== '') {
                                    lineChart.destroy()
                                }
                                lineChart = renderChart('linear-chart', response)
                            }
                        });
                    })
                })

                function renderChart(chartId, data) {
                    let graph = document.getElementById(chartId).getContext('2d');

                    let dataLine1 = {
                        label: 'Время проведённое в модулях (мин)',
                        data: data['seconds'].map(seconds => seconds / 60),
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
                            labels: data['days'],
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

                    return new Chart(graph, chartConfig);
                }
            </script>
        @endslot
    @else
        Нет данных
    @endif
@endcomponent
