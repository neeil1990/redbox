@component('component.card', ['title' =>  __('Cluster') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    <div id="toast-container" class="toast-top-right error-message empty" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right success-message lock-word" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="#">{{ __('My projects') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="#">{{ __('Module administration') }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div id="progress-bar" style="display: none">
                        <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
                        <span class="text-muted" id="progress-bar-state">Подготовка данных..</span>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
                    </div>

                    <div class="col-5 pb-3">
                        <form action="{{ route('analysis.cluster') }}" method="POST">
                            @csrf

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
                                       ]), null, ['class' => 'custom-select rounded-0 region']) !!}
                            </div>

                            <div class="form-group required">
                                <label>{{ __('Top 10/20') }}</label>
                                {!! Form::select('count', array_unique([
                                        '10' => 10,
                                        '20' => 20,
                                ]), null, ['class' => 'custom-select rounded-0 count']) !!}
                            </div>

                            <div class="form-group required">
                                <label id="phrases">{{ __('Phrases') }}</label>
                                {!! Form::textarea("phrases", null,["class" => "form-control phrases", 'required'] ) !!}
                            </div>

                            <div class="form-group required">
                                <label>{{ __('clustering level') }}</label>
                                {!! Form::select('clustering_level', [
                                    '5' => 'soft - 50%',
                                    '7' => 'hard - 70%',
                                    ], null, ['class' => 'custom-select rounded-0', 'id' => 'check-type']) !!}
                            </div>

                            <input type="submit" class="btn btn-secondary" value="{{ __('Analysis') }}">

                        </form>
                    </div>
                    @isset($results)
                        <div class="mt-3">
                            <h3>Таблица кластеров</h3>
                            <table id="clusters-table" class="table table-bordered table-hover dtr-inline">
                                <thead>
                                <tr>
                                    <th>Кластер</th>
                                    <th>ссылка / количество повторений в кластере</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($results['result'] as $result)
                                    <tr class="odd">
                                        <td>
                                            @foreach($result as $phrase => $sites)
                                                @if($phrase !== 'finallyResult')
                                                    <p>{{ $phrase }}</p>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($result['finallyResult'] as $site => $count)
                                                <div>
                                                    <b>{{ $site }}</b>: {{ $count }}
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>
@endcomponent
