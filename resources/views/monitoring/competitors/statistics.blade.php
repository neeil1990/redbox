@component('component.card', ['title' => __('Project') . " $project->name" ])
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
            .custom-info-bg {
                background-color: rgba(23, 162, 184, 0.5) !important;
            }

            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }

            .chart-container {
                width: 100%;
                height: 527px;
            }

            #history-block > table > thead > tr:nth-child(1) > th:nth-child(2),
            #history-block > table > thead > tr:nth-child(1) > th:nth-child(3),
            #history-block > table > thead > tr:nth-child(1) > th:nth-child(4),
            #history-block > table > thead > tr:nth-child(1) > th:nth-child(5) {
                text-align: center;
            }

            .grow-color {
                background-color: rgb(153, 228, 185);
            }

            .shrink-color {
                background-color: rgb(251, 225, 223);
            }

            table {
                width: 100%;
                border-collapse: separate !important;
                table-layout: fixed;
                border-spacing: 0 !important;
            }

            thead {
                position: sticky;
                top: 0;
                background-color: white;
            }

            #avg-position_wrapper,
            #top3_wrapper,
            #top10_wrapper,
            #top100_wrapper {
                width: 50%;
            }

            #avg-position,
            #top3,
            #top10,
            #top100 {
                width: 100% !important;
            }

            #tableHeadRow th {
                min-width: 100px;
                max-width: 100px;
            }

            .min-value {
                background-color: rgb(153, 228, 185);
            }

            #tableHeadRow th,
            #tableBody td {
                width: 150px;
                min-width: 150px;
                max-width: 150px;
            }

            #history-results td {
                width: 100px !important;
                min-width: 100px !important;
                max-width: 100px !important;
            }

            #table_wrapper > div:nth-child(2) > div {
                overflow: auto;
                width: 100%;
                max-height: 950px;
            }

            #history-results_wrapper > div:nth-child(2) > div {
                overflow: auto;
                width: 100%;
                max-height: 950px;
            }

            #history-results {
                width: auto;
            }

            .percentage-block {
                text-align: center;
                justify-content: center;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('Filter applied') }}</div>
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Region filter') }}</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-start align-items-center">
                        <div class="col-4 mr-3">
                            <div class="form-group">
                                <label>{{ __('Search engine') }}:</label>
                                <select name="region" class="custom-select" id="searchEngines">
                                    @foreach($project->searchengines as $search)
                                        @if($search->id == request('region'))
                                            <option value="{{ $search->id }}"
                                                    selected>{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                                [{{$search->lr}}]
                                            </option>
                                        @else
                                            <option
                                                value="{{ $search->id }}">{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                                [{{$search->lr}}]
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="download-results">
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="/img/1485.gif" style="width: 40px; height: 40px;">
                            </div>
                            <div class="d-flex percentage-block">
                                <div id="ready-percent">0</div>
                                %
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mt-5 mb-2">
        {{ __('Statistics for the selected region') }}
    </h4>

    <table id="table" class="table table-hover table-bordered no-footer" style="display: none">
        <thead>
        <tr id="tableHeadRow">
            <th>{{ __('Query') }}</th>
            @foreach($competitors as $key => $competitor)
                <th> {{ $competitor }}
                    <span class="remove-competitor ml-1" data-target="{{ $competitor }}" data-id="{{ $key }}">
                        <i class="fa fa-trash"></i>
                     </span>
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody id="tableBody">
        </tbody>
    </table>

    <div id="statistics-table" class="mt-5" style="display: none">
        <div class="d-flex flex-column">
            <button class="btn btn-outline-secondary col-2 mb-2 collapsed chart-button" type="button"
                    data-toggle="collapse" data-target="#avgCollapse"
                    aria-expanded="false" aria-controls="avgCollapse">
                {{ __('Average position') }}
            </button>
            <div id="avgCollapse" class="collapse">
                <div class="d-flex align-items-start mt-5">
                    <div class="chart-container">
                        <canvas id="bar-chart"></canvas>
                    </div>
                    <table class="table table-hover table-bordered w-50" id="avg-position">
                        <thead>
                        <tr>
                            <th>{{ __('Domain') }}</th>
                            <th>{{ __('Average position') }}</th>
                        </tr>
                        </thead>
                        <tbody id="avg-position-tbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <button class="btn btn-outline-secondary col-2 mb-2 collapsed chart-button" type="button"
                    data-toggle="collapse" data-target="#top3Collapse"
                    aria-expanded="false" aria-controls="top3Collapse">
                {{ __('Percentage of getting into the top') }} 3
            </button>
            <div id="top3Collapse" class="collapse">
                <div class="d-flex align-items-start mt-5">
                    <div class="chart-container">
                        <canvas id="bar-chart-3"></canvas>
                    </div>
                    <table class="table table-hover table-bordered w-50" id="top3">
                        <thead>
                        <tr>
                            <th>{{ __('Domain') }}</th>
                            <th>{{ __('Percentage of getting into the top') }} 3</th>
                        </tr>
                        </thead>
                        <tbody id="top3-tbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <button class="btn btn-outline-secondary col-2 mb-2 collapsed chart-button" type="button"
                    data-toggle="collapse" data-target="#top10Collapse"
                    aria-expanded="false" aria-controls="top10Collapse">
                {{ __('Percentage of getting into the top') }} 10
            </button>
            <div id="top10Collapse" class="collapse">
                <div class="d-flex align-items-start mt-5">
                    <div class="chart-container">
                        <canvas id="bar-chart-10"></canvas>
                    </div>
                    <table class="table table-hover table-bordered w-50" id="top10">
                        <thead>
                        <tr>
                            <th>{{ __('Domain') }}</th>
                            <th>{{ __('Percentage of getting into the top') }} 10</th>
                        </tr>
                        </thead>
                        <tbody id="top10-tbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <button class="btn btn-outline-secondary col-2 mb-2 collapsed chart-button" type="button"
                    data-toggle="collapse" data-target="#top100Collapse"
                    aria-expanded="false" aria-controls="top100Collapse">
                {{ __('Percentage of getting into the top') }} 100
            </button>
            <div id="top100Collapse" class="collapse">
                <div class="d-flex align-items-start mt-5">
                    <div class="chart-container">
                        <canvas id="bar-chart-100"></canvas>
                    </div>
                    <table class="table table-hover table-bordered w-50" id="top100">
                        <thead>
                        <tr>
                            <th>{{ __('Domain') }}</th>
                            <th>{{ __('Percentage of getting into the top') }} 100</th>
                        </tr>
                        </thead>
                        <tbody id="top100-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="dateRange" style="display: none">
        <h3 class="mt-3">{{ __('Changes by top and date') }}</h3>
        <div class="card mt-3">
            <div class="card-header d-flex flex-row justify-content-start align-items-center">
                <div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                              </span>
                        </div>
                        <input type="text" class="form-control" id="date-range">
                        <button id="competitors-history-positions" class="btn btn-default"
                                style="border-top-left-radius: 0; border-bottom-left-radius: 0">
                            {{ __('show') }}
                        </button>
                    </div>
                </div>
                <div id="story-preloader" style="display: none" class="ml-2">
                    <div class="d-flex justify-content-center align-items-center">
                        <img src="/img/1485.gif" style="width: 40px; height: 40px;">
                    </div>
                    <div class="d-flex percentage-block">
                        <div id="percent-date-request"></div>
                        %
                    </div>
                </div>
            </div>
            <div class="card-body" id="history-block">
                <div class="mb-2 btn-group" id="visibility-buttons" style="display: none">
                    <button data-action="hide" data-order="0" class="btn btn-default btn-sm column-visible">
                        {{ __('Domain') }}
                    </button>
                    <button data-action="hide" class="btn btn-default btn-sm column-visible add-order">
                        {{ __('Average position') }}
                    </button>
                    <button data-action="hide" class="btn btn-default btn-sm column-visible add-order">
                        {{ __('Top') }} 3
                    </button>
                    <button data-action="hide" class="btn btn-default btn-sm column-visible add-order">
                        {{ __('Top') }} 10
                    </button>
                    <button data-action="hide" class="btn btn-default btn-sm column-visible add-order">
                        {{ __('Top') }} 100
                    </button>
                    <button data-action="off" class="btn btn-default btn-sm" id="switch-color">
                        {{ __('Turn off the coloring') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <!-- Charts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"
                integrity="sha512-a+mx2C3JS6qqBZMZhSI5LpWv8/4UK21XihyLKaFoSbiKQs/3yRdtqCwGuWZGwHKc5amlNN8Y7JlqnWQ6N/MYgA=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- InputMask -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script>
            let historyTable
            let table
            let chartAvg
            let chart3
            let chart10
            let chart100

            const PROJECT_ID = {{ $project->id }};
            const REGION_ID = '{{ request('region', null) }}';
            const KEYWORDS = {!! $keywords !!};
            const TOTAL_WORDS = {{ $totalWords }};

            $(document).ready(function () {
                let filter = localStorage.getItem('lr_redbox_monitoring_selected_filter')
                if (filter !== null) {
                    filter = JSON.parse(filter)
                    $('#searchEngines option[value=' + filter.val + ']').attr('selected', 'selected')
                }
                renderInfo()

                $('#searchEngines').on('change', function () {
                    let val = $(this).val()
                    localStorage.setItem('lr_redbox_monitoring_selected_filter', JSON.stringify({
                        val: val,
                    }))

                    renderInfo(true)
                })

                $('#competitors-history-positions').unbind().on('click', function () {
                    $('#percent-date-request').html(0)
                    let dates = ($('#date-range').val()).split(' - ')
                    let ajaxRequests = []
                    let results = {}
                    dates = getDates(dates[0], dates[1])
                    let totalRequests = dates.length
                    let countRequest = 0

                    $.each(dates, function (k, date) {
                        ajaxRequests.push($.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('monitoring.competitors.history.positions') }}",
                            data: {
                                'projectId': PROJECT_ID,
                                'region': $('#searchEngines').val(),
                                'date': date,
                            },
                            beforeSend: function () {
                                $('#story-preloader').show(300)
                            },
                            success: function (response) {
                                countRequest++;
                                $('#percent-date-request').html(Number(countRequest / totalRequests * 100).toFixed())
                                if (response.data.length !== 0) {
                                    results[date] = response.data
                                }
                            },
                        }));
                    });

                    $.when.apply($, ajaxRequests).done(function () {
                        if ($.fn.DataTable.fnIsDataTable($('#history-results'))) {
                            $('#history-results').dataTable().fnDestroy()
                        }
                        $('#history-results').remove()

                        renderHistoryPositions(results)
                        $('#story-preloader').hide(300)
                    });
                })

                $('#dateRange').show()
            })

            function getDates($start, $end) {
                let start = $start.split('-')
                const startDate = new Date();
                startDate.setDate(start[0]);
                startDate.setMonth(start[1]);
                startDate.setFullYear(start[2]);

                let end = $end.split('-')
                const endDate = new Date();
                endDate.setDate(end[0]);
                endDate.setMonth(end[1]);
                endDate.setFullYear(end[2]);

                let dates = []
                while (startDate <= endDate) {
                    let day = startDate.getDate() < 10 ? '0' + startDate.getDate() : startDate.getDate()
                    let month = startDate.getMonth() < 10 ? '0' + startDate.getMonth() : startDate.getMonth()

                    dates.push(day + '-' + (month) + '-' + startDate.getFullYear())
                    startDate.setDate(startDate.getDate() + 1);
                }

                return dates;
            }

            function renderTableBody(data) {
                let trs = []
                $.each(data, function (query, info) {
                    let tr = '<tr class="render"><td>' + query + '</td>'
                    $.each(info, function (site, visibility) {
                        if (site !== '') {
                            tr += '<td>' + visibility + '</td>'
                        }
                    })
                    tr += '</tr>'

                    trs.push(tr)
                })
                $('#tableBody').append(trs.join(' '))
            }

            function initTable() {
                let $table = $('#table');
                let $rows = $table.find('tr');

                $rows.each(function (rowIndex) {
                    if (rowIndex !== 0) {
                        let $row = $(this);
                        let $cells = $row.find('td');

                        let array = []
                        $cells.each(function (cellIndex) {
                            let $cell = $(this);
                            let cellVal = parseFloat($cell.text());

                            if (!isNaN(cellVal) && cellVal !== 0 && cellIndex !== 0) {
                                array.push({
                                    cellIndex: cellIndex + 1,
                                    cellVal: cellVal
                                })
                            }
                        });

                        if (array.length > 0) {
                            array.sort((prev, next) => prev.cellVal - next.cellVal);
                            $('#tableBody > tr:nth-child(' + rowIndex + ') > td:nth-child(' + array[0]['cellIndex'] + ')').addClass('min-value');
                        }
                    }
                });
                let res = $('#table').DataTable({
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 50,
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

                $('.remove-competitor').unbind().on('click', function () {
                    let columnIndex = $(this).attr('data-id')
                    let url = $(this).attr('data-target')

                    if (confirm(`{{ __('Are you going to remove the domain') }} "${url}" {{ __('from competitors') }}`)) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('monitoring.remove.competitor') }}",
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'url': url,
                                'projectId': PROJECT_ID
                            },
                            success: function (response) {
                                table.column(columnIndex).visible(false)
                            },
                        });
                    }
                })

                $('#tableHeadRow > th:nth-of-type(2)').addClass('custom-info-bg')
                $('#tableHeadRow > th:nth-of-type(2) > .remove-competitor').remove()

                $('#table').show()
                $('h4.mt-5.mb-2').show()

                return res;
            }

            function renderChartTable(tableId, body, data, key, sortType = 'desc') {
                if ($.fn.DataTable.fnIsDataTable($(tableId))) {
                    $(tableId).dataTable().fnDestroy();
                    $(tableId + ' .render-more').remove();
                }

                let rows = ''
                $.each(data, function (domain, values) {
                    rows += '<tr class="render-more">'
                    rows += '<td>' + domain + '</td>'
                    rows += '<td>' + String(values[key]).substring(0, 5) + '</td></tr>'
                })
                $(body).html(rows)

                $(tableId).DataTable({
                    order: [[1, sortType]],
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
            }

            function renderCharts(data, destroy) {
                let colorArray = getColorArray()

                let labels = []
                let avg = []
                let reverseDatas = []
                let top3 = []
                let top10 = []
                let top100 = []
                let colors = []

                $.each(data, function (domain, info) {
                    if (domain !== "") {
                        labels.push(domain)
                        avg.push(info.avg)
                        reverseDatas.push(100 - info.avg)
                        colors.push(colorArray.shift())
                        top3.push(info.top_3)
                        top10.push(info.top_10)
                        top100.push(info.top_100)
                    }
                })

                if (destroy) {
                    chartAvg.destroy()
                    chart3.destroy()
                    chart10.destroy()
                    chart100.destroy()
                }

                chartAvg = new Chart($('#bar-chart'), {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                data: avg,
                                backgroundColor: "transparent"
                            },
                            {
                                data: reverseDatas,
                                backgroundColor: colors
                            }
                        ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: "{{ __("Average position") }}"
                        },
                        scales: {
                            xAxes: [
                                {
                                    stacked: true,
                                    ticks: {
                                        maxRotation: 60,
                                        minRotation: 60,
                                        fontSize: 12
                                    }
                                },
                            ],
                            yAxes: [
                                {
                                    stacked: true,
                                    ticks: {
                                        reverse: true,
                                        beginAtZero: true,
                                        stepSize: 10,
                                        max: 100,
                                        min: 0
                                    }
                                }
                            ]
                        },
                        tooltips: {
                            callbacks: {
                                title: function (item, everything) {
                                    return item[0].xLabel;
                                },
                                label: function (item, everything) {
                                    if (item.datasetIndex === 1) {
                                        return "{{ __("Average position") }} " + String(100 - item.yLabel).substring(0, 5);
                                    }

                                    return 'Нужно подняться на ' + 100 - item.yLabel;

                                }
                            }
                        },
                        legend: {
                            display: false
                        },
                        maintainAspectRatio: false,
                    }
                });

                chart3 = renderChart(labels, colors, top3, '#bar-chart-3', "{{ __('Percentage of getting into the top') }} 3")
                chart10 = renderChart(labels, colors, top10, '#bar-chart-10', "{{ __('Percentage of getting into the top') }} 10")
                chart100 = renderChart(labels, colors, top100, '#bar-chart-100', "{{ __('Percentage of getting into the top') }} 100")
            }

            function renderChart(labels, colors, data, target, label) {
                return new Chart($(target), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: label
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    maxRotation: 60,
                                    minRotation: 60,
                                    fontSize: 12
                                }
                            }]
                        },
                        legend: {
                            display: false
                        },
                        maintainAspectRatio: false,
                    }
                });
            }

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

            function renderInfo(destroy = false) {
                $('#download-results').show()
                $('#table').hide()
                $('#statistics-table').hide()
                $('h4.mt-5.mb-2').hide()

                if ($.fn.DataTable.fnIsDataTable($('#table'))) {
                    $('#table').dataTable().fnDestroy();
                    $('.render').remove()
                }

                let countReadyWords = 0
                let ajaxRequests = []
                let array = [];
                $('#ready-percent').html(0)
                $.each(KEYWORDS, function (k, words) {
                    ajaxRequests.push($.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('monitoring.get.competitors.statistics') }}",
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'projectId': PROJECT_ID,
                            'region': $('#searchEngines').val(),
                            'keywords': words,
                            'competitors': {!! json_encode($competitors) !!},
                            'totalWords':TOTAL_WORDS
                        },
                        success: function (response) {
                            countReadyWords += words.length
                            $('#ready-percent').html(Number(countReadyWords /TOTAL_WORDS * 100).toFixed())
                            renderTableBody(response.visibility)
                            array.push(response.statistics)
                        },
                    }));
                });

                $.when.apply($, ajaxRequests).done(function () {
                    $('#download-results').hide()
                    table = initTable();

                    let results = calculateAvgValues(array)
                    renderStatistics(results, destroy)
                });
            }

            function calculateAvgValues(array) {
                let domains = []
                let results = {}

                $.each(array, function (key, values) {
                    $.each(values, function (domain, info) {
                        domains.push(domain)
                    })
                    return false;
                })

                $.each(domains, function (k, v) {
                    results[v] = {'avg': 0, 'top_3': 0, 'top_10': 0, 'top_100': 0, 'sum': 0}
                })

                for (let i = 0; i < array.length; i++) {
                    $.each(domains, function (k, v) {
                        results[v]['sum'] += array[i][v]['sum']
                        results[v]['top_3'] += array[i][v]['top_3']
                        results[v]['top_10'] += array[i][v]['top_10']
                        results[v]['top_100'] += array[i][v]['top_100']
                    })
                }

                $.each(results, function (k, v) {
                    results[k]['avg'] = results[k]['sum'] / Number(TOTAL_WORDS)
                    results[k]['top_3'] = (results[k]['top_3'] / Number(TOTAL_WORDS)) * 100
                    results[k]['top_10'] = (results[k]['top_10'] / Number(TOTAL_WORDS)) * 100
                    results[k]['top_100'] = (results[k]['top_100'] / Number(TOTAL_WORDS)) * 100
                })

                return results;
            }

            function renderHistoryPositions(data) {
                let result
                if (data && Object.keys(data).length === 0 && Object.getPrototypeOf(data) === Object.prototype) {
                    result = '<b id="history-results">{{ __('There are no results') }}</b>'
                    $('#history-block').append(result)
                    $('#visibility-buttons').hide()
                } else {
                    const sortedKeys = Object.keys(data).sort((a, b) => new Date(a.split('-').reverse().join('-')) - new Date(b.split('-').reverse().join('-')));
                    const sortedData = {};
                    let length = 0
                    sortedKeys.forEach(key => {
                        sortedData[key] = data[key];
                        length++;
                    });

                    let domains = []
                    let dates = []

                    let bottomHead = ''
                    $.each(sortedData, function (k, v) {
                        bottomHead += '<td>' + k + '</td>'
                        dates.push(k)
                        $.each(v, function (k1, v1) {
                            domains.push(k1)
                        })
                    })

                    domains = getUniqueValues(domains)
                    dates = getUniqueValues(dates)

                    let keys = ['avg', 'top_3', 'top_10', 'top_100']
                    let trs = ''
                    $.each(domains, function (k, domain) {
                        trs += '<tr><td>' + domain + '</td>'
                        $.each(keys, function (key, name) {
                            $.each(dates, function (k1, date) {
                                let firstElement = k1 === 0;
                                if (firstElement) {
                                    trs += '<td style="border-left: 2px solid grey; box-sizing: border-box;">' + data[date][domain][name] + '</td>'
                                } else {
                                    trs += '<td>' + data[date][domain][name] + '</td>'
                                }
                            })
                        })
                        trs += "</tr>"
                    })

                    result =
                        '<table class="table table-hover table-bordered" id="history-results">' +
                        '    <thead>' +
                        '        <tr>' +
                        '            <th class="text-center">{{ __('Domain') }}</th>' +
                        '            <th colspan="' + length + '" class="text-center">{{ __('Average position') }}</th>' +
                        '            <th colspan="' + length + '" class="text-center">{{ __('Percentage of getting into the top') }} 3</th>' +
                        '            <th colspan="' + length + '" class="text-center">{{ __('Percentage of getting into the top') }} 10</th>' +
                        '            <th colspan="' + length + '" class="text-center">{{ __('Percentage of getting into the top') }} 100</th>' +
                        '        </tr>' +
                        '        <tr><td></td>' +
                        bottomHead +
                        bottomHead +
                        bottomHead +
                        bottomHead +
                        '        </tr>' +
                        '    </thead>' +
                        '    <tbody>' + trs + '</tbody>' +
                        '</table>'

                    $('#history-block').append(result)

                    historyTable = $('#history-results').DataTable({
                        bAutoWidth: false,
                        bSort: false,
                        lengthMenu: [10, 25, 50, 100],
                        pageLength: 50,
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

                    $.each($('#history-results > tbody > tr'), function (k, v) {
                        for (let j = 0; j < 4; j++) {
                            let res = length * j
                            let bool = j === 0
                            colorCells($(this), 1 + res, length * (j + 1), bool)
                        }
                    })

                    setDataOrders(length)

                    $('.column-visible').unbind().on('click', function () {
                        if ($(this).attr('data-action') === 'hide') {
                            $(this).attr('data-action', 'show')
                            historyTable.columns(String($(this).attr('data-order')).split(',')).visible(false);
                        } else {
                            String($(this).attr('data-order')).split(',')
                            $(this).attr('data-action', 'hide')
                            historyTable.columns(String($(this).attr('data-order')).split(',')).visible(true);
                        }

                        $('#table').css({
                            'width': '100%'
                        })
                    })

                    $('#switch-color').unbind().on('click', function () {
                        let action = $(this).attr('data-action')

                        if (action === 'off') {
                            $(this).text("{{ __("Turn on the coloring") }}")
                            $(this).attr('data-action', 'on')
                            $('.grow-color').addClass('grow-color-hide').removeClass('grow-color')
                            $('.shrink-color').addClass('shrink-color-hide').removeClass('shrink-color')
                        } else {
                            $(this).text("{{ __('Turn off the coloring') }}")
                            $(this).attr('data-action', 'off')
                            $('.grow-color-hide').addClass('grow-color').removeClass('grow-color-hide')
                            $('.shrink-color-hide').addClass('shrink-color').removeClass('shrink-color-hide')
                        }
                    })

                    $('#visibility-buttons').show()
                }
            }

            function colorCells(elem, start, end, inverse) {
                for (let i = end; i > start; i--) {
                    let targetElement = elem.children('td').eq(i)
                    let beforeElement = elem.children('td').eq(i - 1)

                    let result = Number(targetElement.text()) - Number(beforeElement.text())
                    if (result !== 0) {

                        let substring = String(result).substring(0, 5)
                        if (inverse) {
                            if (result > 0) {
                                targetElement.addClass('shrink-color')
                                targetElement.text(targetElement.text() + ' (+' + substring + ')')
                            } else {
                                targetElement.addClass('grow-color')
                                targetElement.text(targetElement.text() + ' (' + substring + ')')
                            }
                        } else {
                            if (result > 0) {
                                targetElement.addClass('grow-color')
                                targetElement.text(targetElement.text() + ' (+' + substring + ')')
                            } else {
                                targetElement.addClass('shrink-color')
                                targetElement.text(targetElement.text() + ' (' + substring + ')')
                            }
                        }
                    }
                }
            }

            function renderStatistics(data, destroy) {
                renderChartTable('#avg-position', '#avg-position-tbody', data, 'avg', 'asc')
                renderChartTable('#top3', '#top3-tbody', data, 'top_3')
                renderChartTable('#top10', '#top10-tbody', data, 'top_10')
                renderChartTable('#top100', '#top100-tbody', data, 'top_100')

                $('#statistics-table').show()
                renderCharts(data, destroy)
            }

            function setDataOrders(length) {
                let buttonCounter = 1;
                $.each($('.add-order'), function (k, v) {
                    let orders = buttonCounter
                    for (let i = 1; i < length; i++) {
                        buttonCounter++
                        orders += ',' + buttonCounter
                    }
                    $(this).attr('data-order', orders)
                    buttonCounter++
                })
            }

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
                })
            });

            function getUniqueValues(data) {
                data = new Set([...data])

                return [...data]
            }

            function checkChartState(id) {
                let avg = localStorage.getItem('lk_redbox_button_' + id) ?? 'false'
                if (avg === 'false') {
                    $("button[data-target='" + id + "']").trigger('click')
                }
            }

            checkChartState('#avgCollapse')
            checkChartState('#top3Collapse')
            checkChartState('#top10Collapse')
            checkChartState('#top100Collapse')

            $('.chart-button').on('click', function () {
                setTimeout(() => {
                    localStorage.setItem('lk_redbox_button_' + $(this).attr('data-target'), $(this).hasClass('collapsed'))
                }, 300)
            })
        </script>
    @endslot
@endcomponent
