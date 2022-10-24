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

            .ui_tooltip_content {
                width: 325px;
            }

            .dataTables_info, .hidden-result-table_filter {
                display: none;
            }

            .bg-cluster-warning {
                background: rgba(245, 226, 170, 0.5);
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
            <div
                class="toast-message error-msg">{{ __('An unexpected error has occurred, please contact the administrator') }}</div>
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
                    <div class="col-5 pb-3">
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
                            <label>{{ __('Top 10/20') }}</label>
                            {!! Form::select('count', array_unique([
                                '10' => 10,
                                '20' => 20,
                                '30' => 30,
                            ]), null, ['class' => 'custom-select rounded-0', 'id' => 'count']) !!}
                        </div>

                        <div class="form-group required">
                            <label>{{ __('Phrases') }}</label>
                            {!! Form::textarea('phrases', null, ['class' => 'form-control', 'required', 'id'=>'phrases'] ) !!}
                        </div>

                        <div class="form-group required">
                            <label>{{ __('clustering level') }}</label>
                            {!! Form::select('clustering_level', [
                                '5' => 'soft - 50%',
                                '7' => 'hard - 70%',
                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel']) !!}
                        </div>

                        <div class="form-group required">
                            <label>Объединение кластеров</label>
                            {!! Form::select('engine_version', [
                                'old' => 'Формирование на основе первой попавшейся фразы (old)',
                                'new' => 'Формирование на основе массива ссылок кластера (new)',
                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'engineVersion']) !!}
                        </div>

                        <div class="form-group required">
                            <div>
                                <label for="searchBased">Анализ базовой частотности</label>
                                <input type="checkbox" name="searchBased" id="searchBased" checked disabled>
                            </div>
                            <div>
                                <label for="searchPhrases">Анализ фразовой частотности</label>
                                <input type="checkbox" name="searchPhrases" id="searchPhrases">
                            </div>
                            <div>
                                <label for="searchTarget">Анализ точной частотности</label>
                                <input type="checkbox" name="searchTarget" id="searchTarget">
                            </div>
                        </div>

                        <input type="button" class="btn btn-secondary" id="start-analysis"
                               value="{{ __('Analysis') }}">
                    </div>

                    <div id="progress-bar" style="display: none">
                        <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
                    </div>

                    <div id="block-for-downloads-files" style="display: none">
                        <h3>Таблица кластеров</h3>
                        <table id="hidden-result-table" style="display: none">
                            <thead>
                            <tr>
                                <th colspan="4"></th>
                                <th class="centered-text" colspan="3">Частотность</th>
                            </tr>
                            <tr>
                                <th>Порядковый номер</th>
                                <th>Порядковый номер в кластере</th>
                                <th>Ключевой запрос</th>
                                <th>Группа</th>
                                <th>Базовая</th>
                                <th>"Фразовая"</th>
                                <th>"!Точная"</th>
                            </tr>
                            </thead>
                            <tbody id="hidden-table-tbody">
                            </tbody>
                        </table>
                        <div style='width: 100%; overflow-x: scroll;'>
                            <table id="clusters-table" class="table table-bordered dtr-inline">
                                <thead>
                                <tr>
                                    <th>Кластеры</th>
                                    <th style="min-width: 250px;">Конкуренты</th>
                                </tr>
                                </thead>
                                <tbody id="clusters-table-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <textarea name="hiddenForCopy" id="hiddenForCopy" style="display: none"></textarea>

                    <input type="hidden" id="progressId">
                </div>
            </div>
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-hidden-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-table.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            let progressId
            let interval

            $('#start-analysis').click(function () {
                if ($('#phrases').val() !== '') {
                    $(this).attr('disabled', true)
                    $.ajax({
                        type: "GET",
                        url: "{{ route('start.cluster.progress') }}",
                        success: function (response) {
                            progressId = response.id
                            $('#progress-bar').show()
                            $('#progressId').val(progressId)
                            refreshAll()
                            startAnalysis()

                            interval = setInterval(() => {
                                getProgressPercent(response.id)
                            }, 1000)
                        }
                    })
                }
            });


            function getData() {
                return {
                    region: $('#region').val(),
                    count: $('#count').val(),
                    phrases: $('#phrases').val(),
                    clusteringLevel: $('#clusteringLevel').val(),
                    engineVersion: $('#engineVersion').val(),
                    searchBased: $('#searchBased').is(':checked'),
                    searchPhrases: $('#searchPhrases').is(':checked'),
                    searchTarget: $('#searchTarget').is(':checked'),
                    progressId: $('#progressId').val()
                };
            }

            function refreshAll() {
                $.each($('.render-table'), function (key, value) {
                    $('#' + $(this).attr('id')).dataTable().fnDestroy()
                })

                $('.render').remove()
                $('#hidden-result-table').dataTable().fnDestroy()
                $('#block-for-downloads-files').hide()
                $('.render-table').remove()
            }

            function getProgressPercent(id) {
                $.ajax({
                    type: "GET",
                    url: `/get-cluster-progress/${id}`,
                    success: function (response) {
                        setProgressBarStyles(response.percent)
                    }
                })
            }

            function setProgressBarStyles(percent) {
                let bar = $('.progress-bar')
                bar.css({
                    width: percent + '%'
                })
                bar.html(percent + '%');
            }

            function destroyProgress(progressId, interval) {
                clearInterval(interval)
                setTimeout(() => {
                    setProgressBarStyles(0)
                    $('#progress-bar').hide(300)
                }, 3000)
            }

            function startAnalysis() {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('analysis.cluster') }}",
                    data: getData(),
                    success: function (response) {
                        destroyProgress(progressId, interval)
                        $('#start-analysis').attr('disabled', false)
                        renderHiddenTable(response['result'])
                        renderResultTable(response['result'])
                    },
                    error: function (response) {
                        destroyProgress(progressId, interval)
                        $('#start-analysis').attr('disabled', false)
                        $('.toast.toast-error').show(300)
                        setTimeout(function () {
                            $('.toast.toast-error').hide(300)
                        }, 5000)
                    }
                });
            }
        </script>
    @endslot
@endcomponent
