@component('component.card', ['title' =>  __('My projects') ])
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

            a.paginate_button.current {
                background: #ebf0f5 !important;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message">
        <div class="toast toast-success" aria-live="polite" style="display:none;">
            <div class="toast-message success-msg"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message">
        <div class="toast toast-error" aria-live="assertive" style="display:none;">
            <div class="toast-message error-msg"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link active"
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
                    <table id="my-cluster-projects" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>{{ __('Analysis date') }}</th>
                            <th>{{ __('Domain') }}</th>
                            <th>{{ __('Comment') }}</th>
                            <th>{{ __('Number of phrases') }}</th>
                            <th>{{ __('Number of groups') }}</th>
                            <th>{{ __('TOP') }}</th>
                            <th>{{ __('Mode') }}</th>
                            <th>{{ __('Region') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>{{ $project->created_at }}</td>
                                <td>
                                    <textarea data-target="{{ $project->id }}" name="domain"
                                              class="action-edit project-domain form-control"
                                              rows="7">{{ $project->domain }}</textarea>
                                </td>
                                <td>
                                    <textarea data-target="{{ $project->id }}" name="comment"
                                              class="action-edit project-comment form-control"
                                              rows="7">{{ $project->comment }}</textarea>
                                </td>
                                <td>{{ $project->count_phrases }}</td>
                                <td>{{ $project->count_clusters }}</td>
                                <td>{{ $project->top }}</td>
                                <td>{{ $project->clustering_level }} / {{ $project->request['engineVersion'] }}</td>
                                <td class="project-region">{{ $project->region }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a class="btn btn-secondary mb-2"
                                           href="{{ route('show.cluster.result', $project->id) }}" target="_blank">
                                            {{ __('View results') }}
                                        </a>
                                        <a href="{{ route('edit.clusters', $project->id) }}" class="btn btn-secondary mb-2">
                                            {{ __('Hands editor') }}
                                        </a>
                                        <button type="button"
                                                data-toggle="modal"
                                                data-target="#repeat-scan"
                                                data-order="{{ $project->id }}"
                                                class="btn btn-secondary mb-2 repeat-scan">
                                            {{ __('Repeat analysis') }}
                                        </button>
                                        <a class="btn btn-secondary mb-2"
                                           href="/download-cluster-result/{{$project->id}}/csv"
                                           target="_blank">{{ __('Download csv') }}</a>
                                        <a class="btn btn-secondary mb-2"
                                           href="/download-cluster-result/{{$project->id}}/xls"
                                           target="_blank">{{ __('Download xls') }}</a>
                                        @if($project->count_phrases >= $config->warning_limit)
                                            <span class="text-info">{{ __('A page can weigh a lot') }}<br> {{ __('and work slowly') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="modal fade" id="repeat-scan" tabindex="-1" aria-labelledby="repeat-scanLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="repeat-scanLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="pro" style="display: none">
                                        <div class="form-group required">
                                            <label>{{ __('Region') }}</label>
                                            {!! Form::select('region', array_unique([
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
                                                '10' => 10,
                                                '20' => 20,
                                                '30' => 30,
                                                '40' => 40,
                                                '50' => 50,
                                            ]), null, ['class' => 'custom-select rounded-0', 'id' => 'count']) !!}
                                        </div>

                                        <div class="form-group required" id="phrases-form-block">
                                            <label>{{ __('Key phrases') }}</label>
                                            {!! Form::textarea('phrases', null, ['class' => 'form-control', 'id'=>'phrases'] ) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="ignoredDomains">Игнорируемые домены</label>
                                            <textarea class="form form-control" name="ignoredDomains"
                                                      id="ignoredDomains" cols="8"
                                                      rows="8"></textarea>
                                        </div>

                                        <div>
                                            <div class="form-group required">
                                                <label for="ignoredWords">Игнорируемые слова</label>
                                                <textarea class="form form-control" name="ignoredWords"
                                                          id="ignoredWords" cols="8"
                                                          rows="8"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('clustering level') }}</label>
                                            {!! Form::select('clustering_level', [
                                                'light' => 'light',
                                                'soft' => 'soft',
                                                'pre-hard' => 'pre-hard',
                                                'hard' => 'hard',
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="brutForce">{{ __('Additional bulkhead') }}</label>
                                            <input type="checkbox" name="brutForce" id="brutForce">
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
                                                    <label for="gainFactor">коэффициент усиления(%)</label>
                                                    <input class="form form-control" type="number" id="gainFactor"
                                                           name="gainFactor"
                                                           value="">
                                                </div>

                                                <div class="form-group required">
                                                    <label for="brutForceCount">Минимальный размер кластера для
                                                        повторной переборки</label>
                                                    <input type="number" name="brutForceCount" id="brutForceCount"
                                                           class="form form-control"
                                                           value="">
                                                </div>

                                                <div class="form-group required">
                                                    <label for="reductionRatio">Минимальный множитель</label>
                                                    {!! Form::select('reductionRatio', [
                                                        'pre-hard' => 'pre-hard',
                                                        'soft' => 'soft',
                                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'reductionRatio']) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group required" id="extra-block">
                                            <div class="row">
                                                <div class="col-6 d-flex flex-column">
                                                    <label for="domain-textarea">{{ __('Domain') }}</label>
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
                                                <input type="checkbox" name="searchRelevance" id="searchRelevance">
                                                <span class="__helper-link ui_tooltip_w">
                                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                                    <span class="ui_tooltip __right">
                                                        <span class="ui_tooltip_content" style="width: 300px">
                                                            Для каждой фразы будет произведён поиск релевантных страниц
                                                            <br>
                                                            Вам необходимо указать доменное имя в формате <b>http(s)://site.ru/</b>
                                                        </span>
                                                    </span>
                                                </span>
                                            </div>

                                            <div id="searchEngineBlock">
                                                <label for="domain-textarea">{{ __('Search Engine') }}</label>
                                                {!! Form::select('searchEngine', [
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
                                                        true => __('Yes'),
                                                        false => __('No'),
                                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'sendMessage']) !!}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-group required mt-2">
                                            <div>
                                                <label for="searchBase">{{ __('Base frequency analysis') }}</label>
                                                <input type="checkbox" name="searchBase" id="searchBase">
                                            </div>
                                            <div>
                                                <label for="searchPhrases">{{ __('Phrase frequency analysis') }}</label>
                                                <input type="checkbox" name="searchPhrases" id="searchPhrases">
                                            </div>
                                            <div>
                                                <label
                                                    for="searchTarget">{{ __('Accurate frequency analysis') }}</label>
                                                <input type="checkbox" name="searchTarget" id="searchTarget">
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
                                                '1' => __('Save'),
                                                '0' => __('Do not save'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'save']) !!}
                                        </div>
                                    </div>

                                    <div id="classic" style="display: block">
                                        <div class="form-group required">
                                            <label>{{ __('Region') }}</label>
                                            {!! Form::select('region_classic', array_unique([
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
                                            <label>{{ __('Key phrases') }}</label>
                                            {!! Form::textarea('phrases_classic', null, ['class' => 'form-control', 'id' => 'phrases_classic'] ) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('clustering level') }}</label>
                                            {!! Form::select('clustering_level_classic', [
                                                'light' => 'light',
                                                'soft' => 'soft',
                                                'pre-hard' => 'pre-hard',
                                                'hard' => 'hard',
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel_classic']) !!}
                                        </div>

                                        <div class="form-group required" id="extra-block">
                                            <div class="row">
                                                <div class="col-6 d-flex flex-column">
                                                    <label for="domain-textarea">{{ __('Domain') }}</label>
                                                    <textarea name="domain-textarea" id="domain-textarea_classic"
                                                              rows="5" class="form-control w-100"
                                                              placeholder="https://site.ru"></textarea>
                                                </div>

                                                <div class="col-6">
                                                    <div class="d-flex flex-column">
                                                        <label for="comment-textarea">{{ __('Comment') }}</label>
                                                        <textarea name="comment-textarea" id="comment-textarea_classic"
                                                                  rows="5"
                                                                  class="form-control w-100"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group required">
                                                <label
                                                    for="searchRelevance">{{ __('Select a relevant page for the domain') }}</label>
                                                <input type="checkbox" name="searchRelevance"
                                                       id="searchRelevance_classic">
                                                <span class="__helper-link ui_tooltip_w">
                                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                                    <span class="ui_tooltip __right">
                                                        <span class="ui_tooltip_content" style="width: 300px">
                                                            Для каждой фразы будет произведён поиск релевантных страниц
                                                            <br>
                                                            Вам необходимо указать доменное имя в формате <b>http(s)://site.ru/</b>
                                                        </span>
                                                    </span>
                                                </span>
                                            </div>

                                            <div id="searchEngineBlock_classic">
                                                <label for="domain-textarea">{{ __('Search Engine') }}</label>
                                                {!! Form::select('searchEngine_classic', [
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
                                                        true => __('Yes'),
                                                        false => __('No'),
                                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'sendMessage_classic']) !!}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-group required mt-2">
                                            <div>
                                                <label for="searchBase">{{ __('Base frequency analysis') }}</label>
                                                <input type="checkbox" name="searchBase" id="searchBase_classic">
                                            </div>
                                            <div>
                                                <label for="searchPhrases">{{ __('Phrase frequency analysis') }}</label>
                                                <input type="checkbox" name="searchPhrases" id="searchPhrases_classic">
                                            </div>
                                            <div>
                                                <label
                                                    for="searchTarget">{{ __('Accurate frequency analysis') }}</label>
                                                <input type="checkbox" name="searchTarget" id="searchTarget_classic">
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
                                                '1' => __('Save'),
                                                '0' => __('Do not save'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'save_classic']) !!}
                                        </div>
                                    </div>

                                    <input type="button" data-dismiss="modal"
                                           class="btn btn-secondary" id="start-analyse" value="{{ __('Analyse') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="progressId">
                </div>
            </div>
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('/plugins/cluster/js/common.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            var progressId
            var interval

            $(document).ready(function () {
                refreshAll()
                $('#saveResultBlock').remove()
                $('#my-cluster-projects').dataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 25,
                    "searching": true,
                })
                $('.dt-button.buttons-copy.buttons-html5').addClass('ml-2')
                $('.dt-button').addClass('btn btn-secondary')

                $('#brutForce').change(function () {
                    if ($(this).is(':checked')) {
                        $('.brut-force').show(300)
                    } else {
                        $('.brut-force').hide(300)
                    }
                });

            })

            function successMessage(message = "{{ __('Text was successfully change') }}") {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html(message)

                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, 6000)
            }

            function errorMessage(message) {
                $('.toast.toast-error').show(300)
                $('.toast-message.error-msg').html(message)

                setTimeout(() => {
                    $('.toast.toast-error').hide(300)
                }, 5000)
            }

            function startClusterAnalyse(progressId) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('analysis.cluster') }}",
                    data: getData(true, progressId),
                    success: function () {
                        successMessage("{{ __('The analysis has been successfully launched, the results will be automatically added to the table') }}")
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.message)
                        clearInterval(interval)
                    }
                });
            }

            function refreshAll() {
                $('.repeat-scan').unbind().on('click', function () {
                    $.ajax({
                        type: "post",
                        url: "{{ route('get.cluster.request') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: $(this).attr('data-order'),
                        },
                        success: function (response) {
                            let request = response.request
                            $('#repeat-scanLabel').html(response.created_at)

                            if (request.mode === 'classic') {
                                $('#pro').hide()
                                $('#classic').show()
                                $('#start-analyse').attr('data-target', 'classic')
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(4)').hide()
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(6)').hide()

                                $('#region_classic').val(request.region)
                                $('#phrases_classic').val(request.phrases)
                                $('#clusteringLevel_classic').val(request.clusteringLevel)
                                $('#engineVersion').val(request.engineVersion) //
                                $('#domain-textarea_classic').html(request.domain)
                                $('#comment-textarea_classic').html(request.comment)
                                $('#save_classic').val(request.save)

                                if (request.searchBase === 'true') {
                                    $('#searchBase_classic').prop('checked', true);
                                } else {
                                    $('#searchBase_classic').prop('checked', false);
                                }

                                if (request.searchPhrases === 'true') {
                                    $('#searchPhrases_classic').prop('checked', true);
                                } else {
                                    $('#searchPhrases_classic').prop('checked', false);
                                }

                                if (request.searchTarget === 'true') {
                                    $('#searchTarget_classic').prop('checked', true);
                                } else {
                                    $('#searchTarget_classic').prop('checked', false);
                                }

                                if (request.searchRelevance === 'true') {
                                    $('#searchRelevance_classic').prop('checked', true);
                                    $('#searchEngineBlock_classic').show()
                                } else {
                                    $('#searchRelevance_classic').prop('checked', false);
                                    $('#searchEngineBlock_classic').hide()
                                }

                            } else {
                                $('#pro').show()
                                $('#classic').hide()
                                if ('searchEngine' in request) {
                                    $('#searchEngine').val(request.searchEngine)
                                } else {
                                    $('#searchEngine').val('yandex')
                                }

                                if ('ignoredWords' in request) {
                                    $('#ignoredWords').val(request.ignoredWords)
                                } else {
                                    $('#ignoredDomains').val('')
                                }

                                if ('ignoredDomains' in request) {
                                    $('#ignoredDomains').val(request.ignoredDomains)
                                } else {
                                    $('#ignoredDomains').val('')
                                }

                                if ('gainFactor' in request) {
                                    $('#gainFactor').val(request.gainFactor)
                                } else {
                                    $('#gainFactor').val(10)
                                }

                                if (request.searchPhrases === 'true') {
                                    $('#searchPhrases').prop('checked', true);
                                } else {
                                    $('#searchPhrases').prop('checked', false);
                                }

                                if (request.brutForce === 'true') {
                                    $('#brutForce').prop('checked', true);
                                    $('.brut-force').show()
                                } else {
                                    $('#brutForce').prop('checked', false);
                                    $('.brut-force').hide()
                                }

                                if (request.searchRelevance === 'true') {
                                    $('#searchRelevance').prop('checked', true);
                                    $('#searchEngineBlock').show()
                                } else {
                                    $('#searchRelevance').prop('checked', false);
                                    $('#searchEngineBlock').hide()
                                }

                                if (request.searchTarget === 'true') {
                                    $('#searchTarget').prop('checked', true);
                                } else {
                                    $('#searchTarget').prop('checked', false);
                                }

                                if (request.searchBase === 'true') {
                                    $('#searchBase').prop('checked', true);
                                } else {
                                    $('#searchBase').prop('checked', false);
                                }

                                $("#brutForceCount").val(request.brutForceCount)

                                $('#start-analyse').attr('data-target', 'professional')
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(4)').show()
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(6)').show()
                            }

                            $('#repeat-scanLabel').html(response.created_at)
                            $('#region').val(request.region)
                            $('#count').val(request.count)
                            $('#phrases').val(request.phrases)
                            $('#clusteringLevel').val(request.clusteringLevel)
                            $('#engineVersion').val(request.engineVersion)
                            $('#domain-textarea').html(request.domain)
                            $('#comment-textarea').html(request.comment)
                            $('#save').val(request.save)
                        },
                        error: function (error) {
                            errorMessage(error.responseJSON.message)
                        }
                    });
                })

                $('#start-analyse').unbind().on('click', function () {
                    if ($('#phrases').val() !== '') {
                        $.ajax({
                            type: "GET",
                            url: "/start-cluster-progress",
                            success: function (response) {
                                $('#progressId').val(response.id)
                                interval = setInterval(() => {
                                    getProgressPercent(response.id, interval)
                                }, 5000)

                                startClusterAnalyse(response.id, interval)
                            }
                        })
                    }
                });

                $('.action-edit').unbind().on('change', function () {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cluster.edit') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: $(this).attr('data-target'),
                            option: $(this).attr('name'),
                            value: $(this).val(),
                        },
                        success: function (response) {
                            successMessage()
                        },
                        error: function (error) {
                        }
                    });
                })

                function getProgressPercent(id, interval) {
                    $.ajax({
                        type: "GET",
                        url: `/get-cluster-progress/${id}/modify`,
                        success: function (response) {
                            if ('cluster' in response) {
                                let cluster = response['cluster']
                                let domain = cluster['domain'] === null ? '' : cluster['domain']
                                let comment = cluster['comment'] === null ? '' : cluster['comment']
                                let table = $('#my-cluster-projects').DataTable();
                                table.row.add({
                                    0: cluster['created_at'],
                                    1: '<textarea data-target="' + cluster['id'] + '" name="domain" rows="7" class="action-edit project-domain form-control">' + domain + '</textarea>',
                                    2: '<textarea data-target="' + cluster['id'] + '" name="comment" rows="7" class="action-edit project-comment form-control">' + comment + '</textarea>',
                                    3: cluster['count_phrases'],
                                    4: cluster['count_clusters'],
                                    5: cluster['top'],
                                    6: cluster['clustering_level'] + ' / ' + cluster['request']['engineVersion'],
                                    7: cluster['region'],
                                    8: '<div class="d-flex flex-column">' +
                                        '<a href="/show-cluster-result/' + cluster['id'] + '" target="_blank" class="btn btn-secondary mb-2">{{ __('View results') }}</a> ' +
                                        '<a href="/edit-clusters/' + cluster['id'] + '" class="btn btn-secondary mb-2">{{ __('Redistribute clusters') }}</a>' +
                                        '<button type="button" data-toggle="modal" data-target="#repeat-scan" data-order="' + cluster['id'] + '" class="btn btn-secondary mb-2 repeat-scan">{{ __('Repeat the analysis') }}</button> ' +
                                        '<button class="btn btn-secondary mb-2">{{ __('Download csv') }}</button>' +
                                        '<button class="btn btn-secondary">{{ __('Download xls') }}</button></div>'
                                });
                                table.draw()
                                refreshAll()
                                clearInterval(interval)
                            }
                        }
                    })
                }
            }
        </script>
    @endslot
@endcomponent
