@component('component.card', ['title' =>  'Ваша история анализа'])
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

            .table-header {
                z-index: 100;
                background: white;
            }

            .col {
                display: block;
                float: left;
                margin: 1% 0 1% 1.6%;
            }

            .col:first-of-type {
                margin-left: 0;
            }

            .container {
                width: 100%;
                max-width: 940px;
                margin: 0 auto;
                position: relative;
                text-align: center;
            }

            /* CLEARFIX */

            .cf:before,
            .cf:after {
                content: " ";
                display: table;
            }

            .cf:after {
                clear: both;
            }

            .cf {
                *zoom: 1;
            }

            .row {
                margin: 30px 0;
            }

            .loader {
                width: 100px;
                height: 100px;
                border-radius: 100%;
                position: relative;
                margin: 0 auto;
            }

            #loader-1 {
                display: flex;
                justify-content: center;
            }

            #loader-1:before,
            #loader-1:after {
                content: "";
                position: absolute;
                width: 50%;
                height: 50%;
                border-radius: 100%;
                border: 10px solid transparent;
                border-top-color: #5a6268;
            }

            #loader-1:before {
                z-index: 100;
                animation: spin 1s infinite;
            }

            #loader-1:after {
                border: 10px solid #ccc;
            }

            @keyframes spin {
                0% {
                    -webkit-transform: rotate(0deg);
                    -ms-transform: rotate(0deg);
                    -o-transform: rotate(0deg);
                    transform: rotate(0deg);
                }

                100% {
                    -webkit-transform: rotate(360deg);
                    -ms-transform: rotate(360deg);
                    -o-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
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
                    <a class="nav-link" href="{{ route('test.relevance.view') }}">Анализатор</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('create.queue.view') }}">
                        Создать задачи по анализу страниц
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_1" data-toggle="tab">История</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link" href="#tab_2" data-toggle="tab">Администрирование модуля</a>
                    </li>
                @endif
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
                            <th class="table-header">Количество проанализированных страниц</th>
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
                                <td class="col-2">{{ $item->count_sites }}</td>
                                <td>{{ $item->last_check }}</td>
                                <td>{{ $item->total_points }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div style="display:none;" class="history">
                        <!-- Modal -->
                        <div class="modal fade" id="staticBackdrop" data-backdrop="static" tabindex="-1" role="dialog"
                             aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">Повторить анализ</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-12">
                                            <div class="form-group required">
                                                <label>{{ __('Your landing page') }}</label>
                                                {!! Form::text("link", null ,["class" => "form-control link", "required"]) !!}
                                            </div>

{{--                                            <div class="form-group required">--}}
{{--                                                <label>{{ __('Тип проверки') }}</label>--}}
{{--                                                {!! Form::select('type', [--}}
{{--                                                    'phrase' => 'Ключевая фраза',--}}
{{--                                                    'list' => 'Список сканируемых сайтов',--}}
{{--                                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'check-type']) !!}--}}
{{--                                            </div>--}}

                                            <div id="site-list">
                                                <div class="form-group required">
                                                    <label>Список сайтов</label>
                                                    {!! Form::textarea("siteList", null ,["class" => "form-control", 'id'=>'siteList'] ) !!}
                                                </div>
                                            </div>

                                            <div id="key-phrase">
                                                <div class="form-group required">
                                                    <label>{{ __('Keyword') }}</label>
                                                    {!! Form::text("phrase", null ,["class" => "form-control phrase", "required"]) !!}
                                                </div>

                                                <div class="form-group required">
                                                    <label>{{ __('Top 10/20') }}</label>
                                                    <select name="count" id="count"
                                                            class="custom-select rounded-0 count">
                                                        <option value="10">10</option>
                                                        <option value="20">20</option>
                                                    </select>

                                                </div>

                                                <div class="form-group required">
                                                    <label>{{ __('Region') }}</label>
                                                    {!! Form::select('region', array_unique([
                                                           $config->region => $config->region,
                                                           '1' => __('Moscow'),
                                                           '20' => __('Arkhangelsk'),
                                                           '37' => __('Astrakhan'),
                                                           '197' => __('Barnaul'),
                                                           '4' => __('Belgorod'),
                                                           '77' => __('Blagoveshchensk'),
                                                           '191' => __('Bryansk'),
                                                           '24' => __('Veliky Novgorod'),
                                                           '75' => __('Vladivostok'),
                                                           '33' => __('Vladikavkaz'),
                                                           '192' => __('Vladimir'),
                                                           '38' => __('Volgograd'),
                                                           '21' => __('Vologda'),
                                                           '193' => __('Voronezh'),
                                                           '1106' => __('Grozny'),
                                                           '54' => __('Ekaterinburg'),
                                                           '5' => __('Ivanovo'),
                                                           '63' => __('Irkutsk'),
                                                           '41' => __('Yoshkar-ola'),
                                                           '43' => __('Kazan'),
                                                           '22' => __('Kaliningrad'),
                                                           '64' => __('Kemerovo'),
                                                           '7' => __('Kostroma'),
                                                           '35' => __('Krasnodar'),
                                                           '62' => __('Krasnoyarsk'),
                                                           '53' => __('Kurgan'),
                                                           '8' => __('Kursk'),
                                                           '9' => __('Lipetsk'),
                                                           '28' => __('Makhachkala'),
                                                           '213' => __('Moscow'),
                                                           '23' => __('Murmansk'),
                                                           '1092' => __('Nazran'),
                                                           '30' => __('Nalchik'),
                                                           '47' => __('Nizhniy Novgorod'),
                                                           '65' => __('Novosibirsk'),
                                                           '66' => __('Omsk'),
                                                           '10' => __('Eagle'),
                                                           '48' => __('Orenburg'),
                                                           '49' => __('Penza'),
                                                           '50' => __('Perm'),
                                                           '25' => __('Pskov'),
                                                           '39' => __('Rostov-on-Don'),
                                                           '11' => __('Ryazan'),
                                                           '51' => __('Samara'),
                                                           '42' => __('Saransk'),
                                                           '2' => __('Saint-Petersburg'),
                                                           '12' => __('Smolensk'),
                                                           '239' => __('Sochi'),
                                                           '36' => __('Stavropol'),
                                                           '973' => __('Surgut'),
                                                           '13' => __('Tambov'),
                                                           '14' => __('Tver'),
                                                           '67' => __('Tomsk'),
                                                           '15' => __('Tula'),
                                                           '195' => __('Ulyanovsk'),
                                                           '172' => __('Ufa'),
                                                           '76' => __('Khabarovsk'),
                                                           '45' => __('Cheboksary'),
                                                           '56' => __('Chelyabinsk'),
                                                           '1104' => __('Cherkessk'),
                                                           '16' => __('Yaroslavl'),
                                                           ]), null, ['class' => 'custom-select rounded-0 region']) !!}
                                                </div>

                                                <div class="form-group required" id="ignoredDomainsBlock">
                                                    <label id="ignoredDomains">{{ __('Ignored domains') }}</label>
                                                    {!! Form::textarea("ignoredDomains", null,["class" => "form-control ignoredDomains"] ) !!}
                                                </div>
                                            </div>

                                            <div class="form-group required d-flex align-items-center">
                                                <span>{{ __('Cut the words shorter') }}</span>
                                                <input type="number" class="form form-control col-2 ml-1 mr-1"
                                                       name="separator"
                                                       id="separator">
                                                <span>{{ __('symbols') }}</span>
                                            </div>

                                            <div class="switch mt-3 mb-3">
                                                <div class="d-flex">
                                                    <div class="__helper-link ui_tooltip_w">
                                                        <div
                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                            <input type="checkbox"
                                                                   class="custom-control-input"
                                                                   id="switchNoindex"
                                                                   name="noIndex">
                                                            <label class="custom-control-label"
                                                                   for="switchNoindex"></label>
                                                        </div>
                                                    </div>
                                                    <p>{{ __('Track the text in the noindex tag') }}</p>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="__helper-link ui_tooltip_w">
                                                        <div
                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                            <input type="checkbox"
                                                                   class="custom-control-input"
                                                                   id="switchAltAndTitle"
                                                                   name="hiddenText">
                                                            <label class="custom-control-label"
                                                                   for="switchAltAndTitle"></label>
                                                        </div>
                                                    </div>
                                                    <p>{{ __('Track words in the alt, title, and data-text attributes') }}</p>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="__helper-link ui_tooltip_w">
                                                        <div
                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                            <input type="checkbox"
                                                                   class="custom-control-input"
                                                                   id="switchConjunctionsPrepositionsPronouns"
                                                                   name="conjunctionsPrepositionsPronouns">
                                                            <label class="custom-control-label"
                                                                   for="switchConjunctionsPrepositionsPronouns"></label>
                                                        </div>
                                                    </div>
                                                    <p>{{ __('Track conjunctions, prepositions, pronouns') }}</p>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="__helper-link ui_tooltip_w">
                                                        <div
                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                            <input type="checkbox"
                                                                   class="custom-control-input"
                                                                   id="switchMyListWords"
                                                                   name="switchMyListWords">
                                                            <label class="custom-control-label"
                                                                   for="switchMyListWords"></label>
                                                        </div>
                                                    </div>
                                                    <span>{{ __('Exclude') }}<span
                                                            class="text-muted">{{ __('(your own list of words)') }}</span></span>
                                                </div>
                                                <div class="form-group required list-words mt-1"
                                                     @if($config->remove_my_list_words == 'no') style="display:none;" @endif >
                                                    {!! Form::textarea('listWords', $config->my_list_words,['class' => 'form-control listWords', 'cols' => 8, 'rows' => 5]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="hiddenId">
                                    <input type="hidden" id="type">
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                        <button type="button" class="btn btn-secondary" id="relevance-repeat-scan" data-dismiss="modal">
                                            Повторить анализ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3>Последние проверки</h3>
                        <table id="history_table" class="table table-bordered table-hover dataTable dtr-inline w-100">
                            <thead>
                            <tr>
                                <th>
                                    <input class="w-100 form form-control" type="date" name="dateMin"
                                           id="dateMin"
                                           value="{{ Carbon\Carbon::parse('2022-03-01')->toDateString() }}">
                                    <input class="w-100 form form-control" type="date" name="dateMax" id="dateMax"
                                           value="{{ Carbon\Carbon::now()->toDateString() }}">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="projectComment" id="projectComment" placeholder="comment">
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
                                <th class="table-header">Дата последней проверки</th>
                                <th class="table-header" style="min-width: 200px">
                                    Комментарий
                                </th>
                                <th class="table-header" style="min-width: 160px; height: 83px">
                                    Фраза
                                </th>
                                <th class="table-header" style="min-width: 160px; height: 83px">
                                    Регион
                                </th>
                                <th class="table-header" style="min-width: 160px; max-width:160px; height: 83px">
                                    Посадочная страница
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    Позиция в топе
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    Баллы
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    Охват важных
                                    слова
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    Охват tf
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    Ширина
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    Плотность
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    Учитывать в
                                    расчёте общего
                                    балла
                                </th>
                                <th class="table-header"></th>
                            </tr>
                            </thead>
                            <tbody id="historyTbody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab_2">
                    @include('layouts.relevance-config')
                </div>
            </div>
        </div>
    </div>


    @slot('js')
        <script>
            $('input#switchMyListWords').click(function () {
                if ($(this).is(':checked')) {
                    $('.form-group.required.list-words.mt-1').show(300)
                } else {
                    $('.form-group.required.list-words.mt-1').hide(300)
                }
            })

            function getRegionName(id) {
                switch (id) {
                    case '1' :
                        return "{{ __('Moscow') }}";
                    case '20' :
                        return "{{ __('Arkhangelsk') }}";
                    case '37' :
                        return "{{ __('Astrakhan') }}";
                    case '197' :
                        return "{{ __('Barnaul') }}";
                    case '4' :
                        return "{{ __('Belgorod') }}";
                    case '77' :
                        return "{{ __('Blagoveshchensk') }}";
                    case '191' :
                        return "{{ __('Bryansk') }}";
                    case '24' :
                        return "{{ __('Veliky Novgorod') }}";
                    case '75' :
                        return "{{ __('Vladivostok') }}";
                    case '33' :
                        return "{{ __('Vladikavkaz') }}";
                    case '192' :
                        return "{{ __('Vladimir') }}";
                    case '38' :
                        return "{{ __('Volgograd') }}";
                    case '21' :
                        return "{{ __('Vologda') }}";
                    case '193' :
                        return "{{ __('Voronezh') }}";
                    case '1106' :
                        return "{{ __('Grozny') }}";
                    case '54' :
                        return "{{ __('Ekaterinburg') }}";
                    case '5' :
                        return "{{ __('Ivanovo') }}";
                    case '63' :
                        return "{{ __('Irkutsk') }}";
                    case '41' :
                        return "{{ __('Yoshkar-ola') }}";
                    case '43' :
                        return "{{ __('Kazan') }}";
                    case '22' :
                        return "{{ __('Kaliningrad') }}";
                    case '64' :
                        return "{{ __('Kemerovo') }}";
                    case '7' :
                        return "{{ __('Kostroma') }}";
                    case '35' :
                        return "{{ __('Krasnodar') }}";
                    case '62' :
                        return "{{ __('Krasnoyarsk') }}";
                    case '53' :
                        return "{{ __('Kurgan') }}";
                    case '8' :
                        return "{{ __('Kursk') }}";
                    case '9' :
                        return "{{ __('Lipetsk') }}";
                    case '28' :
                        return "{{ __('Makhachkala') }}";
                    case '213' :
                        return "{{ __('Moscow') }}";
                    case '23' :
                        return "{{ __('Murmansk') }}";
                    case '1092' :
                        return "{{ __('Nazran') }}";
                    case '30' :
                        return "{{ __('Nalchik') }}";
                    case '47' :
                        return "{{ __('Nizhniy Novgorod') }}";
                    case '65' :
                        return "{{ __('Novosibirsk') }}";
                    case '66' :
                        return "{{ __('Omsk') }}";
                    case '10' :
                        return "{{ __('Eagle') }}";
                    case '48' :
                        return "{{ __('Orenburg') }}";
                    case '49' :
                        return "{{ __('Penza') }}";
                    case '50' :
                        return "{{ __('Perm') }}";
                    case '25' :
                        return "{{ __('Pskov') }}";
                    case '39' :
                        return "{{ __('Rostov-on') }}";
                    case '11' :
                        return "{{ __('Ryazan') }}";
                    case '51' :
                        return "{{ __('Samara') }}";
                    case '42' :
                        return "{{ __('Saransk') }}";
                    case '2' :
                        return "{{ __('Saint-Petersburg') }}";
                    case '12' :
                        return "{{ __('Smolensk') }}";
                    case '239' :
                        return "{{ __('Sochi') }}";
                    case '36' :
                        return "{{ __('Stavropol') }}";
                    case '973' :
                        return "{{ __('Surgut') }}";
                    case '13' :
                        return "{{ __('Tambov') }}";
                    case '14' :
                        return "{{ __('Tver') }}";
                    case '67' :
                        return "{{ __('Tomsk') }}";
                    case '15' :
                        return "{{ __('Tula') }}";
                    case '195' :
                        return "{{ __('Ulyanovsk') }}";
                    case '172' :
                        return "{{ __('Ufa') }}";
                    case '76' :
                        return "{{ __('Khabarovsk') }}";
                    case '45' :
                        return "{{ __('Cheboksary') }}";
                    case '56' :
                        return "{{ __('Chelyabinsk') }}";
                    case '1104' :
                        return "{{ __('Cherkessk') }}";
                    case '16' :
                        return "{{ __('Yaroslavl') }}";

                }
            }
        </script>
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
