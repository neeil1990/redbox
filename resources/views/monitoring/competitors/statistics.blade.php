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

            .fa.fa-trash {
                cursor: pointer;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message"></div>
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

    <div class="d-flex flex-row mb-3 mt-3 btn-group col-4 p-0">
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.competitors', $project->id) }}">
            {{ __('My competitors') }}
        </a>
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.competitors.positions', $project->id) }}">
            {{ __('Comparison with competitors') }}
        </a>
    </div>

    <div class="row mt-5 ">
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
                                    @foreach($searchEngines as $search)
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

    <h3 class="mb-2 header" style="display: none">
        {{ __('Statistics for the selected region') }}
    </h3>

    <table id="table" class="table table-hover table-bordered no-footer">
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

    <div id="dateRange" class="mt-5">
        <h3 class="mt-3">{{ __('Project') . ' ' .  $project->name }}</h3>
        <h3>{{ __('Changes by top and date') }}</h3>
        <div class="card mt-3">
            <div class="card-header d-flex flex-row justify-content-start align-items-center">
                <div class="input-group col-5 pl-0 ml-0">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="date-range">
                    <button id="competitors-history-positions" class="btn btn-default"
                            style="border-top-left-radius: 0; border-bottom-left-radius: 0">
                        {{ __('Analyse') }}
                    </button>
                </div>
            </div>
            <div class="card-body" id="history-block">
                <table class="table table-bordered w-50">
                    <thead>
                    <tr>
                        <th>{{ __('Date range') }}</th>
                        <th>{{ __('Region') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="changeDatesTbody">
                    @if(count($project->dates) > 0)
                        @foreach($project->dates as $result)
                            <tr @if($result['state'] === 'in queue' || $result['state'] === 'in process') class="need-check"
                                data-id="{{ $result['id'] }}"
                                id="analyse-in-queue-{{ $result['id'] }}" @endif>
                                <td>{{ $result['range'] }}</td>
                                <td>
                                    @foreach($searchEngines as $engine)
                                        @if($engine['id'] == json_decode($result['request'], true)['region'])
                                            {{ strtoupper($engine['engine']) }}, {{ $engine['location']['name'] }}
                                            [{{ $engine['location']['lr'] }}]
                                            @break
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if($result['state'] === 'ready')
                                        <a class="btn btn-default"
                                           href="{{ route('monitoring.changes.dates.result', $result['id']) }}"
                                           target="_blank">{{ __('show') }}</a>
                                        <button class="btn btn-default remove-error-results"
                                                data-id="{{ $result['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @elseif($result['state'] === 'in queue')
                                        {{ __("In queue") }}
                                        <img src="/img/1485.gif" style="width: 20px; height: 20px;">
                                    @elseif($result['state'] === 'in process')
                                        {{ __("In process") }}
                                        <img src="/img/1485.gif" style="width: 20px; height: 20px;">
                                    @else
                                        {{ __('Fail') }}
                                        <button class="btn btn-default remove-error-results"
                                                data-id="{{ $result['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="empty-row">
                            <td class="text-center" colspan="3">{{ __('Empty') }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
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

                renderInfo()

                $('#searchEngines').on('change', function () {
                    let val = $(this).val()
                    localStorage.setItem('lr_redbox_monitoring_selected_filter', JSON.stringify({
                        val: val,
                    }))

                    renderInfo(true)
                })
            })

            function renderTableBody(table, data) {
                let trs = []
                $.each(data, function (query, info) {
                    let tr = []
                    tr.push(query)
                    $.each(info, function (site, visibility) {
                        if (site !== '') {
                            tr.push(visibility)
                        }
                    })

                    trs.push(tr)
                })

                table.rows.add(trs).draw()
            }

            function prepareActions() {
                colorCells()
                table.page.len(50).draw(false)
            }

            function colorCells() {
                $('.min-value').removeClass('min-value')

                let $table = $('#table');
                let $rows = $table.find('tr');

                $rows.each(function (rowIndex) {
                    if (rowIndex !== 0) {
                        let $row = $(this);
                        let $cells = $row.find('td');
                        if ($cells.length > 2) {
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
                    }
                });
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

            function renderCharts(data, destroy = false) {
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
                $('#statistics-table').hide()

                if (destroy) {
                    table.clear().draw()
                } else {
                    table = $('#table').DataTable({
                        ordering: false,
                        lengthMenu: [10, 25, 50, 100, TOTAL_WORDS],
                        pageLength: TOTAL_WORDS,
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

                let countReadyWords = 0
                let array = [];
                $('#ready-percent').html(0)

                ifIssetNotReady(KEYWORDS, countReadyWords, array, destroy, table)
            }

            function ifIssetNotReady(oldArray, countReadyWords, results, destroy, table, needSplit = false) {
                let newArray = []
                let successRequests = 0
                let failRequests = 0
                let totalRequests = oldArray.length
                $.each(oldArray, function (k, words) {
                    let array
                    if (needSplit) {
                        array = chunkArray(words, 5)
                        $.each(array, function (k, words) {
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                timeout: 60000,
                                url: "{{ route('monitoring.get.competitors.statistics') }}",
                                data: {
                                    '_token': $('meta[name="csrf-token"]').attr('content'),
                                    'competitors': {!! json_encode($competitors) !!},
                                    'region': $('#searchEngines').val(),
                                    'totalWords': TOTAL_WORDS,
                                    'projectId': PROJECT_ID,
                                    'keywords': words,
                                },
                                success: function (response) {
                                    renderTableBody(table, response.visibility)
                                    successRequests++
                                    countReadyWords += words.length
                                    $('#ready-percent').html(Number(countReadyWords / TOTAL_WORDS * 100).toFixed())
                                    results.push(response.statistics)
                                },
                                error: function () {
                                    failRequests++
                                    newArray.push(words)
                                }
                            })
                        })

                    } else {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            timeout: 100,
                            url: "{{ route('monitoring.get.competitors.statistics') }}",
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'competitors': {!! json_encode($competitors) !!},
                                'region': $('#searchEngines').val(),
                                'totalWords': TOTAL_WORDS,
                                'projectId': PROJECT_ID,
                                'keywords': words,
                            },
                            success: function (response) {
                                renderTableBody(table, response.visibility)
                                successRequests++
                                countReadyWords += words.length
                                $('#ready-percent').html(Number(countReadyWords / TOTAL_WORDS * 100).toFixed())
                                results.push(response.statistics)
                            },
                            error: function () {
                                failRequests++
                                newArray.push(words)
                            }
                        })
                    }
                });

                let interval = setInterval(() => {
                    if (totalRequests === successRequests + failRequests) {
                        clearInterval(interval)
                        if (failRequests === 0) {
                            $('#download-results').hide()
                            prepareActions();
                            renderStatistics(calculateAvgValues(results), destroy)
                        } else {
                            $('#toast-container').show(300)
                            $('.toast-message').html("{{ __('Data could not be retrieved, the request was duplicated') }}")
                            setTimeout(() => {
                                $('#toast-container').hide(300)
                            }, 5000)

                            ifIssetNotReady(newArray, countReadyWords, results, destroy, table, true)
                        }
                    }
                }, 1000)
            }


            function chunkArray(arr, n) {
                let chunkLength = Math.max(arr.length / n, 1);
                let chunks = [];
                for (let i = 0; i < n; i++) {
                    if (chunkLength * (i + 1) <= arr.length) chunks.push(arr.slice(chunkLength * i, chunkLength * (i + 1)));
                }
                return chunks;
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

            let range = $('#date-range');
            range.daterangepicker({
                opens: 'left',
                startDate: moment().subtract(30, 'days'),
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

            removeErrorResults()
            needCheck()

            function removeErrorResults() {
                $('.remove-error-results').unbind().on('click', function () {
                    let bool = confirm('Подтвердите удаление результатов')
                    if (bool) {
                        let $elem = $(this)
                        $.ajax({
                            type: "POST",
                            url: "{{ route('monitoring.changes.dates.remove') }}",
                            data: {
                                'id': $(this).attr('data-id'),
                            },
                            success: function () {
                                $elem.parents().eq(1).remove()
                            }
                        })
                    }
                })
            }

            function needCheck() {
                $.each($('.need-check'), function (k, v) {
                    waitFinishAnalyse($(this).attr('data-id'))
                })
            }

            function waitFinishAnalyse(recordId) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('monitoring.changes.dates.check') }}",
                    data: {
                        'id': recordId,
                    },
                    success: function (response) {
                        if (response.state === 'ready') {
                            $('#analyse-in-queue-' + recordId).children('td').eq(2).html(
                                '<a class="btn btn-default" href="/monitoring/competitors/result-analyse/' + recordId + '" target="_blank">{{ __('show') }}</a>' +
                                ' <button class="btn btn-default remove-error-results" data-id="' + recordId + '">' +
                                '    <i class="fa fa-trash"></i>' +
                                '</button>'
                            )

                            removeErrorResults()
                        } else if (response.state === 'in process') {
                            $('#analyse-in-queue-' + recordId).children('td').eq(2).html("{{ __('In process') }}" + ' <img src="/img/1485.gif" style="width: 20px; height: 20px;">')
                            setTimeout(() => {
                                waitFinishAnalyse(recordId)
                            }, 10000)
                        } else if (response.state === 'fail') {
                            $('#analyse-in-queue-' + recordId).children('td').eq(2).html("{{ __('Fail') }}" +
                                '<button class="btn btn-default remove-error-results" data-id="' + recordId + '">' +
                                '    <i class="fa fa-trash"></i>' +
                                '</button>')
                            removeErrorResults()
                        } else {
                            setTimeout(() => {
                                waitFinishAnalyse(recordId)
                            }, 10000)
                        }
                    },
                })
            }
        </script>

    @endslot
@endcomponent
