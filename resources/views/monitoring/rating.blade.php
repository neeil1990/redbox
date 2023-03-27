@component('component.card', ['title' => __('Project') . " $project->name" ])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <style>
            .custom-info-bg {
                background-color: rgba(23, 162, 184, 0.5) !important;
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
                        <h3>{{ $navigation['h3'] }}</h3>
                        <p>{{ $navigation['p'] }}</p>
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
                    <form action="" style="display: contents;">
                        <div class="w-25">
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
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-3 mr-3">
        {{  __('Project') . " $project->name" }}
    </h3>

    <h4>
        {{ __('Count competitors') }}: {{ count($competitors) }}
    </h4>


    <div class="d-flex justify-content-center align-items-center align-content-center">
        <img src="/img/1485.gif" style="width: 50px; height: 50px;" id="preloader">
    </div>

    <table id="table" class="table table-hover table-bordered no-footer" style="display: none">
        <thead>
        <tr id="tableHeadRow">
            <th>{{ __('Query') }}</th>
        </tr>
        </thead>
        <tbody id="tableBody">
        </tbody>
    </table>

    <div class="card direct-chat direct-chat-primary collapsed-card mt-3">
        <div class="card-header ui-sortable-handle">
            <button id="more-info" class="btn btn-default"
                    data-card-widget="collapse">{{ __('Positions competitors') }}</button>
        </div>
        <div class="card-body" style="display: none;">
            <div class="d-flex justify-content-center align-items-center align-content-center">
                <img src="/img/1485.gif" style="width: 50px; height: 50px;" id="preloader-competitors"
                     class="mt-3 mb-3">
            </div>
            <table class="table table-hover table-bordered no-footer" id="position-table" style="display: none">
                <thead>
                <tr>
                    <th>{{ __('Domains') }}</th>
                    <th>{{ __('Positions') }}</th>
                    <th>{{ __('Average position') }}</th>
                    <th>{{ __('Top') }} 3</th>
                    <th>{{ __('Top') }} 10</th>
                    <th>{{ __('Top') }} 100</th>
                </tr>
                </thead>
                <tbody id="more-info-tbody">

                </tbody>
            </table>

            <div class="card-footer d-flex flex-column">
                <div class="d-flex flex-row mt-3">
                    <div class="col-6">
                        <canvas id="bar-chart"></canvas>
                    </div>
                    <div class="col-6">
                        <canvas id="bar-chart-3"></canvas>
                    </div>
                </div>
                <div class="d-flex flex-row mt-3">
                    <div class="col-6">
                        <canvas id="bar-chart-10"></canvas>
                    </div>
                    <div class="col-6">
                        <canvas id="bar-chart-100"></canvas>
                    </div>
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
        <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>
        <script src="{{ asset('plugins/chart.js/3.9.1/plugins/chartjs-plugin-crosshair.js') }}"></script>
        <script src="{{ asset('plugins/chart.js/3.9.1/plugins/chartjs-plugin-datalabels.js') }}"></script>

        <script>
            let table

            $(document).ready(function () {
                let filter = localStorage.getItem('lr_redbox_monitoring_selected_filter')

                if (filter !== null) {
                    filter = JSON.parse(filter)
                    $('#searchEngines option[value=' + filter.val + ']').attr('selected', 'selected')
                }

                let data = {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'projectId': {{ $project->id }},
                }

                if ($('#searchEngines').val() !== '') {
                    data.region = $('#searchEngines').val()
                }

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('monitoring.get.competitors.visibility') }}",
                    data: data,
                    success: function (response) {
                        renderTableHead(response.data)
                        renderTableBody(response.data)
                        table = initTable()
                    },
                });

                $('#searchEngines').on('change', function () {
                    let val = $(this).val()
                    let data = {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'projectId': {{ $project->id }},
                    }

                    if (val !== '') {
                        data.region = val
                        localStorage.setItem('lr_redbox_monitoring_selected_filter', JSON.stringify({
                            val: val,
                        }))
                    } else {
                        localStorage.removeItem('lr_redbox_monitoring_selected_filter')
                    }

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('monitoring.get.competitors.visibility') }}",
                        data: data,
                        success: function (response) {
                            if ($.fn.DataTable.fnIsDataTable($('#table'))) {
                                $('#table').dataTable().fnDestroy()
                            }
                            $('.render').remove()

                            renderTableHead(response.data)
                            renderTableBody(response.data)
                            table = initTable()


                            $('#toast-container').hide()
                            $('#toast-container').show(300)

                            setTimeout(() => {
                                $('#toast-container').hide(300)
                            }, 3000)
                        },
                    });
                })

                $('#more-info').unbind().on('click', function () {
                    if ($('#more-info-tbody').html() === '') {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('monitoring.more.info', $project->id) }}",
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'projectId': {{ $project->id }},
                            },
                            success: function (response) {
                                $('.render-more').remove()

                                let iterator = 1;
                                $.each(response.data, function (domain, values) {
                                    let row = '<tr class="render-more">'
                                    let positions = ''

                                    $.each(values['positions'], function (word, position) {
                                        position = String(position).substring(0, 5)
                                        positions += `<div>${word}: ${position}</div>`
                                    })
                                    let td =
                                        '<td>' +
                                        '    <p>' +
                                        '        <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#collapse' + iterator + '" aria-expanded="false" aria-controls="collapse' + iterator + '">' +
                                        "{{ __('Positions') }}" +
                                        '        </button>' +
                                        '    </p>' +
                                        '    <div class="collapse" id="collapse' + iterator + '">'
                                        + positions +
                                        '    </div>' +
                                        '</td>'

                                    row += '<td>' + domain + '</td>'
                                    row += td
                                    row += '<td>' + String(values['avg']).substring(0, 5) + '</td>'
                                    row += '<td>' + String(values['top_3']).substring(0, 5) + '</td>'
                                    row += '<td>' + String(values['top_10']).substring(0, 5) + '</td>'
                                    row += '<td>' + String(values['top_100']).substring(0, 5) + '</td>'

                                    $('#more-info-tbody').append(row)
                                    iterator++
                                })

                                $('#preloader-competitors').hide()
                                $('#position-table').show()

                                renderCharts(response.data)
                            },
                        });
                    }
                })
            })

            function renderTableHead(data) {
                let index = 1
                $.each(data, function (query, sites) {
                    $.each(sites, function (site, counter) {
                        if (site !== '') {
                            $('#tableHeadRow').append(
                                '<th class="render">' + site +
                                '   <span class="remove-competitor ml-1" data-target="' + site + '" data-id="' + index + '">' +
                                '      <i class="fa fa-trash"></i>' +
                                '   </span>' +
                                '</th>'
                            )

                            index++
                        }
                    })
                    return false;
                })
            }

            function renderTableBody(data) {
                $.each(data, function (query, info) {
                    let tr = '<tr class="render"><td>' + query + '</td>'
                    $.each(info, function (site, visibility) {
                        if (site !== '') {
                            tr += '<td>' + visibility + '</td>'
                        }
                    })
                    tr += '</tr>'

                    $('#tableBody').append(tr)
                })
            }

            function initTable() {
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
                    let url = $(this).attr('data-target')
                    let columnIndex = $(this).attr('data-id')
                    if (confirm(`{{ __('Are you going to remove the domain') }} "${url}" {{ __('from competitors') }}`)) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('monitoring.remove.competitor') }}",
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'url': url,
                                'projectId': {{ $project->id }}
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
                $('#preloader').hide()

                return res;
            }

            function renderCharts(data) {
                let colorArray = getColorArray()

                let labels = []
                let datas = []
                let top3 = []
                let top10 = []
                let top100 = []
                let colors = []

                $.each(data, function (domain, info) {
                    if (domain !== "") {
                        labels.push(domain)
                        datas.push(info.avg)
                        colors.push(colorArray.shift())
                        top3.push(info.top_3)
                        top10.push(info.top_10)
                        top100.push(info.top_100)
                    }
                })
                new Chart(document.getElementById("bar-chart"), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "{{ __("Average position") }}",
                                backgroundColor: colors,
                                data: datas
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                suggestedMin: 0,
                                suggestedMax: 100
                            },
                        },
                    }
                });
                new Chart(document.getElementById("bar-chart-3"), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "{{ __("Top") }} 3 (%)",
                                backgroundColor: colors,
                                data: top3
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                suggestedMin: 0,
                                suggestedMax: 100
                            },
                        },
                    }
                });
                new Chart(document.getElementById("bar-chart-10"), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "{{ __("Top") }} 10 (%)",
                                backgroundColor: colors,
                                data: top10
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                suggestedMin: 0,
                                suggestedMax: 100
                            },
                        },
                    }
                });
                new Chart(document.getElementById("bar-chart-100"), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "{{ __("Top") }} 100 (%)",
                                backgroundColor: colors,
                                data: top100
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                suggestedMin: 0,
                                suggestedMax: 100
                            },
                        },
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
        </script>
    @endslot
@endcomponent
