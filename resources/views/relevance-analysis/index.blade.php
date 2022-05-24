@component('component.card', ['title' =>  __('Relevance analysis') ])
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
    <div id="toast-container" class="toast-top-right error-message empty" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right success-message lock-word" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message" id="lock-word"></div>
        </div>
    </div>

    @if($admin)
        <form action="{{ route('changeConfig') }}" method="POST" class="card col-lg-5 col-md-12 p-0">
            @csrf
            <table class="table table-hover border-0 mb-0 pb-0">
                <tbody>
                <tr data-widget="expandable-table" aria-expanded="false">
                    <td class="d-flex justify-content-between border-0">
                        <div class="w-75">
                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                            <span class="short_project_description">Администрирование модуля</span>
                        </div>
                    </td>
                </tr>
                <tr class="expandable-body d-none">
                    <td>
                        <div style="display: none;">

                            <div class="form-group required">
                                <label>Выбрать значение по умолчанию Top 10/20</label>
                                {!! Form::select('count', array_unique([
                                        $config->count_sites => $config->count_sites,
                                        '10' => 10,
                                        '20' => 20,
                                        ]), null, ['class' => 'custom-select rounded-0']) !!}
                            </div>

                            <div class="form-group required">
                                <label>Выбрать регион по умолчанию</label>
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
                                        ]), null, ['class' => 'custom-select rounded-0']) !!}
                            </div>

                            <div class="form-group required">
                                <label>Список игнорируемых доменов по умолчанию</label>
                                {!! Form::textarea("ignored_domains", $config->ignored_domains ,["class" => "form-control"] ) !!}
                            </div>

                            <div class="form-group required d-flex align-items-center">
                                <span>Количество обрезаемых символов по умолчанию</span>
                                <input type="number" class="form form-control col-2 ml-1 mr-1" name="separator"
                                       id="separator" value="{{ $config->separator }}">
                            </div>

                            <div class="mt-3 mb-3">
                                <div class="mt-3 mb-3">
                                    <p>Скрыть игнорируемые домены</p>
                                    {!! Form::select('hide_ignored_domains', array_unique([
                                            $config->hide_ignored_domains => $config->hide_ignored_domains,
                                            'yes' => __('yes'),
                                            'no' => __('no'),
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="mt-3 mb-3">
                                    <p>Отслеживать текст в теге noindex по умолчанию</p>
                                    {!! Form::select('noindex', array_unique([
                                            $config->noindex => $config->noindex,
                                            'yes' => __('yes'),
                                            'no' => __('no'),
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="mt-3 mb-3">
                                    <p>Отслеживать слова в атрибутах alt, tittle и data-text по умолчанию</p>
                                    {!! Form::select('meta_tags', array_unique([
                                            $config->meta_tags => $config->meta_tags,
                                            'yes' => __('yes'),
                                            'no' => __('no'),
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="mt-3 mb-3">
                                    <p>Отслеживать союзы, предлоги, местоимение по умолчанию</p>
                                    {!! Form::select('parts_of_speech', array_unique([
                                            $config->parts_of_speech => $config->parts_of_speech,
                                            'yes' => __('yes'),
                                            'no' => __('no'),
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="mt-3 mb-3">
                                    <div>
                                        Исключать слова по умолчанию
                                    </div>

                                    {!! Form::select('remove_my_list_words', array_unique([
                                            $config->remove_my_list_words => $config->remove_my_list_words,
                                            'yes' => __('yes'),
                                            'no' => __('no'),
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="form-group required list-words mt-1">
                                    <label for="my_list_words">Список исключаемых слов</label>
                                    {!! Form::textarea('my_list_words', $config->my_list_words ,['class' => 'form-control', 'cols' => 8, 'rows' => 5]) !!}
                                </div>
                            </div>

                            <div class="mt-3 mb-3">
                                <div class="mt-3 mb-3">
                                    <p>Количество записей в таблице tlp по умолчанию</p>
                                    {!! Form::select('ltp_count', array_unique([
                                            $config->ltp_count => $config->ltp_count,
                                            '10' => 10,
                                            '25' => 25,
                                            '50' => 50,
                                            '100' => 100,
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="mt-3 mb-3">
                                    <p>Количество записей в таблице tlps по умолчанию</p>
                                    {!! Form::select('ltps_count', array_unique([
                                            $config->ltps_count => $config->ltps_count,
                                            '10' => 10,
                                            '25' => 25,
                                            '50' => 50,
                                            '100' => 100,
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="mt-3 mb-3">
                                    <p>Количество записей в таблице проанализированных сайтов по умолчанию</p>
                                    {!! Form::select('scanned_sites_count', array_unique([
                                            $config->scanned_sites_count => $config->scanned_sites_count,
                                            '10' => 10,
                                            '25' => 25,
                                            '50' => 50,
                                            '100' => 100,
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>

                                <div class="mt-3 mb-3">
                                    <p>Количество записей в таблице рекомендаций по умолчанию</p>
                                    {!! Form::select('recommendations_count', array_unique([
                                            $config->recommendations_count => $config->recommendations_count,
                                            '10' => 10,
                                            '25' => 25,
                                            '50' => 50,
                                            '100' => 100,
                                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                </div>
                            </div>

                            <div class="d-flex mt-3 mb-3">
                                <div>
                                    <label for="boostPercent">добавить % к охвату</label>
                                    <input name="boostPercent" type="number" class="form form-control"
                                           value="{{ $config->boostPercent }}">
                                </div>
                            </div>
                            <input type="submit" value="Изменить стартовую конфигурацию" class="btn btn-secondary">
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    @endif

    <div class="col-5 pb-3">
        <div class="form-group required">
            <label>{{ __('Your landing page') }}</label>
            {!! Form::text("link", null ,["class" => "form-control link", "required"]) !!}
        </div>

        <div class="form-group required">
            <label>{{ __('Тип проверки') }}</label>
            {!! Form::select('type', [
                'phrase' => 'Ключевая фраза',
                'list' => 'Список сканируемых сайтов',
                ], null, ['class' => 'custom-select rounded-0', 'id' => 'check-type']) !!}
        </div>

        <div id="site-list" style="display: none">
            <div class="form-group required">
                <label>{{ __('Список сайтов') }}</label>
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
                {!! Form::select('count', array_unique([
                        $config->count_sites => $config->count_sites,
                        '10' => 10,
                        '20' => 20,
                        ]), null, ['class' => 'custom-select rounded-0 count']) !!}
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
                {!! Form::textarea("ignoredDomains", $config->ignored_domains,["class" => "form-control ignoredDomains"] ) !!}
            </div>
        </div>

        <div class="form-group required d-flex align-items-center">
            <span>{{ __('Cut the words shorter') }}</span>
            <input type="number" class="form form-control col-2 ml-1 mr-1" name="separator" id="separator"
                   value="{{ $config->separator }}">
            <span>{{ __('symbols') }}</span>
        </div>

        <div class="switch mt-3 mb-3">
            <div class="d-flex">
                <div class="__helper-link ui_tooltip_w">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="switchNoindex"
                               name="noIndex"
                               @if($config->noindex == 'yes') checked @endif>
                        <label class="custom-control-label" for="switchNoindex"></label>
                    </div>
                </div>
                <p>{{ __('Track the text in the noindex tag') }}</p>
            </div>
            <div class="d-flex">
                <div class="__helper-link ui_tooltip_w">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="switchAltAndTitle"
                               name="hiddenText"
                               @if($config->meta_tags == 'yes') checked @endif>
                        <label class="custom-control-label" for="switchAltAndTitle"></label>
                    </div>
                </div>
                <p>{{ __('Track words in the alt, title, and data-text attributes') }}</p>
            </div>
            <div class="d-flex">
                <div class="__helper-link ui_tooltip_w">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="switchConjunctionsPrepositionsPronouns"
                               name="conjunctionsPrepositionsPronouns"
                               @if($config->parts_of_speech == 'yes') checked @endif>
                        <label class="custom-control-label" for="switchConjunctionsPrepositionsPronouns"></label>
                    </div>
                </div>
                <p>{{ __('Track conjunctions, prepositions, pronouns') }}</p>
            </div>
            <div class="d-flex">
                <div class="__helper-link ui_tooltip_w">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="switchMyListWords"
                               name="switchMyListWords"
                               @if($config->remove_my_list_words == 'yes') checked @endif>
                        <label class="custom-control-label" for="switchMyListWords"></label>
                    </div>
                </div>
                <span>{{ __('Exclude') }}<span class="text-muted">{{ __('(your own list of words)') }}</span></span>
            </div>
            <div class="form-group required list-words mt-1"
                 @if($config->remove_my_list_words == 'no') style="display:none;" @endif >
                {!! Form::textarea('listWords', $config->my_list_words,['class' => 'form-control listWords', 'cols' => 8, 'rows' => 5]) !!}
            </div>
        </div>
    </div>

    <div class="d-flex flex-column">
        <div class="btn-group col-lg-3 col-md-5 mb-2">
            <button class="btn btn-secondary" id="full-analyse">
                {{ __('Full analysis') }}
            </button>
            <button type="button" class="btn btn-secondary col-2">
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right">
                        <span class="ui_tooltip_content">
                            {{ __('A survey of the xml service will be conducted in order to get the relevant top sites of competitors. The landing page will also be parsed.') }} <br>
                            {{ __('Based on all the data received, an analysis will be performed.') }} <br>
                        </span>
                    </span>
                </span>
            </button>
        </div>
        <div class="btn-group col-lg-3 col-md-5 mb-2">
            <button type="button" class="btn btn-secondary" id="repeat-relevance-analyse">
                {{ __('Repeated analysis of competitor sites') }}
            </button>
            <button type="button" class="btn btn-secondary col-2">
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right">
                        <span class="ui_tooltip_content">
                            {{ __('Updating the content of competitors that was received as a result of the last request') }}
                        </span>
                    </span>
                </span>
            </button>
        </div>
        <div class="btn-group col-lg-3 col-md-5 mb-2">
            <button class="btn btn-secondary" id="repeat-main-page-analyse">
                {{ __('Repeated analysis of the landing page') }}
            </button>
            <button type="button" class="btn btn-secondary col-2">
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right">
                        <span
                            class="ui_tooltip_content">{{ __('We re-poll the landing page and take data from competitors websites that were received as a result of the last request') }}</span>
                    </span>
                </span>
            </button>
        </div>
    </div>

    <div id="progress-bar">
        <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
    </div>

    <div class="pb-3 pt-3 text" style="display:none">
        <h3>{{ __('Comparing the amount of text') }}</h3>
        <table class="table table-bordered table-striped dataTable dtr-inline">
            <thead>
            <tr>
                <th class="col-3"></th>
                <th>{{ __('Average values of competitors') }}</th>
                <th>{{ __('Landing Page Values') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <b>{{ __('Number of words') }}</b>
                </td>
                <td id="avgCountWords"></td>
                <td id="mainPageCountWords"></td>
            </tr>
            <tr>
                <td><b>{{ __('Number of characters') }}</b></td>
                <td id="avgCountSymbols"></td>
                <td id="mainPageCountSymbols"></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="pb-3 clouds" style="display:none;">
        <h3>{{ __('The clouds') }}</h3>
        <div class="d-flex flex-column pb-3">
            <button id="tf-idf-clouds" class="btn btn-secondary col-lg-3 col-md-5 mb-3" style="cursor: pointer">
                Облака tf-idf сайтов из топа и посадочной страницы
            </button>
            <div class="tf-idf-clouds" style="display: none">
                <div class="d-lg-flex mt-4 justify-content-around">

                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('Average tf-idf values of links and competitor text') }}</span>
                        <div style="height: 350px" id="competitorsTfCloud" class="generated-cloud"></div>
                    </div>

                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('TF-idf values of links and landing page text') }}</span>
                        <div style="height: 350px" id="mainPageTfCloud" class="generated-cloud"></div>
                    </div>

                </div>
                <div class="d-lg-flex mt-4 justify-content-around">

                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('Average tf-idf values of competitors text') }}</span>
                        <div style="height: 350px" id="competitorsTextTfCloud" class="generated-cloud"></div>
                    </div>

                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('TF-idf values of the landing page text') }}</span>
                        <div style="height: 350px" id="mainPageTextTfCloud" class="generated-cloud"></div>
                    </div>

                </div>
                <div class="d-lg-flex mt-4 justify-content-around">

                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('Average tf-idf values of competitor links') }}</span>
                        <div style="height: 350px" id="competitorsLinksTfCloud" class="generated-cloud"></div>
                    </div>

                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('TF-idf values of landing page links') }}</span>
                        <div style="height: 350px" id="mainPageLinksTfCloud" class="generated-cloud"></div>
                    </div>

                </div>
            </div>
            <button id="text-clouds" class="btn btn-secondary col-lg-3 col-md-5" style="cursor: pointer;">
                Облака текста сайтов из топа и посадочной страницы
            </button>
            <div class="text-clouds" style=" display: none">
                <div class="d-lg-flex mt-4 justify-content-around">
                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('Competitors Link Zone') }}</span>
                        <div style="height: 350px" id="competitorsLinksCloud" class="generated-cloud"></div>
                    </div>
                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('The link zone of your page') }}</span>
                        <div style="height: 350px" id="mainPageLinksCloud" class="generated-cloud"></div>
                    </div>
                </div>
                <div class="d-lg-flex mt-4 justify-content-around">
                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('Competitors text area') }}</span>
                        <div style="height: 350px" id="competitorsTextCloud" class="generated-cloud"></div>
                    </div>
                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('The text area of your page') }}</span>
                        <div style="height: 350px" id="mainPageTextCloud" class="generated-cloud"></div>
                    </div>
                </div>
                <div class="d-lg-flex mt-4 justify-content-around">
                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('Competitors Link and Text area') }}</span>
                        <div style="height: 350px" id="competitorsTextAndLinksCloud" class="generated-cloud"></div>
                    </div>
                    <div class="col-lg-5 col-md-10">
                        <span>{{ __('The zone of links and text of your page') }}</span>
                        <div style="height: 350px" id="mainPageTextWithLinksCloud" class="generated-cloud"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="rec" style="display: none" class="mb-3">
        <h2>Рекомендации TLP и проверка на спам</h2>
        <button class="btn btn-secondary" id="recButton">Показать</button>
    </div>

    <div class="pb-3 recommendations" style="display:none;">
        <table id="recommendations" class="table table-bordered table-hover dataTable dtr-inline"
               style="width: 100% !important;">
            <thead>
            <tr style="position: relative; z-index: 100">
                <th class="сol-1">
                        <span class="text-muted" style="font-weight: 400">
                            Вы можете удалить слово из таблицы, если оно было проработано
                        </span>
                </th>
                <th>Слово</th>
                <th>Tf</th>
                <th>Среднее кол-во повторений у конкурентов</th>
                <th>Количество у вас на странице</th>
                <th>Рекомендуемый диапозон</th>
                <th>Уровень спама</th>
                <th>Добавить</th>
                <th>Удалить</th>
            </tr>
            </thead>
            <tbody id="recommendationsTBody">
            </tbody>
        </table>
    </div>

    <div class="pb-3 unigram" style="display: none; margin-top: 50px">
        <h2>Топ лист фраз (TLP)</h2>
        <table id="unigram" class="table table-bordered table-hover dataTable dtr-inline"
               style="width: 100% !important;">
            <thead>
            <tr>
                <th></th>
                <th class="font-weight-normal text-muted">{{ __('Ranges for filtering the table') }}</th>
                <th>
                    <div style="width: 90px">
                        <input class="w-100" type="number" name="minTF" id="minTF" placeholder="min">
                        <input class="w-100" type="number" name="maxTF" id="maxTF" placeholder="max">
                    </div>
                </th>
                <th>
                    <div style="width: 90px">
                        <input class="w-100" type="number" name="minIdf" id="minIdf" placeholder="min">
                        <input class="w-100" type="number" name="maxIdf" id="maxIdf" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minInter" id="minInter" placeholder="min">
                        <input class="w-100" type="number" name="maxInter" id="maxInter" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minReSpam" id="minReSpam" placeholder="min">
                        <input class="w-100" type="number" name="maxReSpam" id="maxReSpam" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minAVG" id="minAVG" placeholder="min">
                        <input class="w-100" type="number" name="maxAVG" id="maxAVG" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minAVGText" id="minAVGText" placeholder="min">
                        <input class="w-100" type="number" name="maxAVGText" id="maxAVGText" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minInYourPage" id="minInYourPage" placeholder="min">
                        <input class="w-100" type="number" name="maxInYourPage" id="maxInYourPage" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minTextIYP" id="minTextIYP" placeholder="min">
                        <input class="w-100" type="number" name="maxTextIYP" id="maxTextIYP" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minAVGLink" id="minAVGLink" placeholder="min">
                        <input class="w-100" type="number" name="maxAVGLink" id="maxAVGLink" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minLinkIYP" id="minLinkIYP" placeholder="min">
                        <input class="w-100" type="number" name="maxLinkIYP" id="maxLinkIYP" placeholder="max">
                    </div>
                </th>
            </tr>
            <tr style="position: relative; z-index: 100">
                <th></th>
                <th>
                    {{ __('Words') }}<span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right">
                            <span class="ui_tooltip_content" style="text-align: right">{{ __('Words and their word forms that are present on competitors websites.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>Tf<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>Idf<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>
                    {{ __('Intersection') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of sites in which the word is present.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>{{ __('Re - spam') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The maximum number of repetitions found on the competitors website.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>{{ __('Average number of repetitions in the text and links') }}<span
                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text and links of your competitors.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>{{ __('The total number of repetitions in the text and links') }}<span
                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The total number of repetitions on your page in links and text.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>{{ __('Average number of repetitions in the text') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text of your competitors.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>{{ __('Number of repetitions in text') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of repetitions in the text on your page.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>{{ __('Average number of repetitions in links') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the links of your competitors.') }}
                                </span>
                            </span>
                        </span>
                </th>
                <th>{{ __('Number of repetitions in links') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of repetitions in the links on your page.') }}
                                </span>
                            </span>
                        </span>
                </th>
            </tr>
            </thead>
            <tbody id="unigramTBody">
            </tbody>
        </table>
    </div>

    <div class="phrases" style="display:none;">
        <h3>Топ лист словосочетаний (TLPs)</h3>
        <table id="phrases" class="table table-bordered table-hover dataTable dtr-inline w-100">
            <thead>
            <tr>
                <th class="font-weight-normal text-muted">{{ __('Ranges for filtering the table') }}</th>
                <th>
                    <div style="width: 90px">
                        <input class="w-100" type="number" id="phrasesMinTF" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxTF" placeholder="max">
                    </div>
                </th>
                <th>
                    <div style="width: 90px">
                        <input class="w-100" type="number" id="phrasesMinIdf" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxIdf" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinInter" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxInter" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinReSpam" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxReSpam" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinAVG" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxAVG" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinAVGText" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxAVGText" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinInYourPage"
                               placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxInYourPage"
                               placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinTextIYP" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxTextIYP" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinAVGLink" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxAVGLink" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" id="phrasesMinLinkIYP" placeholder="min">
                        <input class="w-100" type="number" id="phrasesMaxLinkIYP" placeholder="max">
                    </div>
                </th>
            </tr>
            <tr style="position: relative; z-index: 100;">
                <th>Фраза</th>
                <th>tf</th>
                <th>idf</th>
                <th>Пересечение</th>
                <th>Переспам</th>
                <th>Среднее количество повторений в тексте и ссылках</th>
                <th>Общее колиество повторений в тексте и ссылках</th>
                <th>Среднее количество повторений в тексте</th>
                <th>Количество повторений в тексте</th>
                <th>Среднее количество поторений в ссылках</th>
                <th>Количество поторений в ссылках</th>
            </tr>
            </thead>
            <tbody id="phrasesTBody">
            </tbody>
        </table>
    </div>

    <div class="sites" style="display:none; margin-top:50px;">
        <h3>{{ __('Analyzed sites') }}</h3>
        <table id="scaned-sites" class="table table-bordered table-hover dataTable dtr-inline">
            <thead>
            <tr role="row" style="position: relative; z-index: 100">
                <th>{{ __('Position in the top') }}</th>
                <th>{{ __('Domain') }}</th>
                <th>
                    Общий балл
                    @if($admin)
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Общий балл рассчитывается следующим образом: охват по важным словам + охват по tf + плотность<br>
                                    Полученная сумма сначала делится на 3, затем умножается на 2<br>
                                    - <br>
                                    Если полученное кол-во баллов больше 100, то мы приравниваем его к 100.<br>
                                </span>
                            </span>
                        </span>
                    @endif
                </th>
                <th>{{ __('coverage for all important words') }}
                    @if($admin)
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Из таблицы униграм берутся все слова (далее эти слова именуются "важные слова") <br>
                                    Для каждого отдельно взятого сайта происходит проверка наличия в нём слов, которые считаются важными <br>
                                    Если важное слово присутсвует в проверяемом сайте, то он получает за него 1 балл<br>
                                    Полученый процент равен сумме полученых баллов делённой на 600
                                </span>
                            </span>
                        </span>
                    @endif
                </th>
                <th>{{ __('Coverage by tf') }}
                    @if($admin)
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Из таблицы униграм берутся все слова и их значения tf(далее эти слова именуются "важные слова") <br>
                                    Для каждого отдельно взятого сайта происходит проверка наличия в нём слов, которые считаются важными <br>
                                    Если важное слово присутсвует в проверяемом сайте, то он получает за него балл равный tf из таблицы униграм <br>
                                    Общая сумма баллов каждого конкретного сайта делиться на общую сумму tf из таблицы униграм, таким образом мы получаем % охвата
                                </span>
                            </span>
                        </span>
                    @endif
                </th>
                <th>{{ __('Width') }}
                    @if($admin)
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Для вычисления  ширины, беруться первые 10 не игнорируемых сайтов (позиция в топе) <br>
                                    Их охват по всем словам(%) плюсуется и делиться на 10, для того чтобы выявить 100% ширину <br>
                                    В соответствии с этими 100% для каждого сайта ширина просчитывается  отдельно
                                </span>
                            </span>
                        </span>
                    @endif
                </th>
                <th>
                    {{ __('Density') }}
                    @if($admin)
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Плотность высчитывается от значения средней по ТОПу для КАЖДОЙ ОСНОВНОЙ ФРАЗЫ. <br>
                                    Если в средней 20, а у нас 5, то это 25 баллов. <br>
                                    Дальше все баллы для всех фраз складываются и делятся на общее количество слов. <br>
                                    - <br>
                                    Если мы переспамили, то пока в этом варианте мы никак не учитываем этот момент, фраза просто получает 100 баллов по плотности. <br>
                                </span>
                            </span>
                        </span>
                    @endif
                </th>
                <th>Общее количество символов</th>
                <th>{{ __('Result') }}</th>
            </tr>
            </thead>
            <tbody id="scanned-sites-tbody">
            </tbody>
        </table>
    </div>

    <div class="pb-3" id="competitorsTfClouds" style="display: none !important;">
        <div class="align-items-end clouds-div">
            <button class="btn btn-secondary col-lg-3 col-md-5" id="coverage-clouds-button">
                Облака первых 200 важных (по tf-idf) слов у конкурентов
            </button>
        </div>
        <div style="display: none" id="coverage-clouds" class="pt-2">
            <div class='d-flex w-100'>
                <div class='__helper-link ui_tooltip_w'>
                    <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>
                        <input type='checkbox'
                               class='custom-control-input'
                               id='showOrHideIgnoredClouds'>
                        <label class='custom-control-label' for='showOrHideIgnoredClouds'></label>
                    </div>
                </div>
                <p>скрыть игнорируемые домены</p>
            </div>
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/canvasjs/js/canvasjs.js') }}"></script>
        <script src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV4/renderClouds.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV4/renderUnigramTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV4/renderScannedSitesList.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV4/renderTextTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV4/renderPhrasesTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV4/renderRecommendationsTable.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            $('#recButton').click(function () {
                if ($('.pb-3.recommendations').is(':visible')) {
                    $('.pb-3.recommendations').hide()
                    $('#recButton').html('Показать')
                } else {
                    $('.pb-3.recommendations').show()
                    $('#recButton').html('Скрыть')
                }
            });

            $('#check-type').on('change', function () {
                if ($(this).val() === 'list') {
                    $('#key-phrase').hide()
                    $('#site-list').show(300)
                } else {
                    $('#site-list').hide()
                    $('#key-phrase').show(300)
                }
            });

            $('input#switchMyListWords').click(function () {
                if ($(this).is(':checked')) {
                    $('.form-group.required.list-words.mt-1').show(300)
                } else {
                    $('.form-group.required.list-words.mt-1').hide(300)
                }
            })

            $('#tf-idf-clouds').click(() => {
                if ($('.tf-idf-clouds').is(':visible')) {
                    $('.tf-idf-clouds').hide()
                } else {
                    $('.tf-idf-clouds').show()
                }
            })

            $('#text-clouds').click(() => {
                if ($('.text-clouds').is(':visible')) {
                    $('.text-clouds').hide()
                } else {
                    $('.text-clouds').show()
                }
            })
        </script>
        <script>
            var generatedTfIdf = false
            var generatedText = false
            var generatedCompetitorCoverage = false

            $('#full-analyse').click(() => {
                var interval = startProgressBar()
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('analysis.relevance') }}",
                    data: {
                        type: $('#check-type').val(),
                        siteList: $('#siteList').val(),
                        separator: $('#separator').val(),
                        link: $('.form-control.link').val(),
                        phrase: $('.form-control.phrase').val(),
                        noIndex: $('#switchNoindex').is(':checked'),
                        listWords: $('.form-control.listWords').val(),
                        count: $('.custom-select.rounded-0.count').val(),
                        region: $('.custom-select.rounded-0.region').val(),
                        hiddenText: $('#switchAltAndTitle').is(':checked'),
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ignoredDomains: $('.form-control.ignoredDomains').val(),
                        switchMyListWords: $('#switchMyListWords').is(':checked'),
                        conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked')
                    },
                    beforeSend: function () {
                        refreshAllRenderElements()
                        $('#full-analyse').prop("disabled", true);
                        $('#repeat-main-page-analyse').prop("disabled", true);
                        $('#repeat-relevance-analyse').prop("disabled", true);
                    },
                    success: function (response) {
                        successRequest(response, interval)
                    },
                    error: function (response) {
                        if (response.responseText) {
                            var block = false;
                            prepareMessage(response)
                        } else {
                            $('.toast-message.error-message').html("{{ __('An error has occurred, repeat the request.') }}")
                        }

                        $('.toast-top-right.error-message.empty').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.error-message.empty').hide(300)
                        }, 5000)

                        errorRequest(interval, block)
                    }
                });
            })

            $('#repeat-main-page-analyse').click(() => {
                var interval = startProgressBar()
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('repeat.main.page.analysis') }}",
                    data: {
                        type: $('#check-type').val(),
                        siteList: $('#siteList').val(),
                        separator: $('#separator').val(),
                        link: $('.form-control.link').val(),
                        phrase: $('.form-control.phrase').val(),
                        noIndex: $('#switchNoindex').is(':checked'),
                        listWords: $('.form-control.listWords').val(),
                        count: $('.custom-select.rounded-0.count').val(),
                        region: $('.custom-select.rounded-0.region').val(),
                        hiddenText: $('#switchAltAndTitle').is(':checked'),
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ignoredDomains: $('.form-control.ignoredDomains').val(),
                        switchMyListWords: $('#switchMyListWords').is(':checked'),
                        conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked')
                    },
                    beforeSend: function () {
                        refreshAllRenderElements()
                        $('#full-analyse').prop("disabled", true);
                        $('#repeat-main-page-analyse').prop("disabled", true);
                        $('#repeat-relevance-analyse').prop("disabled", true);
                    },
                    success: function (response) {
                        successRequest(response, interval)
                    },
                    error: function (response) {
                        if (response.responseText) {
                            var block = false;
                            prepareMessage(response)
                        } else {
                            $('.toast-message.error-message').html("{{ __('An error has occurred, repeat the request.') }}")
                        }

                        $('.toast-top-right.error-message.empty').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.error-message.empty').hide(300)
                        }, 5000)

                        errorRequest(interval, block)
                    }
                });
            })

            $('#repeat-relevance-analyse').click(() => {
                var interval = startProgressBar()
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('repeat.relevance.analysis') }}",
                    data: {
                        type: $('#check-type').val(),
                        siteList: $('#siteList').val(),
                        separator: $('#separator').val(),
                        link: $('.form-control.link').val(),
                        phrase: $('.form-control.phrase').val(),
                        noIndex: $('#switchNoindex').is(':checked'),
                        listWords: $('.form-control.listWords').val(),
                        count: $('.custom-select.rounded-0.count').val(),
                        region: $('.custom-select.rounded-0.region').val(),
                        hiddenText: $('#switchAltAndTitle').is(':checked'),
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ignoredDomains: $('.form-control.ignoredDomains').val(),
                        switchMyListWords: $('#switchMyListWords').is(':checked'),
                        conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked')
                    },
                    beforeSend: function () {
                        refreshAllRenderElements()
                        $('#full-analyse').prop("disabled", true);
                        $('#repeat-main-page-analyse').prop("disabled", true);
                        $('#repeat-relevance-analyse').prop("disabled", true);
                    },
                    success: function (response) {
                        successRequest(response, interval)
                    },
                    error: function (response) {
                        if (response.responseText) {
                            var block = false
                            prepareMessage(response)
                        } else {
                            $('.toast-message.error-message').html("{{ __('An error has occurred, repeat the request.') }}")
                        }

                        $('.toast-top-right.error-message.empty').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.error-message.empty').hide(300)
                        }, 5000)

                        errorRequest(interval, block)
                    }
                });
            })

            function successRequest(response, interval) {
                sessionStorage.setItem('hideDomains', response.hide_ignored_domains)
                stopProgressBar(interval)
                renderTextTable(response.avg, response.mainPage)
                renderRecommendationsTable(response.recommendations, response.recommendations_count)
                renderUnigramTable(response.unigramTable, response.ltp_count);
                renderPhrasesTable(response.phrases, response.ltps_count)
                renderScannedSitesList(response.sites, response.avgCoveragePercent, response.scanned_sites_count, response.hide_ignored_domains, response.boostPercent);
                renderClouds(response.clouds.competitors, response.clouds.mainPage, response.tfCompClouds, response.hide_ignored_domains);
                $("#full-analyse").prop("disabled", false);
                $("#repeat-main-page-analyse").prop("disabled", false);
                $("#repeat-relevance-analyse").prop("disabled", false);
            }

            function errorRequest(interval) {
                stopProgressBar(interval)
                $("#full-analyse").prop("disabled", false);
                $("#repeat-main-page-analyse").prop("disabled", false);
                $("#repeat-relevance-analyse").prop("disabled", false);
            }

            function refreshAllRenderElements() {
                $('#recButton').html('Показать')
                if (generatedCompetitorCoverage) {
                    $('#coverage-clouds-button').trigger('click')
                    if (sessionStorage.getItem('hideDomains') === 'yes') {
                        $("#showOrHideIgnoredClouds").prop("checked", false);
                    }
                }
                generatedTfIdf = false
                generatedText = false
                generatedCompetitorCoverage = false
                $(".generated-cloud").html("")
                $("#clouds").html("")
                $("#recommendations").dataTable().fnDestroy();
                $("#unigram").dataTable().fnDestroy();
                $("#scaned-sites").dataTable().fnDestroy();
                $("#phrases").dataTable().fnDestroy();
                $('.render').remove();
                $('.text').hide()
                $('.unigram').hide()
                $('.sites').hide()
                $('.clouds').hide()
                $('.phrases').hide()
                $('#rec').hide()
                $('.pb-3.recommendations').hide()
                $('#competitorsTfClouds').hide()
            }

            function setProgressBarStyles(percent) {
                let bar = $('.progress-bar')
                bar.css({
                    width: percent + '%'
                })
                bar.html(percent + '%');
            }

            function stopProgressBar(interval) {
                window.clearInterval(interval)
                setProgressBarStyles(100)
                setTimeout(() => {
                    $('.progress-bar').css({
                        opacity: 0,
                        width: 0 + '%'
                    });
                    $("#progress-bar").hide(300)
                }, 3000)

            }

            function startProgressBar() {
                let percent = 0;
                $('.progress-bar').css({
                    opacity: 1
                });

                $("#progress-bar").show(300)
                return setInterval(() => {
                    percent += Math.random();
                    setProgressBarStyles(percent.toFixed(2))
                }, 1321)
            }

            function prepareMessage(response) {
                let message = ''
                let messages = JSON.parse(response.responseText);
                try {
                    if (messages['errors']['link']) {
                        message += messages['errors']['link'] + "<br>"
                    }
                    if (messages['errors']['phrase']) {
                        message += messages['errors']['phrase'] + "<br>"
                    }
                    if (messages['errors']['siteList']) {
                        message += messages['errors']['siteList'] + "<br>"
                    }
                } catch (e) {
                    message += messages.countError + "<br>"
                }

                $('.toast-message.error-message').html(message)
            }
        </script>
    @endslot
@endcomponent
