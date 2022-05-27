@component('component.card', ['title' =>  __('Create Queue') ])
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
                width: 460px;
            }

            .bg-warning-elem {
                background-color: #f5e2aa !important;
            }

            #unigramTBody > tr > td:nth-child(1) {
                text-align: center;
            }

            .ui_tooltip.__left, .ui_tooltip.__right {
                width: auto;
            }

            .pb-3.unigram thead th {
                position: sticky;
                top: 0;
                z-index: 1;
            }

            .pb-3.unigram tbody th {
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
    {!! Form::open(['action' =>'RelevanceController@createTaskQueue', 'method' => 'POST', 'class' => 'express-form'])!!}

    <div class="col-6 pt-3 pb-3">
        <label for="params">Посадочные страницы и ссылки</label>
        <textarea name="params" id="params" cols="30"
                  rows="10" class="form form-control"
                  placeholder="{{ __('Your landing page') }};{{ __('Keyword') }}{{ "\n" }}{{ __('Your landing page') }};{{ __('Keyword') }}"
        ></textarea>
        <span class="text-muted mb-3">Как это работает
            <span class="__helper-link ui_tooltip_w">
                <i class="fa fa-question-circle"></i>
                <span class="ui_tooltip __bottom ">
                    <span class="ui_tooltip_content">
                        Нужно ввести данные в формате:
                        <br><br>
                        Ссылка на посадочную страницу;ключевая фраза <br>
                        Ссылка на посадочную страницу2;ключевая фраза2
                        <br> <br>

                        И изменить конфигурацию формы так как вам потребуется. <br>
                        После нажатия кнопки "запустить очередь" ваши задачи будут помещены в очередь, которая состоит из очереди ваших задач и задач других пользователей.
                        Когда очередь дойдёт до вас, ваши проекты будут проанализированы и помещены на страницу с историей
                        <a href="{{ route('relevance.history') }}" target="_blank">тут</a>.
                    </span>
                </span>
            </span>
        </span>

        <div class="form-group required pt-3">
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

        <div class="form-group required d-flex align-items-center">
            <span>{{ __('Cut the words shorter') }}</span>
            <input type="number" class="form form-control col-2 ml-1 mr-1" name="separator"
                   id="separator"
                   value="{{ $config->separator }}">
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
                               name="noIndex"
                               @if($config->noindex == 'yes') checked @endif>
                        <label class="custom-control-label" for="switchNoindex"></label>
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
                               name="hiddenText"
                               @if($config->meta_tags == 'yes') checked @endif>
                        <label class="custom-control-label" for="switchAltAndTitle"></label>
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
                               name="conjunctionsPrepositionsPronouns"
                               @if($config->parts_of_speech == 'yes') checked @endif>
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
                               name="switchMyListWords"
                               @if($config->remove_my_list_words == 'yes') checked @endif>
                        <label class="custom-control-label" for="switchMyListWords"></label>
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

        <input type="hidden" name="countTasks" id="countTasks" value="1">
    </div>

    <div class="d-flex flex-column">
        <div class="btn-group col-lg-3 col-md-5 mb-2">
            <button class="btn btn-secondary" id="full-analyse">
                Запустить очередь
            </button>
        </div>
    </div>
    {!! Form::close() !!}
    @slot('js')
        <script>

        </script>
    @endslot
@endcomponent
