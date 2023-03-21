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
        Количество Конкурентов: {{ count($competitors) }}
    </h4>

    <div class="d-flex justify-content-center align-items-center align-content-center">
        <img src="/img/1485.gif" style="width: 50px; height: 50px;" id="preloader">
    </div>

    <table id="table" class="table table-hover table-bordered no-footer" style="display: none">
        <thead>
        <tr id="tableHeadRow">
            <th>Запрос</th>
        </tr>
        </thead>
        <tbody id="tableBody">
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
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('monitoring.get.competitors.visibility') }}",
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'projectId': {{ $project->id }},
                            'region': $(this).val()
                        },
                        success: function (response) {
                            if ($.fn.DataTable.fnIsDataTable($('#table'))) {
                                $('#table').dataTable().fnDestroy()
                            }
                            $('.render').remove()

                            renderTableHead(response.data)
                            renderTableBody(response.data)
                            table = initTable()
                        },
                    });
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
                    if (confirm(`Вы собираетесь убрать домен "${url}" из конкурентов`)) {
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
        </script>
    @endslot
@endcomponent
