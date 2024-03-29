@component('component.card', ['title' =>  __('Cluster configuration') ])
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

            .nav-link.text-primary.active {
                color: white !important;
            }

            .Clusters {
                background: oldlace;
            }
        </style>
    @endslot
    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link"
                       href="{{ route('cluster.projects') }}">{{ __('My projects') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link text-primary active" href="{{ route('cluster.configuration') }}">
                            {{ __('Module administration') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="row">
                        <div class="col-4">
                            <div class="card card-primary">
                                <div class="card-header">
                                    Настройки профессионального режима
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('change.cluster.configuration') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="type" value="pro">
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

                                        <div class="form-group required">
                                            <label>{{ __('clustering level') }}</label>
                                            {!! Form::select('clustering_level', [
                                                $config->clustering_level => $config->clustering_level,
                                                'light' => 'light - 40%',
                                                'soft' => 'soft - 50%',
                                                'pre-hard' => 'pre-hard - 60%',
                                                'hard' => 'hard - 70%',
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('Merging Clusters') }}</label>
                                            {!! Form::select('engine_version', [
                                                    $config->engine_version => $config->engine_version,
                                                    'max_phrases' => 'Фразовый перебор и поиск максимального (13.01)',
                                                    '1501' => 'Фразовый перебор и поиск максимального (15.01)',
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'engineVersion']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="ignored_domains">Игнорируемые домены</label>
                                            <textarea class="form form-control" name="ignored_domains"
                                                      id="ignored_domains" cols="8" rows="8"
                                            >{{ $config->ignored_domains }}</textarea>
                                        </div>

                                        <div class="form-group required">
                                            <label for="ignored_words">Игнорируемые слова</label>
                                            <textarea class="form form-control" name="ignored_words" id="ignored_words"
                                                      cols="8" rows="8"
                                            >{{ $config->ignored_words }}</textarea>
                                        </div>

                                        <div class="form-group required">
                                            <label for="brutForce">{{ __('Additional bulkhead') }}</label>
                                            {!! Form::select('brut_force', [
                                                    $config->brut_force => $config->brut_force,
                                                    '1' => __('Yes'),
                                                    '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'brut_force']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="gain_factor">Коэффицент усиления</label>
                                            <input type="number" class="form form-control"
                                                   value="{{ $config->gain_factor }}">
                                        </div>

                                        <div class="form-group required">
                                            <label for="brut_force_count">Минимальный размер кластера для повторной
                                                переборки</label>
                                            <input type="number" class="form form-control"
                                                   value="{{ $config->brut_force_count }}">
                                        </div>

                                        <div class="form-group required">
                                            <label for="reduction_ratio">Минимальный множитель</label>
                                            {!! Form::select('reduction_ratio', array_unique([
                                                $config->reduction_ratio => $config->reduction_ratio,
                                                '0.6' => 'pre-hard',
                                                '0.5' => 'soft',
                                            ]), null, ['class' => 'custom-select rounded-0']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="sendMessage"
                                                   class="pt-1">{{ __('Notify in a telegram upon completion') }}</label>
                                            {!! Form::select('send_message', [
                                                $config->send_message => $config->send_message,
                                                true => __('Yes'),
                                                false => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'send_message']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('Save results') }}</label>
                                            {!! Form::select('save_results', [
                                                $config->save_results => $config->save_results,
                                                '1' => __('Save'),
                                                '0' => __('Do not save'),
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'save_results']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label
                                                for="searchRelevance">{{ __('Select a relevant page for the domain') }}</label>
                                            {!! Form::select('search_relevance', [
                                                $config->search_relevance => $config->search_relevance,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_relevance']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search Engine') }}</label>
                                            {!! Form::select('search_engine', [
                                                $config->search_engine => $config->search_engine,
                                                'yandex' => 'Yandex',
                                                'google' => 'Google',
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_engine']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search base form') }}</label>
                                            {!! Form::select('search_base', [
                                                $config->search_base => $config->search_base,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_base']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search phrase form') }}</label>
                                            {!! Form::select('search_phrased', [
                                                $config->search_phrased => $config->search_phrased,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_phrased']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search target form') }}</label>
                                            {!! Form::select('search_target', [
                                                $config->search_target => $config->search_target,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_target']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="warning_limit">При каком количестве фраз выводить предупреждение
                                                о весе страницы?</label>
                                            <input type="number" name="warning_limit" class="form form-control"
                                                   value="{{ $config->warning_limit }}">
                                        </div>

                                        <input type="submit" class="btn btn-secondary" value="{{ __('Save changes') }}">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="card card-primary">
                                <div class="card-header">
                                    Настройки класического режима
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('change.cluster.configuration') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="type" value="classic">

                                        <div class="form-group required">
                                            <label>{{ __('Region') }}</label>
                                            {!! Form::select('region', array_unique([
                                                $config_classic->region => $config_classic->region,
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
                                               $config_classic->count => $config_classic->count,
                                                '10' => 10,
                                                '20' => 20,
                                                '30' => 30,
                                                '40' => 40,
                                                '50' => 50,
                                            ]), null, ['class' => 'custom-select rounded-0', 'id' => 'count']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('clustering level') }}</label>
                                            {!! Form::select('clustering_level', [
                                                $config_classic->clustering_level => $config_classic->clustering_level,
                                                'light' => 'light - 40%',
                                                'soft' => 'soft - 50%',
                                                'pre-hard' => 'pre-hard - 60%',
                                                'hard' => 'hard - 70%',
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('Merging Clusters') }}</label>
                                            {!! Form::select('engine_version', [
                                                    $config_classic->engine_version => $config_classic->engine_version,
                                                    'max_phrases' => 'Фразовый перебор и поиск максимального (13.01)',
                                                    '1501' => 'Фразовый перебор и поиск максимального (15.01)',
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'engineVersion']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="ignored_domains_classic">Игнорируемые домены</label>
                                            <textarea class="form form-control" name="ignored_domains"
                                                      id="ignored_domains_classic" cols="8" rows="8"
                                            >{{ $config_classic->ignored_domains }}</textarea>
                                        </div>

                                        <div class="form-group required">
                                            <label for="ignored_words_classic">Игнорируемые слова</label>
                                            <textarea class="form form-control" name="ignored_words"
                                                      id="ignored_words_classic" cols="8" rows="8"
                                            >{{ $config_classic->ignored_words }}</textarea>
                                        </div>

                                        <div class="form-group required">
                                            <label for="brutForce">{{ __('Additional bulkhead') }}</label>
                                            {!! Form::select('brut_force', [
                                                    $config_classic->brut_force => $config_classic->brut_force,
                                                    '1' => __('Yes'),
                                                    '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'brut_force']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="gain_factor_classic">Коэффицент усиления</label>
                                            <input type="number" class="form form-control"
                                                   name="gain_factor" id="gain_factor_classic"
                                                   value="{{ $config_classic->gain_factor }}">
                                        </div>

                                        <div class="form-group required">
                                            <label for="brut_force_count_classic">Минимальный размер кластера для
                                                повторной переборки</label>
                                            <input type="number" class="form form-control" name="brut_force_count"
                                                   id="brut_force_count_classic"
                                                   value="{{ $config_classic->brut_force_count }}">
                                        </div>

                                        <div class="form-group required">
                                            <label for="reduction_ratio_classic">Минимальный множитель</label>
                                            {!! Form::select('reduction_ratio', array_unique([
                                                $config_classic->reduction_ratio => $config_classic->reduction_ratio,
                                                '0.6' => 'pre-hard',
                                                '0.5' => 'soft',
                                            ]), null, ['class' => 'custom-select rounded-0']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="sendMessage"
                                                   class="pt-1">{{ __('Notify in a telegram upon completion') }}</label>
                                            {!! Form::select('send_message', [
                                                $config_classic->send_message => $config_classic->send_message,
                                                true => __('Yes'),
                                                false => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'send_message']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('Save results') }}</label>
                                            {!! Form::select('save_results', [
                                                $config_classic->save_results => $config_classic->save_results,
                                                '1' => __('Save'),
                                                '0' => __('Do not save'),
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'save_results']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label
                                                for="searchRelevance">{{ __('Select a relevant page for the domain') }}</label>
                                            {!! Form::select('search_relevance', [
                                                $config_classic->search_relevance => $config_classic->search_relevance,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_relevance']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search Engine') }}</label>
                                            {!! Form::select('search_engine', [
                                                $config_classic->search_engine => $config_classic->search_engine,
                                                'yandex' => 'Yandex',
                                                'google' => 'Google',
                                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_engine']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search base form') }}</label>
                                            {!! Form::select('search_base', [
                                                $config_classic->search_base => $config_classic->search_base,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_base']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search phrase form') }}</label>
                                            {!! Form::select('search_phrased', [
                                                $config_classic->search_phrased => $config_classic->search_phrased,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_phrased']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="domain-textarea">{{ __('Search target form') }}</label>
                                            {!! Form::select('search_target', [
                                                $config_classic->search_target => $config_classic->search_target,
                                                '1' => __('Yes'),
                                                '0' => __('No'),
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'search_target']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="warning_limit">
                                                При каком количестве фраз выводить предупреждение о весе страницы?
                                            </label>
                                            <input type="number"
                                                   name="warning_limit"
                                                   class="form form-control"
                                                   value="{{ $config_classic->warning_limit }}">
                                        </div>

                                        <input type="submit" class="btn btn-secondary" value="{{ __('Save changes') }}">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    Настройка автоудаления
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('set.cluster.cleaning.interval') }}" method="POST">
                                        @csrf
                                        <div class="form-group required d-flex align-items-center">
                                            Удалить проекты старше
                                            <input class="ml-1 mr-1 form form-control w-25" name="cleaning_interval"
                                                   id="cleaning_interval" type="number"
                                                   value="{{ $config->cleaning_interval }}">
                                            дней
                                        </div>

                                        <input type="submit" class="btn btn-secondary" value="{{ __('Save changes') }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcomponent
