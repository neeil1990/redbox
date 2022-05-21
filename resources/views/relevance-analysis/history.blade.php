@component('component.card', ['title' =>  __('Relevance history') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            .ui_tooltip_content {
                font-weight: normal;
            }

            .bg-warning-elem {
                background-color: #f5e2aa !important;
            }

            #unigramTBody > tr > td:nth-child(1) {
                text-align: center;
            }

            #app > div > div > div.card-body > div.d-flex.flex-column > div > button.btn.btn-secondary.col-2 > span > i {
                color: #fffdfd !important;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
            }

            .fa.fa-question-circle {
                color: white;
            }

            #unigramTBody > tr > td:nth-child(8),
            #unigramTBody > tr > td:nth-child(10),
            #unigramTBody > tr > td:nth-child(12),
            #phrasesTBody > tr > td:nth-child(7),
            #phrasesTBody > tr > td:nth-child(9),
            #phrasesTBody > tr > td:nth-child(11),
            #recommendationsTBody > tr > td:nth-child(5) {
                background: #ebf0f5;
            }

            .ui_tooltip.__left, .ui_tooltip.__right {
                width: auto;
            }

            .pb-3.unigramd thead th {
                position: sticky;
                top: 0;
                z-index: 1;
            }

            .pb-3.unigramd tbody th {
                position: sticky;
                left: 0;
            }

            .dataTables_paginate.paging_simple_numbers {
                padding-bottom: 50px;
            }

            .dt-buttons {
                margin-left: 20px;
                float: left;
            }

            .bg-my-site {
                background: #4eb767c4;
            }
        </style>
    @endslot

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_1" data-toggle="tab">Ваша история</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tab_2" data-toggle="tab">Управление доступом</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tab_3" data-toggle="tab">Доступные истории других пользователей</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <h3>Основные истории</h3>
                    <table id="main_history_table" class="table table-bordered table-striped dataTable dtr-inline mb-3">
                        <thead>
                        <tr>
                            <th>Название проекта</th>
                            <th class="col-2">Группа</th>
                            <th class="col-2">Количество проанализированных страниц</th>
                            <th>Последняя проверка</th>
                            <th>Общий балл</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($main as $item)
                            <tr>
                                <td style="cursor:pointer;" class="project_name" data-order="{{ $item->id }}">{{ $item->name }}</td>
                                <td data-order="{{ $item->group_name }}">
                                    <input type="text" class="form form-control group-name-input"
                                           value="{{ $item->group_name }}"
                                           name="group_name"
                                           data-target="{{ $item->id }}">
                                </td>
                                <td>{{ $item->count_sites }}</td>
                                <td>{{ $item->last_check }}</td>
                                <td>{{ $item->total_points }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div style="display:none;" class="history">
                        <h3>Последние проверки</h3>
                        <table id="history_table" class="table table-bordered table-striped dataTable dtr-inline w-100">
                            <thead>
                            <tr>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100 form form-control" type="number" name="dataMin" id="dataMin" placeholder="min">
                                        <input class="w-100 form form-control" type="number" name="dataMax" id="dataMax" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100 form form-control" type="number" name="phraseSearch" id="phraseSearch" placeholder="phrase">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="regionSearch" id="regionSearch" placeholder="region">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="mainPageSearch" id="mainPageSearch" placeholder="link">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="minPosition" id="minPosition" placeholder="min">
                                        <input class="w-100 form form-control" type="number" name="maxPosition" id="maxPosition" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="minPoints" id="minPoints" placeholder="min">
                                        <input class="w-100 form form-control" type="number" name="maxPoints" id="maxPoints" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="minCoverage" id="minCoverage" placeholder="min">
                                        <input class="w-100 form form-control" type="number" name="maxCoverage" id="maxCoverage" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="minCoveradeTf" id="minCoveradeTf"placeholder="min">
                                        <input class="w-100 form form-control" type="number" name="maxCoveradeTf" id="maxCoveradeTf" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="minWidth" id="minWidth" placeholder="min">
                                        <input class="w-100 form form-control" type="number" name="maxWidth" id="maxWidth" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100 form form-control" type="number" name="minDensity" id="minDensity" placeholder="min">
                                        <input class="w-100 form form-control" type="number" name="maxDensity" id="maxDensity" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>Дата последней проверки</th>
                                <th>Фраза</th>
                                <th>Регион</th>
                                <th>Посадочная страница</th>
                                <th>Позиция в топе</th>
                                <th>Баллы</th>
                                <th>Охват важных слова</th>
                                <th>Охват tf</th>
                                <th>Ширина</th>
                                <th>Плотность</th>
                                <th>Учитывать в расчёте общего балла</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($history as $item)
                                <tr data-order="{{ $item->project_relevance_history_id }}" class="children-row">
                                    <td class="col-1">{{ $item->last_check }}</td>
                                    <td class="col-1">{{ $item->phrase }}</td>
                                    <td class="col-2">{{ $item->getRegionName($item->region) }}</td>
                                    <td class="col-2">{{ $item->main_link }}</td>
                                    <td class="col-1">{{ $item->position }}</td>
                                    <td class="col-1">{{ $item->points }}</td>
                                    <td class="col-1">{{ $item->coverage }}</td>
                                    <td class="col-1">{{ $item->coverage_tf }}</td>
                                    <td class="col-1">{{ $item->width }}</td>
                                    <td class="col-1">{{ $item->density }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="__helper-link ui_tooltip_w">
                                                <div
                                                    class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                    <input type="checkbox"
                                                           class="custom-control-input switch"
                                                           id="calculate-project-{{ $item->id }}"
                                                           name="noIndex"
                                                           data-target="{{ $item->id }}"
                                                           data-name="calculate"
                                                           @if($item->calculate ) checked @endif>
                                                    <label class="custom-control-label"
                                                           for="calculate-project-{{ $item->id }}"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('show.history', $item->id) }}" target="_blank"
                                           class="btn btn-secondary">Подробная
                                            информация</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab_2">
                    Управление доступом
                </div>
                <div class="tab-pane" id="tab_3">
                    Доступные истории других пользователей
                </div>
            </div>
        </div>
    </div>


    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            let mainHistory = $('#main_history_table').DataTable({
                "order": [[1, "desc"]],
                "pageLength": 10,
                "searching": true,
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel'
                ]
            });

            let history = $('#history_table').DataTable({
                "order": [[1, "desc"]],
                "pageLength": 10,
                "searching": true,
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel'
                ]
            });

            $.each($(".dt-button"), function (key, value) {
                $(this).addClass('btn btn-secondary')
            });

            $('.project_name').click(function () {
                let target = $(this).attr('data-order')
                $('.history').show()
                // children-row
                $.each($('.children-row'), function (key, value) {
                    if ($(this).attr('data-order') === target) {
                        $(this).show()
                    } else {
                        $(this).hide()
                    }
                })
                // console.log($(this).attr('data-order'))
            });
        </script>
        <script>
            $(".group-name-input").change(function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('change.group.name') }}",
                    data: {
                        id: $(this).attr('data-target'),
                        name: $(this).val()
                    },
                    success: function (response) {
                    },
                });
            });

            $(".switch").change(function () {
                console.log($(this).attr('data-target'))
                console.log($(this).is(':checked'))
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('change.state') }}",
                    data: {
                        id: $(this).attr('data-target'),
                        calculate: $(this).is(':checked')
                    },
                    success: function (response) {
                        console.log(response)
                    },
                });
            })
        </script>
    @endslot
@endcomponent
