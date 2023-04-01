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
            #table_wrapper {
                display: none;
            }

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
                    <div class="row">
                        <form action="" style="display: contents;">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>{{ __('Search engine') }}:</label>
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
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a class="btn btn-outline-secondary" href="{{ route('monitoring.competitors.positions', $project->id) }}">
        Сравнение с конкурентами
    </a>

    <h3 class="mt-3 mr-3">
        {{  __('Project') . " $project->name" }}
    </h3>

    <h4>
        Количество фраз: {{ $countQuery }}
    </h4>

    <div class="d-flex justify-content-center align-items-center align-content-center">
        <img src="/img/1485.gif" style="width: 50px; height: 50px;" id="preloader">
    </div>
    <table id="table" class="table table-bordered no-footer" style="display: none">
        <thead style="top: 0; position: sticky; background-color: white">
        <tr>
            <th>Конкурент?</th>
            <th>Домен</th>
            <th>Поисковые системы</th>
            <th>Видимость</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
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
            let table
            let data = {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'projectId': {{ $project->id }},
            }

            $(document).ready(function () {
                let filter = localStorage.getItem('lr_redbox_monitoring_selected_filter')

                if (filter !== null) {
                    filter = JSON.parse(filter)
                    $('#searchEngines option[value=' + filter.val + ']').attr('selected', 'selected')
                }

                table = $('#table').DataTable({
                    fixedHeader: true,
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 50,
                    order: [[3, 'desc']],
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
                    columnDefs: [
                        {orderable: false, targets: [0, 2]},
                    ],
                })

                if ($('#searchEngines').val() !== '') {
                    data.region = $('#searchEngines').val()
                }

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('monitoring.get.competitors') }}",
                    data: data,
                    success: function (response) {
                        console.log(response)
                        renderTableRows(response)

                        $('#preloader').hide()
                        setTimeout(() => {
                            $('#table_wrapper').show()
                            $('#table').show()
                        }, 300)

                        refreshMethods()
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
                        url: "{{ route('monitoring.get.competitors') }}",
                        data: data,
                        success: function (response) {
                            table.rows().remove().draw();
                            renderTableRows(response)

                            $('#toast-container').hide()
                            $('#toast-container').show(300)

                            setTimeout(() => {
                                $('#toast-container').hide(300)
                            }, 3000)
                        },
                    });
                })
            })

            function renderTableRows(data) {
                $.each(data, function (key, val) {
                    let input = ''
                    if (val.mainPage) {
                        input = 'Ваш сайт'
                    } else {
                        if (val.competitor) {
                            input = '<input type="checkbox" data-target="' + key + '" class="change-domain-state" checked>'
                        } else {
                            input = '<input type="checkbox" data-target="' + key + '" class="change-domain-state">'
                        }

                    }

                    let stub = key + '<i class="ml-2 fa fa-plus-circle get-more-info" data-target="' + key + '">'

                    let engines = ''
                    $.each(val.urls, function (engine, v) {
                        if (engine === 'yandex') {
                            engines += '<i class="fab fa-yandex fa-sm mr-2"></i>'
                        }
                        if (engine === 'google') {
                            engines += '<i class="fab fa-google fa-sm mr-2"></i>'
                        }
                    })

                    let google
                    if (val.visibilityGoogle.length !== 0) {
                        google = '<ul>'
                        $.each(val.visibilityGoogle, function (count, lr) {
                            google += `<li>${lr}<span class="text-muted">(${count})</span></li>`
                        })
                        google += '</ul>'
                    } else {
                        google = 0
                    }

                    let yandex
                    if (val.visibilityYandex.length !== 0) {
                        yandex = '<ul>'
                        $.each(val.visibilityYandex, function (count, lr) {
                            yandex += `<li>${lr}<span class="text-muted">(${count})</span></li>`
                        })
                        yandex += '</ul>'
                    } else {
                        yandex = 0
                    }

                    table.row.add({
                        0: input,
                        1: stub,
                        2: engines,
                        3: '<div>Общая: ' + val.visibility + '</div>' +
                            '<div> Google: ' + google + '</div>' +
                            '<div> Yandex: ' + yandex + '</div>'
                    })
                })

                table.draw(false)

                refreshMethods()
            }

            function refreshMethods() {
                $('.fa-plus-circle.get-more-info').unbind().on('click', function () {
                    $(this).attr('class', 'ml-2 fa fa-minus-circle get-more-info')
                    let parent = $(this).parents().eq(1)
                    let targetDomain = $(this).attr('data-target')

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('monitoring.get.competitors') }}",
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'projectId': {{ $project->id }},
                            'targetDomain': targetDomain,
                            'region': $('#searchEngines').val()
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

                            $.each(response[targetDomain]['urls'], function (phrase, engines) {
                                $.each(engines, function (engine) {
                                    if (engine === 'yandex') {
                                        yandexTh = true
                                    }
                                    if (engine === 'google') {
                                        googleTh = true
                                    }
                                })

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
                                '            <th> {{ __('Phrase') }} </th>'

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

                $('.change-domain-state').unbind().on('click', function () {
                    let url = $(this).attr('data-target')
                    if ($(this).is(':checked')) {
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
                                success: function (response) {

                                },
                            });
                        } else {
                            $(this).prop('checked', false);
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

                                },
                            });
                        } else {
                            $(this).prop('checked', true);
                        }
                    }
                })
            }
        </script>
    @endslot
@endcomponent
