@component('component.card', ['title' =>  __('Cluster') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <style>
            #clusters-table > tbody > tr > td > table > thead:hover {
                background: transparent !important;
            }

            .centered-text {
                text-align: center;
                vertical-align: inherit;
            }

            .dataTables_info, .hidden-result-table_filter {
                display: none;
            }

            .bg-cluster-warning {
                background: rgba(245, 226, 170, 0.5);
            }

            .text-primary {
                color: #007bff !important;
            }

            .Clusters {
                background: oldlace;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message">
        <div class="toast toast-success" aria-live="polite" style="display:none;">
            <div class="toast-message success-msg"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message" style="z-index: 99999 !important;">
        <div class="toast toast-error" aria-live="assertive" style="display:none;">
            <div class="toast-message error-msg">
                {{ __('An unexpected error has occurred, please contact the administrator') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="saveUrlsModal" tabindex="-1" aria-labelledby="saveUrlsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveUrlsModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label
                        for="relevanceUrls">{{ __('Select the url that will be saved for each phrase of this cluster') }}</label>
                    <select name="relevanceUrls" id="relevanceUrls" class="select custom-select"></select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="save-cluster-url-button"
                            data-dismiss="modal">{{ __('Save') }}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link"
                       href="{{ route('cluster.projects') }}">{{ __('My projects') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link text-primary" href="{{ route('cluster.configuration') }}">
                            {{ __('Module administration') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <p>
                        <button class="btn btn-secondary click_tracking" data-click="Classic mode" id="classicMode">
                            {{ __('Classic mode') }}
                        </button>

                        <button class="btn btn-outline-secondary click_tracking" data-click="Pro mode"
                                id="ProfessionalMode">
                            {{ __('Pro mode') }}
                        </button>
                    </p>
                    <div class="w-50 pb-3">

                        <div id="toast-container" class="toast-top-right success-message dont-worry-notification"
                             style="display:none;">
                            <div class="toast toast-info" aria-live="polite">
                                <div class="toast-message">
                                    {{ __("If your analysis is \"hanging\" at 50% for a long time, don't worry, it's just waiting in line to process") }}
                                </div>
                            </div>
                        </div>

                        <div id="toast-container" class="toast-top-right success-message history-notification"
                             style="display: none">
                            <div class="toast toast-info" aria-live="polite">
                                <div class="toast-message">
                                    {{ __('You can close the page or start a new analysis, when your results are ready, you can view them') }}
                                    <a href="{{ route('cluster.projects') }}"
                                       target="_blank"><u>{{ __('here') }}</u></a>
                                </div>
                            </div>
                        </div>

                        <div id="pro" style="display: none">
                            <div class="form-group required">
                                <label>{{ __('Region') }}</label>
                                {!! Form::select('region', array_unique([
                                    $config->region => $config->region,
                                  '213' => __('Moscow'),
                                   '1' => __('Moscow and the area'),
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
                                   '10649' => __('Stary Oskol'),
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
                               ]), null, ['class' => 'custom-select rounded-0', 'id' => 'region']) !!}
                            </div>

                            <div class="form-group required">
                                <label>{{ __('TOP') }}</label>
                                {!! Form::select('count', array_unique([
                                   $config->count => $config->count,
                                    '10' => 10,
                                    '20' => 20,
                                    '30' => 30,
                                    '40' => 40,
                                    '50' => 50,
                                ]), null, ['class' => 'custom-select rounded-0', 'id' => 'count']) !!}
                            </div>

                            <div class="form-group required" id="phrases-form-block">
                                <div class="d-flex justify-content-between">
                                    <label>{{ __('Key phrases') }}</label>
                                    <span class="text-muted">{{ __('Count phrases') }}:
                <span id="list-phrases-counter">0</span>
            </span>
                                </div>
                                {!! Form::textarea('phrases', null, ['class' => 'form-control', 'id'=>'phrases'] ) !!}
                            </div>

                            <div class="form-group required">
                                <label for="ignoredDomains">{{ __('Ignored domains') }}</label>
                                <textarea class="form form-control" name="ignoredDomains" id="ignoredDomains" cols="8"
                                          rows="8">{{ $config->ignored_domains }}</textarea>
                            </div>

                            <div id="ignoredWordsBlock">
                                <div class="form-group required">
                                    <label for="ignoredWords">{{ __('Ignored words') }}</label>
                                    <textarea class="form form-control" name="ignoredWords" id="ignoredWords" cols="8"
                                              rows="8">{{ $config->ignored_words }}</textarea>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label>{{ __('clustering level') }}</label>
                                <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle" style="color: grey"></i>
            <span class="ui_tooltip __right">
                <span class="ui_tooltip_content" style="width: 300px">
                    {{ __('the higher the clustering level, the more groups you will get') }}
                </span>
            </span>
        </span>
                                {!! Form::select('clustering_level', [
                                    $config->clustering_level => $config->clustering_level,
                                    'light' => 'light',
                                    'soft' => 'soft',
                                    'pre-hard' => 'pre-hard',
                                    'hard' => 'hard',
                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel']) !!}
                            </div>

                            <div class="form-group required">
                                <label for="brutForce">{{ __('Additional bulkhead') }}</label>
                                <input type="checkbox" name="brutForce" id="brutForce"
                                       @if($config->brut_force) checked @endif>
                                <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle" style="color: grey"></i>
            <span class="ui_tooltip __right">
                <span class="ui_tooltip_content" style="width: 300px">
                    {{ __('Phrases that, after clustering, did not get into the cluster will be further revised with a reduced entry threshold.') }} <br><br>
                    {{ __('If the clustering level is "pre-hard", then the entry threshold for phrases will be reduced to "soft",') }}
                    {{ __('if the phrase still doesnt get anywhere, then the threshold will be reduced to "light".') }}
                </span>
            </span>
        </span>
                                <div class="brut-force" style="display: none">
                                    <div class="form-group required">
                                        <label for="gainFactor">{{ __('Gain factor(%)') }}</label>
                                        <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle" style="color: grey"></i>
                    <span class="ui_tooltip __right">
                        <span class="ui_tooltip_content" style="width: 300px">
                            {{ __('In order for the clusters to merge, you need N number of matches between phrases that are in different clusters.') }}
                            {{ __('If you use the gain factor(X):') }} <br>
                            <b>Х = 0.15</b>
                            <br>
                            <b>N = N - ((N / 100) * Х)</b>
                        </span>
                    </span>
                </span>
                                        <input class="form form-control" type="number" id="gainFactor" name="gainFactor"
                                               value="{{ $config->gain_factor }}">
                                    </div>

                                    <div class="form-group required">
                                        <label
                                            for="brutForceCount">{{ __('Minimum cluster size for re-bulkhead') }}</label>
                                        <input type="number" name="brutForceCount" id="brutForceCount"
                                               class="form form-control"
                                               value="{{ $config->brut_force_count }}">
                                    </div>

                                    <div class="form-group required">
                                        <label for="reductionRatio">{{ __('Minimum multiplier') }}</label>
                                        <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle" style="color: grey"></i>
                    <span class="ui_tooltip __right">
                        <span class="ui_tooltip_content" style="width: 300px">
                           {{ __("'Clustering level' and 'Minimum multiplier' are both from X to Y,") }} <br>
                            <b>{{ __('where') }} X = 100, {{ __('and') }} Y = 80</b><br>
                            {{ __('These values determine the minimum threshold for merging clusters, at certain points of verification') }}
                        </span>
                    </span>
                </span>
                                        {!! Form::select('reductionRatio', [
                                            $config->reduction_ratio => $config->reduction_ratio,
                                            'pre-hard' => 'pre-hard',
                                            'soft' => 'soft',
                                        ], null, ['class' => 'custom-select rounded-0', 'id' => 'reductionRatio']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group required" id="extra-block">
                                <div class="row">
                                    <div class="col-6 d-flex flex-column">
                                        <label for="domain-textarea">{{ __('Domain') }} <b>http/https</b></label>
                                        <textarea name="domain-textarea" id="domain-textarea" rows="5"
                                                  class="form-control w-100"
                                                  placeholder="https://site.ru"></textarea>
                                    </div>

                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <label for="comment-textarea">{{ __('Comment') }}</label>
                                            <textarea name="comment-textarea" id="comment-textarea" rows="5"
                                                      class="form-control w-100"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label
                                        for="searchRelevance">{{ __('Select a relevant page for the domain') }}</label>
                                    <input type="checkbox" name="searchRelevance" id="searchRelevance"
                                           @if($config->search_relevance) checked @endif>
                                    <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle" style="color: grey"></i>
            <span class="ui_tooltip __right">
                <span class="ui_tooltip_content" style="width: 300px">
                    {{ __('Relevant pages will be searched for each phrase') }}
                    <br>
                    {{ __('You need to specify the domain name in the format') }} <b>http(s)://site.ru/</b>
                </span>
            </span>
        </span>
                                </div>

                                <div id="searchEngineBlock">
                                    <label for="domain-textarea">{{ __('Search Engine') }}</label>
                                    {!! Form::select('searchEngine', [
                                        $config->search_engine => $config->search_engine,
                                        'yandex' => 'Yandex',
                                        'google' => 'Google',
                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'searchEngine']) !!}
                                </div>

                                @if(!Auth::user()->telegram_bot_active)
                                    <div class="mt-2">
                                        {{ __('Want to') }}
                                        <a href="{{ route('profile.index') }}" target="_blank">
                                            {{ __('receive notifications from our telegram bot') }}
                                        </a> ?
                                    </div>
                                @else
                                    <div id="sendTelegramMessage">
                                        <label for="sendMessage"
                                               class="pt-1">{{ __('Notify in a telegram upon completion') }}</label>
                                        {!! Form::select('sendMessage', [
                                            $config->send_message => $config->send_message,
                                            true => __('Yes'),
                                            false => __('No'),
                                        ], null, ['class' => 'custom-select rounded-0', 'id' => 'sendMessage']) !!}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group required mt-2">
                                <div>
                                    <label for="searchBase">{{ __('Base frequency analysis') }}</label>
                                    <input type="checkbox" name="searchBase" id="searchBase"
                                           @if($config->search_base) checked @endif>
                                </div>
                                <div>
                                    <label for="searchPhrases">{{ __('Phrase frequency analysis') }}</label>
                                    <input type="checkbox" name="searchPhrases" id="searchPhrases"
                                           @if($config->search_phrased) checked @endif>
                                </div>
                                <div>
                                    <label for="searchTarget">{{ __('Accurate frequency analysis') }}</label>
                                    <input type="checkbox" name="searchTarget" id="searchTarget"
                                           @if($config->search_target) checked @endif>
                                </div>
                            </div>

                            <div class="form-group required" id="saveResultBlock">
                                <label>{{ __('Save results') }}</label>
                                <span class="__helper-link ui_tooltip_w">
        <i class="fa fa-question-circle" style="color: grey"></i>
        <span class="ui_tooltip __right">
            <span class="ui_tooltip_content" style="width: 300px">
            {{ __("If you save the results then you can view the results in the 'my projects' tab") }} <br><br>
            {{ __('If you do not save the results, then you can view the result only after the analysis is completed,') }}
                {{ __('data will be lost when starting the next analysis or when reloading the page') }}
            </span>
        </span>
    </span>
                                {!! Form::select('save', [
                                    $config->save_results => $config->save_results,
                                    '1' => __('Save'),
                                    '0' => __('Do not save'),
                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'save']) !!}
                            </div>
                        </div>

                        <div id="classic" style="display: block">
                            <div class="form-group required">
                                <label>{{ __('Region') }}</label>
                                {!! Form::select('region_classic', array_unique([
                                    $config_classic->region => $config->region,
                                  '213' => __('Moscow'),
                                   '1' => __('Moscow and the area'),
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
                                   '10649' => __('Stary Oskol'),
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
                               ]), null, ['class' => 'custom-select rounded-0', 'id' => 'region_classic']) !!}
                            </div>

                            <div class="form-group required" id="phrases-form-block">
                                <div class="d-flex justify-content-between">
                                    <label>{{ __('Key phrases') }}</label>
                                    <span class="text-muted">{{ __('Count phrases') }}:
                <span id="list-phrases-counter-classic">0</span>
            </span>
                                </div>
                                {!! Form::textarea('phrases_classic', null, ['class' => 'form-control', 'id' => 'phrases_classic'] ) !!}
                            </div>

                            <div class="form-group required" style="display: none">
                                <label for="ignoredDomains">{{ __('Ignored domains') }}</label>
                                <textarea class="form form-control" name="ignoredDomains" id="ignoredDomains_classic"
                                          cols="8"
                                          rows="8">{{ $config_classic->ignored_domains }}</textarea>
                            </div>

                            <div style="display: none">
                                <div class="form-group required">
                                    <label for="ignoredWords">{{ __('Ignored words') }}</label>
                                    <textarea class="form form-control" name="ignoredWords" id="ignoredWords_classic"
                                              cols="8"
                                              rows="8">{{ $config_classic->ignored_words }}</textarea>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label>{{ __('clustering level') }}</label>
                                <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle" style="color: grey"></i>
            <span class="ui_tooltip __right">
                <span class="ui_tooltip_content" style="width: 300px">
                    {{ __('the higher the clustering level, the more groups you will get') }}
                </span>
            </span>
        </span>
                                {!! Form::select('clustering_level_classic', [
                                    $config_classic->clustering_level => $config_classic->clustering_level,
                                    'light' => 'light',
                                    'soft' => 'soft',
                                    'pre-hard' => 'pre-hard',
                                    'hard' => 'hard',
                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel_classic']) !!}
                            </div>

                            <div class="form-group required" id="extra-block">
                                <div class="row">
                                    <div class="col-6 d-flex flex-column">
                                        <label for="domain-textarea">{{ __('Domain') }} <b>http/https</b></label>
                                        <textarea name="domain-textarea" id="domain-textarea_classic" rows="5"
                                                  class="form-control w-100"
                                                  placeholder="https://site.ru"></textarea>
                                    </div>

                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <label for="comment-textarea">{{ __('Comment') }}</label>
                                            <textarea name="comment-textarea" id="comment-textarea_classic" rows="5"
                                                      class="form-control w-100"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label
                                        for="searchRelevance">{{ __('Select a relevant page for the domain') }}</label>
                                    <input type="checkbox" name="searchRelevance" id="searchRelevance_classic"
                                           @if($config_classic->search_relevance) checked @endif>
                                    <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle" style="color: grey"></i>
            <span class="ui_tooltip __right">
                <span class="ui_tooltip_content" style="width: 300px">
                    {{ __('Relevant pages will be searched for each phrase') }}
                    <br>
                    {{ __('You need to specify the domain name in the format') }} <b>http(s)://site.ru/</b>
                </span>
            </span>
        </span>
                                </div>

                                <div id="searchEngineBlock_classic">
                                    <label for="domain-textarea">{{ __('Search Engine') }}</label>
                                    {!! Form::select('searchEngine_classic', [
                                        $config_classic->search_engine => $config_classic->search_engine,
                                        'yandex' => 'Yandex',
                                        'google' => 'Google',
                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'searchEngine']) !!}
                                </div>

                                @if(!Auth::user()->telegram_bot_active)
                                    <div class="mt-2">
                                        {{ __('Want to') }}
                                        <a href="{{ route('profile.index') }}" target="_blank">
                                            {{ __('receive notifications from our telegram bot') }}
                                        </a> ?
                                    </div>
                                @else
                                    <div id="sendTelegramMessage">
                                        <label for="sendMessage"
                                               class="pt-1">{{ __('Notify in a telegram upon completion') }}</label>
                                        {!! Form::select('sendMessage', [
                                            $config_classic->send_message => $config_classic->send_message,
                                            true => __('Yes'),
                                            false => __('No'),
                                        ], null, ['class' => 'custom-select rounded-0', 'id' => 'sendMessage_classic']) !!}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group required mt-2">
                                <div>
                                    <label for="searchBase">{{ __('Base frequency analysis') }}</label>
                                    <input type="checkbox" name="searchBase" id="searchBase_classic"
                                           @if($config_classic->search_base) checked @endif>
                                </div>
                                <div>
                                    <label for="searchPhrases">{{ __('Phrase frequency analysis') }}</label>
                                    <input type="checkbox" name="searchPhrases" id="searchPhrases_classic"
                                           @if($config_classic->search_phrased) checked @endif>
                                </div>
                                <div>
                                    <label for="searchTarget">{{ __('Accurate frequency analysis') }}</label>
                                    <input type="checkbox" name="searchTarget" id="searchTarget_classic"
                                           @if($config_classic->search_target) checked @endif>
                                </div>
                            </div>

                            <div class="form-group required" id="saveResultBlock">
                                <label>{{ __('Save results') }}</label>
                                <span class="__helper-link ui_tooltip_w">
        <i class="fa fa-question-circle" style="color: grey"></i>
        <span class="ui_tooltip __right">
            <span class="ui_tooltip_content" style="width: 300px">
            {{ __("If you save the results then you can view the results in the 'my projects' tab") }} <br><br>
            {{ __('If you do not save the results, then you can view the result only after the analysis is completed,') }}
                {{ __('data will be lost when starting the next analysis or when reloading the page') }}
            </span>
        </span>
    </span>
                                {!! Form::select('save_classic', [
                                    $config_classic->save_results => $config_classic->save_results,
                                    '1' => __('Save'),
                                    '0' => __('Do not save'),
                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'save_classic']) !!}
                            </div>
                        </div>

                        <input type="button" data-dismiss="modal"
                               class="btn btn-secondary" id="start-analyse"
                               data-target="classic" value="{{ __('Analyse') }}">

                        <span class="ml-2">
    {{ __('It will be written off') }} <span id="loss-limits">0</span> {{ __('limits') }}
</span>
                    </div>

                    <div id="progress-bar" class="w-25 pt-3 pb-3" style="display: none">
                        <span id="progress-bar-state"></span>
                        <span id="total-phrases"></span>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
                    </div>

                    <div id="block-for-downloads-files" class="mt-5" style="display: none">
                        <h3>{{ __('Cluster table') }}</h3>
                        <div id="files-downloads"></div>
                    </div>
                    <div id="result-table" style='width: 100%; overflow-x: scroll; display: none'>
                        <table id="clusters-table" class="table table-bordered dtr-inline">
                            <thead>
                            <tr>
                                <th>{{ __('Clusters') }}</th>
                                <th style="min-width: 400px;">{{ __('Competitors') }}</th>
                            </tr>
                            </thead>
                            <tbody id="clusters-table-tbody">
                            </tbody>
                        </table>
                    </div>

                    <textarea name="hiddenForCopy" id="hiddenForCopy" style="display: none"></textarea>

                    <input type="hidden" id="progressId">
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('/plugins/cluster/js/common_v2.min.js') }}"></script>
        <script src="{{ asset('/plugins/common/js/common.js') }}"></script>
        <script>
            try {
                let clusterLocationPage = new URL(window.location.href)
                if (clusterLocationPage['pathname'] === '/cluster-configuration') {
                    $('#form').remove()
                    $('#sendTelegramMessageConfig').show()
                } else {
                    $('#sendTelegramMessageConfig').remove()
                }
            } catch (e) {

            }

            function calculateClassicLimits() {
                let count = 1;

                if ($('#searchRelevance_classic').is(':checked')) {
                    count += 1
                }

                if ($('#searchBase_classic').is(':checked')) {
                    count += 1
                }

                if ($('#searchPhrases_classic').is(':checked')) {
                    count += 1
                }

                if ($('#searchTarget_classic').is(':checked')) {
                    count += 1
                }

                return count
            }

            function calculateLimits() {
                let count = 1;

                if ($('#searchRelevance').is(':checked')) {
                    count += 1
                }

                if ($('#searchBase').is(':checked')) {
                    count += 1
                }

                if ($('#searchPhrases').is(':checked')) {
                    count += 1
                }

                if ($('#searchTarget').is(':checked')) {
                    count += 1
                }

                return count
            }

            function changeCheckBoxState() {
                $('#searchRelevance_classic,#searchBase_classic,#searchPhrases_classic,#searchTarget_classic').change(function () {
                    let count = calculateClassicLimits();
                    let newCount = Number($('#list-phrases-counter-classic').html())
                    $('#loss-limits').html(newCount * count)
                })

                $('#searchRelevance,#searchBase,#searchPhrases,#searchTarget').change(function () {
                    let count = calculateLimits();
                    let newCount = Number($('#list-phrases-counter').html())
                    $('#loss-limits').html(newCount * count)
                })
            }

            $(document).ready(function () {
                $('#brutForce').on('click', function () {
                    if ($(this).is(':checked')) {
                        $('.brut-force').show(300)
                    } else {
                        $('.brut-force').hide(300)
                    }
                })

                $('#brutForce_classic').on('click', function () {
                    if ($('#brutForce_classic').is(':checked')) {
                        $('.brut-force_classic').show(300)
                    } else {
                        $('.brut-force_classic').hide(300)
                    }
                })

                eventChangeList($('#phrases_classic'), $('#loss-limits'), $('#list-phrases-counter-classic'), calculateClassicLimits)
                eventChangeList($('#phrases'), $('#loss-limits'), $('#list-phrases-counter'), calculateLimits)

                changeCheckBoxState()
            })
        </script>

        <script>
            $('#tab_1 > div.w-50.pb-3 > div:nth-child(4)').hide()
            $('#tab_1 > div.w-50.pb-3 > div:nth-child(6)').hide()

            function successCopiedMessage() {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html("{{ __('Successfully copied') }}")
                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, 3000)
            }
        </script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-table_v2.min.js') }}"></script>
        <script src="{{ asset('plugins/common/js/common.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/jszip.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/vfs_fonts.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/html5.min.js') }}"></script>
        <script>
            let progressId
            let interval

            $(document).ready(function () {
                $('#pro').hide()
                $('#classic').show()

                isSearchRelevance();
            })

            $('#start-analyse').click(function () {
                if ($(this).attr('data-target') === 'classic' && $('#phrases_classic').val() === '') {
                    alert('Добавьте ключевые фразы')
                    return;
                }
                if ($(this).attr('data-target') !== 'classic' && $('#phrases').val() === '') {
                    alert('Добавьте ключевые фразы')
                    return;
                }
                $(this).attr('disabled', true)

                $.ajax({
                    type: "GET",
                    url: "{{ route('start.cluster.progress') }}",
                    success: function (response) {
                        progressId = response.id
                        $('#progress-bar').show()
                        $('#progressId').val(progressId)
                        setProgressBarStyles(0)
                        interval = setInterval(() => {
                            getProgressPercent(response.id, interval)
                        }, 5000)

                        startClusterAnalyse(interval)
                    }
                })
            });

            function refreshAll() {
                $('#block-for-downloads-files').hide()
                $('#result-table').hide()

                $.each($('.render-table'), function (key, value) {
                    $('#' + $(this).attr('id')).dataTable().fnDestroy()
                })
                $('#hidden-result-table').dataTable().fnDestroy()

                $('.render-table').remove()
                $('.render').remove()

                $('#start-analyse').attr('disabled', false)
            }

            function getProgressPercent(id, interval) {
                $.ajax({
                    type: "GET",
                    url: `/get-cluster-progress/${id}`,
                    success: function (response) {
                        setProgressBarStyles(response.count)
                        if ('result' in response) {
                            $('#progress-bar-state').html("{{ __('Parse xml') }}")

                            refreshAll()
                            renderResultTable_v2(response['result'], response['objectId'])
                            destroyProgress(interval)

                            $('#files-downloads').html(
                                '<a class="btn btn-secondary mb-2" href="/download-cluster-result/' + response['objectId'] + '/csv" target="_blank">{{ __('Download csv') }}</a>' +
                                ' <a class="btn btn-secondary mb-2" href="/download-cluster-result/' + response['objectId'] + '/xls" target="_blank">{{ __('Download xls') }}</a>'
                            );

                            $('.save-relevance-url').unbind().on('click', function () {
                                let phrase = $(this).attr('data-order')
                                let select = $('#' + phrase.replaceAll(' ', '-'))

                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('set.cluster.relevance.url') }}",
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        phrase: $(this).attr('data-order'),
                                        url: select.val(),
                                        projectId: response['objectId'],
                                    },
                                    success: function () {
                                        select.parent().parent().html('<a href="' + select.val() + '" target="_blank">' + select.val() + '</a>')
                                    },
                                    error: function (response) {
                                    }
                                });
                            })

                            saveAllUrls(response['objectId'])

                            setTimeout(() => {
                                $('#result-table').show()
                                $('#block-for-downloads-files').show()
                            }, 1000)
                        }
                    },
                    error: function () {
                        clearInterval(interval)
                        $('#start-analyse').attr('disabled', false)
                    }
                })
            }

            function startClusterAnalyse(interval) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('analysis.cluster') }}",
                    data: getData(),
                    success: function (response) {
                        if ($('#start-analyse').attr('data-target') === 'classic' && $('#save_classic').val() === '1') {
                            $('.history-notification').show(300)
                            setTimeout(() => {
                                $('.history-notification').hide(300)
                            }, 15000)
                        } else if ($('#start-analyse').attr('data-target') === 'professional' && $('#save').val() === '1') {
                            if ($('#save').val() === '1') {
                                $('.history-notification').show(300)
                                setTimeout(() => {
                                    $('.history-notification').hide(300)
                                }, 15000)
                            }
                        }

                        $('#total-phrases').html(response.totalPhrases)
                    },
                    error: function (response) {
                        destroyProgress(interval)
                        let values = [];

                        $('#start-analyse').attr('disabled', false)
                        $('.toast.toast-error').show(300)
                        $.each(response.responseJSON.errors, function (key, value) {
                            values.push(value)
                        })

                        $('.error-msg').html(values + "")

                        setTimeout(() => {
                            $('.toast.toast-error').hide(300)
                        }, 5000)
                    },
                });
            }

            function destroyProgress(interval) {
                clearInterval(interval)
                $('#progress-bar').hide(300)
                setProgressBarStyles(0)
            }

            $('#classicMode').on('click', function () {
                $('#start-analyse').attr('data-target', 'classic')
                $('#pro').hide()
                $('#classic').show(300)

                $('#classicMode').attr('class', 'btn btn-secondary')
                $('#ProfessionalMode').attr('class', 'btn btn-outline-secondary')

                let count = calculateClassicLimits();
                let newCount = Number($('#list-phrases-counter-classic').html())
                $('#loss-limits').html(newCount * count)
            })

            $('#ProfessionalMode').on('click', function () {
                $('#start-analyse').attr('data-target', 'professional')
                $('#classic').hide()
                $('#pro').show(300)

                $('#classicMode').attr('class', 'btn btn-outline-secondary')
                $('#ProfessionalMode').attr('class', 'btn btn-secondary')

                let count = calculateLimits();
                let newCount = Number($('#list-phrases-counter').html())
                $('#loss-limits').html(newCount * count)
            })

        </script>
    @endslot
@endcomponent
