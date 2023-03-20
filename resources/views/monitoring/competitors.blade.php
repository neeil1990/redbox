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
            #table_wrapper .row {
                opacity: 0;
            }

            .custom-info-bg {
                background-color: rgba(23, 162, 184, 0.5) !important;
            }
        </style>
    @endslot

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
                    <h3 class="card-title">{{ __('Keywords filter') }}</h3>
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

    <h3 class="mt-3">
        {{  __('Project') . " $project->name" }}
    </h3>

    <h4>
        Количество фраз: {{ $countQuery }}
    </h4>

    <div class="d-flex justify-content-center align-items-center align-content-center">
        <img src="/img/1485.gif" style="width: 50px; height: 50px;" id="preloader">
    </div>
    <table id="table" class="table table-hover table-bordered no-footer" style="display: none">
        <thead>
        <tr>
            <th>Конкурент?</th>
            <th>Домен</th>
            <th>Поисковые системы</th>
            <th>Видимость</th>
        </tr>
        </thead>
        <tbody>
        {{--        @foreach($competitors as $competitor => $info)--}}
        {{--            <tr>--}}
        {{--                <td data-order="@if(isset($info['competitor'])) 1 @else 0 @endif">--}}
        {{--                    <div>--}}
        {{--                        <input type="checkbox"--}}
        {{--                               class="change-domain-state"--}}
        {{--                               data-target="{{ $competitor }}"--}}
        {{--                               @if(isset($info['competitor'])) checked @endif>--}}
        {{--                    </div>--}}
        {{--                </td>--}}
        {{--                <td @if(isset($info['mainPage'])) class="custom-info-bg" @endif>--}}
        {{--                    {{ $competitor }}--}}
        {{--                            <span class="__helper-link ui_tooltip_w">--}}
        {{--                                <i class="fa fa-question-circle"></i>--}}
        {{--                                <span class="ui_tooltip __right" style="width: 460px">--}}
        {{--                                    <span class="ui_tooltip_content">--}}
        {{--                                        @foreach($info['urls'] as $engine => $words)--}}
        {{--                                            <b class="mb-2 text-info"> {{ $engine }}: </b>--}}
        {{--                                            @foreach($words as $word => $stats)--}}
        {{--                                                @foreach($stats as $stat)--}}
        {{--                                                    <div class="mb-2">--}}
        {{--                                                        {{ $word }}: <a href="{{ $stat }}" target="_blank"> {{ $stat }} </a>--}}
        {{--                                                    </div>--}}
        {{--                                                @endforeach--}}
        {{--                                            @endforeach--}}
        {{--                                        @endforeach--}}
        {{--                                    </span>--}}
        {{--                                </span>--}}
        {{--                            </span>--}}
        {{--                </td>--}}
        {{--                <td>--}}
        {{--                    @foreach($info['urls'] as $engine => $urls)--}}
        {{--                        @if($engine === 'google')--}}
        {{--                            <i class="fab fa-google fa-sm mr-2"></i>--}}
        {{--                        @endif--}}
        {{--                        @if($engine === 'yandex')--}}
        {{--                            <i class="fab fa-yandex fa-sm mr-2"></i>--}}
        {{--                        @endif--}}
        {{--                    @endforeach--}}
        {{--                </td>--}}
        {{--                <td>--}}
        {{--                    {{ $info['visibility'] }}--}}
        {{--                </td>--}}
        {{--            </tr>--}}
        {{--        @endforeach--}}
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

        <script>
            var table
            $(document).ready(function () {
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

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('monitoring.get.competitors') }}",
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'projectId': {{ $project->id }}
                    },
                    success: function (response) {
                        renderTableRows(response.data)

                        $('#preloader').hide()
                        setTimeout(() => {
                            $('#table_wrapper .row').css({
                                opacity: 1
                            })
                            $('#table').show()
                        }, 300)

                        $('.change-domain-state').unbind().on('click', function () {
                            let url = $(this).attr('data-target')
                            if ($(this).is(':checked')) {
                                if (confirm(`Вы собираетесь добавить домен "${url}" в конкуренты`)) {
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
                                if (confirm(`Вы собираетесь убрать домен "${url}" из конкурентов`)) {
                                    $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        url: "{{ route('monitoring.remove.competitor') }}",
                                        data: {
                                            '_token': $('meta[name="csrf-token"]').attr('content'),
                                            'url': target,
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
                    },
                });
            })

            $('#searchEngines').on('change', function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('monitoring.get.competitors') }}",
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'projectId': {{ $project->id }},
                        'region': $(this).val()
                    },
                    success: function (response) {
                        table.rows().remove().draw();

                        renderTableRows(response.data)
                    },
                });
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

                    let stub = key + '<span class="__helper-link ui_tooltip_w"> ' +
                        '<i class="fa fa-question-circle"></i> ' +
                        '<span class="ui_tooltip __right" style="width: 460px"> ' +
                        '<span class="ui_tooltip_content">'

                    $.each(val.urls, function (engine, words) {
                        stub += '<b class="mb-2 text-info"> ' + engine + ': </b>'
                        $.each(words, function (word, stats) {
                            $.each(stats, function (k, stat) {
                                stub += ' <div class="mb-2">' + word + ': <a href="' + stat + '" target="_blank"> ' + stat + ' </a> </div>'
                            })

                        })
                    });
                    stub += '</span></span></span>'

                    let engines = ''
                    $.each(val.urls, function (k, v) {
                        if (k === 'yandex') {
                            engines += '<i class="fab fa-yandex fa-sm mr-2"></i>'
                        }
                        if (k === 'google') {
                            engines += '<i class="fab fa-google fa-sm mr-2"></i>'
                        }
                    })

                    table.row.add({
                        0: input,
                        1: stub,
                        2: engines,
                        3: val.visibility
                    }).draw(false)
                })
            }
        </script>
    @endslot
@endcomponent
