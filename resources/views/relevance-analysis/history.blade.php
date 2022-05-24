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

    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message" id="message-info"></div>
        </div>
    </div>

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
                    <table id="main_history_table" class="table table-bordered table-hover dataTable dtr-inline mb-3">
                        <thead>
                        <tr>
                            <th>Название проекта</th>
                            <th>Группа</th>
                            <th>Количество проанализированных страниц</th>
                            <th>Последняя проверка</th>
                            <th>Общий балл</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($main as $item)
                            <tr>
                                <td>
                                    <nav class="scrollto">
                                        <a href="#history_table" class="project_name" style="cursor:pointer;"
                                           data-order="{{ $item->id }}">
                                            {{ $item->name }}
                                        </a>
                                    </nav>
                                </td>
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
                        <table id="history_table" class="table table-bordered table-hover dataTable dtr-inline w-100">
                            <thead>
                            <tr>
                                <th>
                                    <input class="w-100 form form-control search-input" type="date" name="dataMin"
                                           id="dataMin"
                                           placeholder="min">
                                    <input class="w-100 form form-control" type="date" name="dataMax" id="dataMax"
                                           placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="phraseSearch" id="phraseSearch" placeholder="phrase">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="regionSearch" id="regionSearch" placeholder="region">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="mainPageSearch" id="mainPageSearch" placeholder="link">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minPosition" id="minPosition" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxPosition" id="maxPosition" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minPoints" id="minPoints" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxPoints" id="maxPoints" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minCoverage" id="minCoverage" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxCoverage" id="maxCoverage" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minCoverageTf" id="minCoverageTf" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxCoverageTf" id="maxCoverageTf" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number" name="minWidth"
                                           id="minWidth" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxWidth" id="maxWidth" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minDensity" id="minDensity" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxDensity" id="maxDensity" placeholder="max">
                                </th>
                                <th>
                                    <div>
                                        Переключить всё
                                        <div class='d-flex w-100'>
                                            <div class='__helper-link ui_tooltip_w'>
                                                <div
                                                    class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success changeAllState'>
                                                    <input type='checkbox' class='custom-control-input'
                                                           id='changeAllState'>
                                                    <label class='custom-control-label' for='changeAllState'></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th style="z-index: 9999; background: white !important;">Дата последней проверки</th>
                                <th style="z-index: 9999; background: white !important; min-width: 160px; height: 83px">
                                    Фраза
                                </th>
                                <th style="z-index: 9999; background: white !important; min-width: 160px; height: 83px">
                                    Регион
                                </th>
                                <th style="z-index: 9999; background: white !important; min-width: 160px; max-width:160px; height: 83px">
                                    Посадочная страница
                                </th>
                                <th style="z-index: 9999; background: white !important; height: 83px">Позиция в топе
                                </th>
                                <th style="z-index: 9999; background: white !important; height: 83px">Баллы</th>
                                <th style="z-index: 9999; background: white !important; height: 83px">Охват важных
                                    слова
                                </th>
                                <th style="z-index: 9999; background: white !important; height: 83px">Охват tf</th>
                                <th style="z-index: 9999; background: white !important; height: 83px">Ширина</th>
                                <th style="z-index: 9999; background: white !important; height: 83px">Плотность</th>
                                <th style="z-index: 9999; background: white !important; height: 83px">Учитывать в
                                    расчёте общего
                                    балла
                                </th>
                                <th style="z-index: 9999; background: white !important;"></th>
                            </tr>
                            </thead>
                            <tbody id="historyTbody">
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
        <script src="{{ asset('plugins/relevance-analysis/history/mainHistoryTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/childHistoryTable.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/1.12.0/sorting/date-dd-MMM-yyyy.js"></script>
    @endslot
@endcomponent
