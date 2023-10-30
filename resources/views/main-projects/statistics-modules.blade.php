@component('component.card', ['title' => __('General statistics modules')])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet"
              href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>

        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
        <style>
            .dt-buttons {
                float: left;
            }

            #statistics > thead > tr.filters > th:nth-child(8) > input,
            #statistics_info,
            #statistics_paginate {
                display: none;
            }

            #statistics_length {
                float: left;
            }
        </style>
    @endslot

    <table id="statistics" class="table table-striped border">
        <thead>
        <tr>
            <th>Модуль</th>
            <th>Ссылка на статистику модуля</th>
            <th>Цвет</th>
            <th>Количество действий</th>
            <th>Количество обновлений страницы</th>
            <th>Время проведённое в модуле</th>
        </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            <tr>
                <td>
                    <a href="{{ $project['link'] }}" target="_blank">{{ __($project['title']) }}</a>
                </td>
                <td>
                    <a class="btn btn-default" href="{{ route('main-projects.statistics', $project['id'])}}"
                       target="_blank">Статистика</a>
                </td>
                <td>
                    <div
                        style="height: 25px; width: 25px; border-radius: 40px; background-color: {{ $project['color'] }}"></div>
                </td>
                <td>{{ $project['statistics']['actions_counter'] }}</td>
                <td>{{ $project['statistics']['refresh_page_counter'] }}</td>
                <td>{{ Carbon::now()->addSeconds($project['statistics']['seconds'])->diff(Carbon::now())->format('%H:%I:%S') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="card mt-5">
        <div class="card-header d-flex p-0">
            <div class="p-3" style="width: 60% !important;">
                <div class="d-flex">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" id="date-range" class="form-control float-right">
                    </div>
                    <select id="step" class="custom-select">
                        <option value="day">День</option>
                        <option value="week">Неделя</option>
                        <option value="month">Месяц</option>
                    </select>
                    <select id="action" class="custom-select">
                        <option value="actions_counter">Действия</option>
                        <option value="refresh_page_counter">Обновления страниц</option>
                        <option value="seconds">Время проведённое в модулях</option>
                    </select>
                    <button class="btn btn-secondary" id="get-statistics">Статистика</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="bar-chart-grouped" style="width: 0"></canvas>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.excel.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.js') }}"></script>

        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>
        <script>
            let chart = undefined
            let range = $('#date-range');
            range.daterangepicker({
                opens: 'left',
                startDate: moment().subtract(7, 'days'),
                endDate: moment(),
                ranges: {
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Последние 60 дней': [moment().subtract(59, 'days'), moment()],
                    'Последние 100 дней': [moment().subtract(99, 'days'), moment()],
                    'Последние 200 дней': [moment().subtract(199, 'days'), moment()],
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
            });

            $('#get-statistics').on('click', function () {
                $.ajax({
                    type: "POST",
                    url: "{{ route('get.statistics.modules') }}",
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        dateRange: $('#date-range').val(),
                        action: $('#action').val(),
                        step: $('#step').val()
                    },
                    success: function (response) {
                        if (chart !== undefined) {
                            chart.destroy()
                        }

                        $('#bar-chart-grouped').css({
                            width: '100%',
                        })

                        if ($('#action').val() === 'seconds') {
                            chart = new Chart(document.getElementById("bar-chart-grouped"), {
                                type: 'line',
                                data: {
                                    labels: response.dates,
                                    datasets: response.datasets
                                },
                                options: {
                                    scales: {
                                        y: {
                                            type: 'linear',
                                            position: 'bottom',
                                            ticks: {
                                                callback: function (value, index, values) {
                                                    let hours = Math.floor(value / 3600);
                                                    let minutes = Math.floor((value % 3600) / 60);
                                                    let seconds = value % 60;
                                                    hours = hours <= 9 ? '0' + hours : hours;
                                                    minutes = minutes <= 9 ? '0' + minutes : minutes;
                                                    seconds = seconds <= 9 ? '0' + seconds : seconds;
                                                    return `${hours}:${minutes}:${seconds}`;
                                                }
                                            }
                                        },
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function (context) {
                                                    const seconds = context.parsed.y;
                                                    return convertToHHMMSS(context, seconds);
                                                },
                                                footer: function () {
                                                    return 'часы:минуты:секунды'
                                                }
                                            },
                                        },
                                    },
                                }
                            });
                        } else {
                            chart = new Chart(document.getElementById("bar-chart-grouped"), {
                                type: 'line',
                                data: {
                                    labels: response.dates,
                                    datasets: response.datasets
                                },
                                options: {
                                    scales: {
                                        y: {
                                            type: 'linear',
                                            position: 'bottom',
                                        }
                                    },
                                }
                            });
                        }
                    },
                    error: function (response) {
                    }
                });
            });

            function convertToHHMMSS(context, totalSeconds) {
                let hours = Math.floor(totalSeconds / 3600);
                let minutes = Math.floor((totalSeconds % 3600) / 60);
                let seconds = totalSeconds % 60;

                hours = hours <= 9 ? '0' + hours : hours
                minutes = minutes <= 9 ? '0' + minutes : minutes
                seconds = seconds <= 9 ? '0' + seconds : seconds

                return context.dataset.label + ` ${hours}:${minutes}:${seconds}`;
            }
        </script>
        <script>
            $('#statistics').DataTable({
                lengthMenu: [100],
                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                ],
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
                drawCallback: function () {
                    $('#statistics_length > label > select').addClass('custom-select custom-select-sm form-control form-control-sm')
                    $('#statistics_filter > label > input[type=search]').addClass('form-control form-control-sm')

                }
            })
        </script>
    @endslot
@endcomponent
