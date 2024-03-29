@component('component.card', ['title' =>  __('Competitor Analyzer Settings') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            #tab_1 > div.d-flex.flex-column > div:nth-child(3) > button.btn.btn-secondary.col-2 > span > span > span,
            #tab_1 > div.d-flex.flex-column > div:nth-child(2) > button.btn.btn-secondary.col-2 > span > span > span,
            #tab_1 > div.d-flex.flex-column > div:nth-child(1) > button.btn.btn-secondary.col-2 > span > span > span {
                width: 400px;
            }

            .CompetitorAnalysisPhrases {
                background: oldlace;
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
                    <a class="nav-link" href="{{ route('competitor.analysis') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link active"
                       style="color: white !important;"
                       href="{{ route('competitor.config') }}">{{ __('Module administration') }}</a>
                </li>
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
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('competitor.edit.config') }}" method="post">
                                    @csrf
                                    <label for="agrigators">{{ __('List of aggregator sites') }}</label>
                                    <textarea name="agrigators"
                                              id="agrigators"
                                              cols="30" rows="10"
                                              class="form form-control"
                                    >{{ $config->agrigators }}</textarea>

                                    <div class="mt-3 mb-3">
                                        <label for="urls_lenght">Стандартная длинна таблицы
                                            <b>"Анализ по страницам"</b>
                                        </label>
                                        {!! Form::select('urls_length', array_unique([
                                                $config->urls_length => $config->urls_length,
                                                '10' => 10,
                                                '25' => 25,
                                                '50' => 50,
                                                '100' => 100,
                                        ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                    </div>

                                    <div class="mt-3 mb-3">
                                        <label for="urls_length">Стандартная длинна таблицы
                                            <b>"Анализ по проценту попадания в топ и средней позиции"</b>
                                        </label>
                                        {!! Form::select('positions_length', array_unique([
                                                $config->positions_length => $config->positions_length,
                                                '10' => 10,
                                                '25' => 25,
                                                '50' => 50,
                                                '100' => 100,
                                        ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                                    </div>

                                    <div class="mt-3 mb-3">
                                        <label for="count_top_10">Среднее количество повторений для вхождения слова в рекомендаци <b>Топ 10</b></label>
                                        <input type="number" name="count_repeat_top_10" class="form form-control" value="{{ $config->count_repeat_top_10 }}">
                                    </div>


                                    <div class="mt-3 mb-3">
                                        <label for="count_top_20">Среднее количество повторений для вхождения слова в рекомендаци <b>Топ 20</b></label>
                                        <input type="number" name="count_repeat_top_20" class="form form-control" value="{{ $config->count_repeat_top_20 }}">
                                    </div>

                                    <input type="submit" class="btn btn-secondary mt-2 float-right" value="{{ __('Update') }}">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('General statistics of the module') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div>
                                    {{ __('Scans in the current month') }} <b>{{ $counter }}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @slot('js')

    @endslot
@endcomponent
