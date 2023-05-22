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
            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }

            .custom-info-bg {
                background-color: rgba(23, 162, 184, 0.5) !important;
            }

            #table > thead > tr > th.sorting_disabled.sorting_asc:before {
                display: none;
            }

            #table > thead > tr > th.sorting_disabled.sorting_asc:after {
                display: none;
            }

            #table > tbody > tr > td:nth-child(4) {
                width: 37.5%;
            }

            #table > tbody > tr:nth-child(1) {
                width: 5%;
                min-width: 5%;
                max-width: 5%;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right info-message" style="display:none;">
        <div class="toast toast-info" aria-live="polite">
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

    <div class="d-flex flex-row mb-3 mt-3 btn-group col-6 p-0">
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.competitors', $project->id) }}">
            {{ __('My competitors') }}
        </a>
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.competitors.positions', $project->id) }}">
            {{ __('Comparison with competitors') }}
        </a>

        <div class="btn-group">
            <button class="btn btn-outline-secondary" id="searchCompetitors" data-toggle="modal"
                    data-target="#competitorsModal" disabled>
                {{ __('Search for competitors') }}
            </button>
            <button type="button" class="btn btn-secondary">
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle" style="color:white;"></i>
                    <span class="ui_tooltip __right" style="width: 200px;">
                        <span class="ui_tooltip_content">
                            {{ __('We will automatically identify 5 of your closest competitors') }}
                        </span>
                    </span>
                </span>
            </button>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Region filter') }}</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-start align-items-center">
                        <div class="input-group col-4 pl-0 ml-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <select name="region" class="custom-select" id="searchEngines">
                                @if($project->searchengines->count() > 1)
                                    <option value="">{{ __('All search engine and regions') }}</option>
                                @endif

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
                            <button class="btn btn-secondary" id="start-analyse-region">{{ __("Analyse") }}</button>
                        </div>
                        <div id="download-results" style="display: none">
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="/img/1485.gif" style="width: 20px; height: 20px;">
                            </div>
                            <div id="render-state">
                                {{ __('loading results') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="competitorsModal" tabindex="-1" aria-labelledby="competitorsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="competitorsModalLabel">{{ __('Adding new competitors') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <label for="competitors-textarea"><b>{{ __('Your closest competitors') }}</b></label>
                        <textarea name="competitors-textarea"
                                  id="competitors-textarea"
                                  class="form form-control"
                                  cols="8" rows="8"></textarea>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-default mb-3" type="button" data-toggle="collapse"
                                data-target="#collapseIgnoredDomains" aria-expanded="false"
                                aria-controls="collapseIgnoredDomains">
                            {{ __('Ignored domains') }}
                        </button>
                        <div class="collapse" id="collapseIgnoredDomains">
                        <textarea id="ignored-domains" name="ignored-domains" class="form form-control" cols="8"
                                  rows="8" disabled>{{ $ignoredDomains }}</textarea>
                        </div>

                    </div>
                    <div class="mt-3">
                        <div>
                            <b>{{ __('Domain') }}: {{ __('How many times have I met') }}</b>
                        </div>
                        <div id="competitors-list"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="add-competitors" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('Add') }} </button>
                </div>
            </div>
        </div>
    </div>

    <div id="tableBlock" style="display: none">
        <h3>{{ __('Domains ranked in the top 10') }}</h3>
        <p>{{ __('The date of withdrawal of positions used') }}: <span id="dateOnly"></span></p>
        <table id="table" class="table table-bordered no-footer">
            <thead style="top: 0; position: sticky; background-color: white">
            <tr>
                <th style="min-width: 100px; max-width: 100px;">{{ __('Competitor') }}?</th>
                <th>{{ __('Domain') }}</th>
                <th>{{ __('Search engines') }}</th>
                <th>{{ __('Visibility by selected regions') }}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    @slot('js')
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

        <script src="{{ asset('plugins/datatables-buttons/js/buttons.excel.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.js') }}"></script>

        <script>
            let interval

            function changeCellState(elem) {
                let targetBlock = $(elem)
                let url = targetBlock.attr('data-target')
                let targetInput = targetBlock.children('input').eq(0)
                let state = targetBlock.attr('data-order') === 'true'

                if (!state) {
                    if (confirm(`{{ __('Are you going to add the domain') }} "${url}" {{ __('in competitors') }}`)) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('monitoring.add.competitor') }}",
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'url': url,
                                'projectId': {{ $project->id }}
                            },
                            success: function () {
                                targetInput.prop('checked', true)
                                targetBlock.attr('data-order', 'true')
                            },
                        });
                    } else {
                        targetInput.prop('checked', state)
                    }
                } else {
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
                                targetInput.prop('checked', false)
                                targetBlock.attr('data-order', 'false')
                            },
                        });
                    } else {
                        targetInput.prop('checked', state)
                    }
                }
            }

            $(document).ready(function () {
                let filter = localStorage.getItem('lr_redbox_monitoring_selected_filter')

                if (filter !== null) {
                    filter = JSON.parse(filter)
                    $('#searchEngines option[value=' + filter.val + ']').attr('selected', 'selected')
                }

                $('#searchEngines').on('change', function () {
                    let val = $(this).val()
                    if (val !== '') {
                        localStorage.setItem('lr_redbox_monitoring_selected_filter', JSON.stringify({
                            val: val,
                        }))
                    } else {
                        localStorage.removeItem('lr_redbox_monitoring_selected_filter')
                    }
                })

                $('#searchCompetitors').on('click', function () {
                    let table = $('#table').DataTable()
                    table.on('draw.dt', function () {
                        let competitors = getMaxValues()

                        let textAreaText = ''
                        let competitorsList = ''

                        for (let i = 0; i < competitors.length; i++) {
                            textAreaText += competitors[i][0] + "\n"
                            competitorsList += "<div>" + competitors[i][0] + ": " + competitors[i][1] + "</div>"
                        }

                        $('#competitors-textarea').text(textAreaText)
                        $('#competitors-list').html(competitorsList)

                        table.off('draw.dt');
                    });

                    table.order([3, 'desc']).draw();
                })

                $('#add-competitors').on('click', function () {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('monitoring.add.competitors') }}",
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'projectId': {{ $project->id }},
                            'domains': $('#competitors-textarea').val().split('\n')
                        },
                        success: function (response) {
                            $.each(response.urls, function (k, domain) {
                                $("input[data-target='" + domain + "']").prop('checked', true)
                                $("td[data-target='" + domain + "']").attr('data-order', 'true')
                            })

                            refreshMethods()
                        },
                    });
                })

                $('#start-analyse-region').on('click', function () {
                    getCompetitors()
                })
            })

            function getCompetitors() {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('monitoring.get.competitors') }}",
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'projectId': {{ $project->id }},
                        'region': $('#searchEngines').val()
                    },
                    beforeSend: function () {
                        $('#download-results').show()
                        if ($.fn.DataTable.fnIsDataTable($('#table'))) {
                            $('#table').dataTable().fnDestroy();
                            $('#table > tbody').html('')
                        }
                        $('#render-state').html("{{ __('loading results') }}")
                        $('#searchCompetitors').prop('disabled', true)
                        $('#tableBlock').hide()
                        $('#render-state').html("{{ __('In progress') }}")
                    },
                    success: function (response) {
                        if (response.state === 'ready') {
                            renderTableRows(response)
                        } else if (response.state === 'in process' || response.state === 'in queue') {
                            waitFinishResult(response)
                        }

                        if (response.newScan) {
                            $('#toast-container').show(300)
                            $('.toast-message').html('{{ __('New withdrawals of positions were discovered.') }} <br> {{ __('The analysis of fresh data has been launched.') }} <br>')
                            setTimeout(() => {
                                $('#toast-container').hide(300)
                            }, 10000)
                        }
                    },
                });
            }

            function waitFinishResult(response) {
                clearInterval(interval)
                $('#download-results').show()
                $('#render-state').html(`Вы в очереди`)

                interval = setInterval(() => {
                    $.ajax({
                        url: "/monitoring/wait-result",
                        method: "POST",
                        data: {
                            id: response.id,
                        },
                        success: function (response) {
                            if (response.state === 'ready') {
                                renderTableRows(response)
                                clearInterval(interval)
                            } else {
                                $('#render-state').html(`Вы в очереди`)
                            }
                        },
                    });
                }, 5000)
            }

            function renderTableRows(response) {
                let data = JSON.parse(response.result)
                let date = response.date

                $('#render-state').html("{{ __('Render data') }}")
                $('#dateOnly').html(date)

                let tableRows = []
                if (data !== []) {
                    $.each(data, function (key, val) {
                        let input = ''
                        if (val.mainPage) {
                            input = "{{ __('Your website') }}"
                        } else {
                            if (val.competitor) {
                                input = '<input type="checkbox" data-target="' + key + '" checked>'
                            } else {
                                input = '<input type="checkbox" data-target="' + key + '">'
                            }
                        }

                        let stub = key + '<i class="ml-2 fa fa-plus-circle get-more-info" data-target="' + key + '">'

                        let engines = ''
                        let firstBlock = false

                        $.each(val.urls, function (engine, v) {
                            if (engine === 'yandex') {
                                engines += '<i class="fab fa-yandex fa-sm mr-2"></i>'
                            }
                            if (engine === 'google') {
                                engines += '<i class="fab fa-google fa-sm mr-2"></i>'
                            }
                        })

                        let google = renderRegions(val.visibilityGoogle)
                        let yandex = renderRegions(val.visibilityYandex)

                        let visibilityCell =
                            '<div class="d-flex flex-row justify-content-between">'

                        if (google !== 0) {
                            firstBlock = true
                            visibilityCell += '<div class="w-50 p-2"> Google: ' + google + '</div>'
                        }
                        if (yandex !== 0) {
                            let border = 'border-0'
                            if (firstBlock) {
                                border = 'border-left'
                            }
                            visibilityCell += '<div class="w-50 p-2 ' + border + '"> Yandex: ' + yandex + '</div>'
                        }

                        let bool = val.competitor ?? false

                        tableRows.push('<tr>' +
                            '    <td data-order="' + bool + '" onclick="changeCellState(this)" data-target="' + key + '">' + input + '</td>' +
                            '    <td data-order="' + key + '">' + stub + '</td>' +
                            '    <td>' + engines + '</td>' +
                            '    <td class="p-0 m-0" data-order="' + Number(val.visibility) + '" data-action="' + key + '">' + visibilityCell + '</td>' +
                            '</tr>')
                    })
                }

                $('#table > tbody').html(tableRows.join(' '))

                $('#table').DataTable({
                    "order": [[3, 'desc']],
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
                        "emptyTable": "{{ __('More than 15 days have passed since the last withdrawal') }}"
                    },
                    columnDefs: [
                        {
                            orderable: false, targets: [0, 2]
                        },
                    ],
                })

                $('#table').on('draw.dt', function () {
                    refreshMethods()
                });

                setTimeout(() => {
                    $('#download-results').hide()
                    $('#table_wrapper').show()
                    $('#tableBlock').show()
                    $('#searchCompetitors').prop('disabled', false)
                    refreshMethods()
                }, 300)
            }

            function refreshMethods() {
                $('.fa-plus-circle.get-more-info').unbind().on('click', function () {
                    $(this).attr('class', 'ml-2 fa fa-minus-circle get-more-info')
                    let parent = $(this).parents().eq(1)
                    let targetDomain = $(this).attr('data-target')

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('monitoring.get.competitors.domain') }}",
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'projectId': {{ $project->id }},
                            'targetDomain': targetDomain,
                            'region': $('#searchEngines').val(),
                        },
                        beforeSend: function () {
                            parent.after(
                                '<tr class="progress-render" data-id="' + targetDomain + '">' +
                                '   <td colspan="' + {{ $countQuery + 1 }} + '">' +
                                '       <img src="/img/1485.gif" style="width: 50px; height: 50px;">' +
                                '   </td>' +
                                '</tr>'
                            )
                        },
                        success: function (response) {
                            let rows = ''
                            let yandexTh = false
                            let googleTh = false

                            $.each(response, function (phrase, engines) {
                                let yandex = ''
                                let google = ''

                                rows += '<tr><td>' + phrase + '</td>'

                                $.each(engines, function (engine, urls) {
                                    if (engine === 'yandex') {
                                        yandexTh = true
                                        $.each(urls, function (key, url) {
                                            $.each(url, function (region, link) {
                                                yandex += `<div><a href="${link}" target="_blank">${link}<a>(${region})</div>` + "\n\r"
                                            })
                                        })
                                    }
                                    if (engine === 'google') {
                                        googleTh = true
                                        $.each(urls, function (key, url) {
                                            $.each(url, function (region, link) {
                                                google += `<div><a href="${link}" target="_blank">${link}<a>(${region})</div>` + "\n\r"
                                            })
                                        })
                                    }
                                })
                                if (yandexTh) {
                                    rows += '<td>' + yandex + '</td>'
                                }

                                if (googleTh) {
                                    rows += '<td>' + google + '</td>'
                                }

                                rows += '</tr>'
                            })

                            let table =
                                '<table class="table table-hover table-bordered no-footer custom-table">' +
                                '    <thead>' +
                                '        <tr>' +
                                '            <th style="min-width:200px; max-width:200px;"> {{ __('Phrase') }} </th>'

                            if (yandexTh) {
                                table += '<th> {{ __('Yandex') }} </th>'
                            }
                            if (googleTh) {
                                table += '<th> {{ __('Google') }} </th>'
                            }
                            table += '</tr>' +
                                '</thead>' +
                                '    <tbody>' +
                                rows +
                                '    </tbody>' +
                                '</table>'

                            $('#table').find(`.progress-render[data-id='${targetDomain}']`).remove()
                            parent.after(
                                '<tr class="custom-render" data-id="' + targetDomain + '">' +
                                '   <td colspan="' + {{ $countQuery + 1 }} + '">'
                                + table +
                                '   </td>' +
                                '</tr>'
                            )

                            $.each($('.custom-table'), function () {
                                if (!$.fn.DataTable.fnIsDataTable($(this))) {
                                    $(this).DataTable({
                                        dom: 'lBfrtip',
                                        buttons: [
                                            'copy', 'csv', 'excel'
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
                                    })
                                }
                            })
                        },
                    });

                    refreshMethods()
                })

                $('.fa-minus-circle.get-more-info').unbind().on('click', function () {
                    let dataTarget = $(this).attr('data-target')
                    $('#table').find(`[data-id='${dataTarget}']`).remove()

                    $(this).attr('class', 'ml-2 fa fa-plus-circle get-more-info')
                    refreshMethods()
                })
            }

            function getMaxValues() {
                let domains = []
                let ignoredDomains = $('#ignored-domains').val().split('\n')

                $.each($('#table > tbody > tr > td:nth-child(4)'), function (k, v) {
                    if (
                        !ignoredDomains.includes($(this).attr('data-action')) &&
                        $(this).parent('tr').eq(0).children('td').eq(0).children('input').length !== 0 &&
                        !$(this).parent('tr').eq(0).children('td').eq(0).children('input').eq(0).is(':checked')
                    ) {
                        domains[$(this).attr('data-action')] = Number($(this).attr('data-order'))
                    }
                })

                let tuples = [];

                for (let key in domains) tuples.push([key, domains[key]]);

                tuples.sort(function (a, b) {
                    a = a[1];
                    b = b[1];

                    return a < b ? -1 : (a > b ? 1 : 0);
                });

                return tuples.reverse().slice(0, 5);
            }

            function renderRegions(val) {
                let region
                if (val.length !== 0) {
                    region = '<div>'
                    $.each(val, function (lr, count) {
                        region += `<div>${count}<span class="text-muted" title="${lr}"> ${lr.split(',')[0]}</span></div>`
                    })
                    region += '</div>'
                } else {
                    region = 0
                }

                return region
            }
        </script>
    @endslot
@endcomponent
