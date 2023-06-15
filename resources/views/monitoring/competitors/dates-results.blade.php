@component('component.card', ['title' => $project->mainProject->name . ' ' . $project->range])
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

            .antiquewhite {
                background: antiquewhite;
            }

            th:first-child,
            td:first-child {
                position: sticky;
                left: 0;
                background-color: #FFF;
            }

            td:first-child {
                padding-left: 17px;
            }
        </style>
    @endslot
    <div>
        <h3 class="mb-3">{{ $request['region'] }}</h3>
        <div id="history-block">
            <div class="mb-2 btn-group" id="visibility-buttons">
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

    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script>
            let historyTable

            $(document).ready(function () {
                renderHistoryPositions({!! $project['result'] !!})
            })

            function renderHistoryPositions(data) {
                const sortedKeys = Object.keys(data).sort((a, b) => new Date(a.split('-').reverse().join('-')) - new Date(b.split('-').reverse().join('-')));
                const sortedData = {};
                let length = 0

                sortedKeys.forEach(key => {
                    sortedData[key] = data[key];
                    length++;
                });

                let result
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

                if (bottomHead === '') {
                    $('#visibility-buttons').remove()
                    $('#history-block').append('<table class="table table-hover table-bordered w-25" id="history-results">' +
                        '    <thead>' +
                        '        <tr>' +
                        '            <th class="text-center">{{ __('Domain') }}</th>' +
                        '            <th colspan="' + length + '" class="text-center">{{ __('Statistics') }}</th>' +
                        '        </tr>' +
                        '    </thead>' +
                        '    <tbody>' +
                        '    <tr>' +
                        '        <td colspan="2">{{ __('There are no positions for the selected date ranges') }}</td>' +
                        '    </tr>' +
                        '    </tbody>' +
                        '</table>')
                } else {
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
                            emptyTable: "{{ __('There are no positions for the selected date ranges') }}"
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
                            $(this).addClass('antiquewhite')
                            $(this).attr('data-action', 'show')
                            historyTable.columns(String($(this).attr('data-order')).split(',')).visible(false);
                        } else {
                            $(this).removeClass('antiquewhite')
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
        </script>
    @endslot
@endcomponent
