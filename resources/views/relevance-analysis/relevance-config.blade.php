@component('component.card', ['title' =>  __('Relevance analysis configuration') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
        <style>
            #tab_1 > div.d-flex.flex-column > div:nth-child(3) > button.btn.btn-secondary.col-2 > span > span > span,
            #tab_1 > div.d-flex.flex-column > div:nth-child(2) > button.btn.btn-secondary.col-2 > span > span > span,
            #tab_1 > div.d-flex.flex-column > div:nth-child(1) > button.btn.btn-secondary.col-2 > span > span > span {
                width: 400px;
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
                    <a class="nav-link" href="{{ route('relevance-analysis') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('create.queue.view') }}">
                        {{ __('Create page analysis tasks') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('relevance.history') }}">{{ __('History') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sharing.view') }}" class="nav-link">{{ __('Share your projects') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('access.project') }}" class="nav-link">{{ __('Projects available to you') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('all.relevance.projects') }}">{{ __('Statistics') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('show.config') }}">{{ __('Module administration') }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Analyzer Settings') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                            class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('changeConfig') }}" method="POST" class="col-12 p-0">
                                    @csrf
                                    <div>
                                        <div class="form-group required">
                                            <label>{{ __('Select the default value Top 10/20') }}</label>
                                            {!! Form::select('count', array_unique([
                                                    $config->count_sites => $config->count_sites,
                                                    '10' => 10,
                                                    '20' => 20,
                                                    ]), null, ['class' => 'custom-select rounded-0']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('Select the default region') }}</label>
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
                                                    ]), null, ['class' => 'custom-select rounded-0']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label>{{ __('The list of ignored domains by default') }}</label>
                                            {!! Form::textarea("ignored_domains", $config->ignored_domains ,["class" => "form-control"] ) !!}
                                        </div>

                                        <div class="form-group required d-flex align-items-center">
                                            <span>{{ __('The number of characters to crop by default') }}</span>
                                            <input type="number" class="form form-control col-2 ml-1 mr-1"
                                                   name="separator"
                                                   id="separator" value="{{ $config->separator }}">
                                        </div>

                                        <div class="mt-3 mb-3">
                                            <div class="mt-3 mb-3">
                                                <p>{{ __('hide ignored domains') }}</p>
                                                {!! Form::select('hide_ignored_domains', array_unique([
                                                        $config->hide_ignored_domains => $config->hide_ignored_domains,
                                                        'yes' => __('yes'),
                                                        'no' => __('no'),
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="mt-3 mb-3">
                                                <p>{{ __('Track the text in the noindex tag by default') }}</p>
                                                {!! Form::select('noindex', array_unique([
                                                        $config->noindex => $config->noindex,
                                                        'yes' => __('yes'),
                                                        'no' => __('no'),
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="mt-3 mb-3">
                                                <p>{{ __('Track words in the alt, title, and data-text attributes by default') }}</p>
                                                {!! Form::select('meta_tags', array_unique([
                                                        $config->meta_tags => $config->meta_tags,
                                                        'yes' => __('yes'),
                                                        'no' => __('no'),
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="mt-3 mb-3">
                                                <p>{{ __('Track conjunctions, prepositions, pronouns by default') }}</p>
                                                {!! Form::select('parts_of_speech', array_unique([
                                                        $config->parts_of_speech => $config->parts_of_speech,
                                                        'yes' => __('yes'),
                                                        'no' => __('no'),
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="mt-3 mb-3">
                                                <div>
                                                    {{ __('Exclude default words') }}
                                                </div>

                                                {!! Form::select('remove_my_list_words', array_unique([
                                                        $config->remove_my_list_words => $config->remove_my_list_words,
                                                        'yes' => __('yes'),
                                                        'no' => __('no'),
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="form-group required list-words mt-1">
                                                <label for="my_list_words">{{ __('List of excluded words') }}</label>
                                                {!! Form::textarea('my_list_words', $config->my_list_words ,['class' => 'form-control', 'cols' => 8, 'rows' => 5]) !!}
                                            </div>
                                        </div>

                                        <div class="mt-3 mb-3">
                                            <div class="mt-3 mb-3">
                                                <p>{{ __('The number of entries in the tlp table by default') }}</p>
                                                {!! Form::select('ltp_count', array_unique([
                                                        $config->ltp_count => $config->ltp_count,
                                                        '10' => 10,
                                                        '25' => 25,
                                                        '50' => 50,
                                                        '100' => 100,
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="mt-3 mb-3">
                                                <p>{{ __('The number of entries in the tlps table by default') }}</p>
                                                {!! Form::select('ltps_count', array_unique([
                                                        $config->ltps_count => $config->ltps_count,
                                                        '10' => 10,
                                                        '25' => 25,
                                                        '50' => 50,
                                                        '100' => 100,
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="mt-3 mb-3">
                                                <p>{{ __('The number of entries in the table of analyzed sites by default') }}</p>
                                                {!! Form::select('scanned_sites_count', array_unique([
                                                        $config->scanned_sites_count => $config->scanned_sites_count,
                                                        '10' => 10,
                                                        '25' => 25,
                                                        '50' => 50,
                                                        '100' => 100,
                                                ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                            </div>

                                            <div class="mt-3 mb-3">
                                                <p>{{ __('The number of entries in the default recommendation table') }}</p>
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
                                                <label for="boostPercent">{{ __('add % to coverage') }}</label>
                                                <input name="boostPercent" type="number" class="form form-control"
                                                       value="{{ $config->boostPercent }}">
                                            </div>
                                        </div>
                                        <input type="submit" value="Изменить стартовую конфигурацию"
                                               class="btn btn-secondary">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Settings for auto-cleaning results') }}</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                            class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                {{ __('Auto-cleaning every') }}
                                <input type="number" class="form form-control w-25 d-inline" id="cleaningInterval"
                                       value="{{ $config->cleaning_interval }}">
                                {{ __('days') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/relevance-analysis/history/common.js') }}"></script>
        <script>
            $('#cleaningInterval').on('change', function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('change.cleaning.interval') }}",
                    data: {
                        newInterval: $(this).val()
                    },
                    success: function (response) {
                        getSuccessMessage(response.message)
                    },
                });
            })

            function getSuccessMessage(message) {
                $('.toast-top-right.success-message').show(300)
                $('#message-info').html(message)
                setTimeout(() => {
                    $('.toast-top-right.success-message').hide(300)
                }, 3000)
            }
        </script>
    @endslot
@endcomponent
