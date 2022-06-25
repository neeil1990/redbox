{{--@component('component.card', ['title' =>  'Ваша история анализа'])--}}
{{--    @slot('css')--}}
{{--        <link rel="stylesheet" type="text/css"--}}
{{--              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>--}}
{{--        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>--}}
{{--        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>--}}
{{--        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>--}}
{{--        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>--}}
{{--        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>--}}
{{--    @endslot--}}

{{--    <div id="toast-container" class="toast-top-right success-message" style="display:none;">--}}
{{--        <div class="toast toast-success" aria-live="polite">--}}
{{--            <div class="toast-message" id="toast-success-message"></div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div id="toast-container" class="toast-top-right error-message" style="display:none;">--}}
{{--        <div class="toast toast-error" aria-live="polite">--}}
{{--            <div class="toast-message error-message" id="toast-message"></div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="card">--}}
{{--        <div class="card-header d-flex p-0">--}}
{{--            <div class="card-header d-flex p-0">--}}
{{--                <ul class="nav nav-pills p-2">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link" href="{{ route('relevance-analysis') }}">{{ __('Analyzer') }}</a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link" href="{{ route('create.queue.view') }}">--}}
{{--                            {{ __('Create page analysis tasks') }}--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link" href="{{ route('relevance.history') }}">{{ __('History') }}</a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('sharing.view') }}" class="nav-link">{{ __('Share your projects') }}</a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('access.project') }}" class="nav-link active">{{ __('Projects available to you') }}</a>--}}
{{--                    </li>--}}
{{--                    @if($admin)--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link" href="{{ route('show.config') }}" >{{ __('Module administration') }}</a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="card-body">--}}
{{--            <div class="tab-content">--}}
{{--                <div class="tab-pane active" id="tab_1">--}}
{{--                    <table id="main_history_table" class="table table-bordered table-hover dataTable dtr-inline mb-3">--}}
{{--                        <thead>--}}
{{--                        <tr>--}}
{{--                            <th>{{ __('Project name') }}</th>--}}
{{--                            <th>{{ __('Owner') }}</th>--}}
{{--                            <th class="table-header">{{ __('Number of analyzed pages') }}</th>--}}
{{--                            <th>{{ __('Last check') }}</th>--}}
{{--                            <th>{{ __('Total score') }}</th>--}}
{{--                            <th></th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        @foreach($projects as $item)--}}
{{--                            <tr>--}}
{{--                                <td class="project_name" style="cursor:pointer;"--}}
{{--                                    data-order="{{ $item->project[0]->id }}"--}}
{{--                                    data-access="{{ $item->access }}">--}}
{{--                                    <a href="#history_table_{{ $item->project[0]->name }}">--}}
{{--                                        {{ $item->project[0]->name }}--}}
{{--                                    </a>--}}
{{--                                    <p>--}}
{{--                                        @if($item->access == 1)--}}
{{--                                            Доступен только просмотр--}}
{{--                                        @elseif($item->access == 2)--}}
{{--                                            Доступен просмотр и возможность запуска повторного анализа--}}
{{--                                        @endif--}}
{{--                                    </p>--}}
{{--                                </td>--}}
{{--                                <td id="project-{{ $item->project[0]->id }}">--}}
{{--                                    {{ $item->owner->email }}--}}
{{--                                    <span class="text-muted">--}}
{{--                                        {{ $item->owner->name }}--}}
{{--                                        {{ $item->owner->last_name }}--}}
{{--                                    </span>--}}
{{--                                </td>--}}
{{--                                <td class="col-2">{{ $item->project[0]->count_sites }}</td>--}}
{{--                                <td>{{ $item->project[0]->last_check }}</td>--}}
{{--                                <td>{{ $item->project[0]->total_points }}</td>--}}
{{--                                <td>--}}
{{--                                    <button class="btn btn-secondary remove-access" data-target="{{ $item->id }}">--}}
{{--                                        Отказаться от доступа--}}
{{--                                    </button>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                    <div style="display:none;" class="history">--}}
{{--                        <div class="modal fade" id="staticBackdrop" data-backdrop="static" tabindex="-1" role="dialog"--}}
{{--                             aria-labelledby="staticBackdropLabel" aria-hidden="true">--}}
{{--                            <div class="modal-dialog" role="document">--}}
{{--                                <div class="modal-content">--}}
{{--                                    <div class="modal-header">--}}
{{--                                        <h5 class="modal-title"--}}
{{--                                            id="staticBackdropLabel">{{ __('Repeat the analysis') }}</h5>--}}
{{--                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                                            <span aria-hidden="true">&times;</span>--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                    <div class="modal-body">--}}
{{--                                        <div class="col-12">--}}
{{--                                            <div class="form-group required">--}}
{{--                                                <label>{{ __('Your landing page') }}</label>--}}
{{--                                                {!! Form::text("link", null ,["class" => "form-control link", "required"]) !!}--}}
{{--                                            </div>--}}

{{--                                            <div id="site-list">--}}
{{--                                                <div class="form-group required">--}}
{{--                                                    <label>{{ __('List of sites') }}</label>--}}
{{--                                                    {!! Form::textarea("siteList", null ,["class" => "form-control", 'id'=>'siteList'] ) !!}--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div id="key-phrase">--}}
{{--                                                <div class="form-group required">--}}
{{--                                                    <label>{{ __('Keyword') }}</label>--}}
{{--                                                    {!! Form::text("phrase", null ,["class" => "form-control phrase", "required"]) !!}--}}
{{--                                                </div>--}}

{{--                                                <div class="form-group required">--}}
{{--                                                    <label>{{ __('Top 10/20') }}</label>--}}
{{--                                                    <select name="count" id="count"--}}
{{--                                                            class="custom-select rounded-0 count">--}}
{{--                                                        <option value="10">10</option>--}}
{{--                                                        <option value="20">20</option>--}}
{{--                                                    </select>--}}

{{--                                                </div>--}}

{{--                                                <div class="form-group required">--}}
{{--                                                    <label>{{ __('Region') }}</label>--}}
{{--                                                    {!! Form::select('region', array_unique([--}}
{{--                                                           $config->region => $config->region,--}}
{{--                                                           '1' => __('Moscow'),--}}
{{--                                                           '20' => __('Arkhangelsk'),--}}
{{--                                                           '37' => __('Astrakhan'),--}}
{{--                                                           '197' => __('Barnaul'),--}}
{{--                                                           '4' => __('Belgorod'),--}}
{{--                                                           '77' => __('Blagoveshchensk'),--}}
{{--                                                           '191' => __('Bryansk'),--}}
{{--                                                           '24' => __('Veliky Novgorod'),--}}
{{--                                                           '75' => __('Vladivostok'),--}}
{{--                                                           '33' => __('Vladikavkaz'),--}}
{{--                                                           '192' => __('Vladimir'),--}}
{{--                                                           '38' => __('Volgograd'),--}}
{{--                                                           '21' => __('Vologda'),--}}
{{--                                                           '193' => __('Voronezh'),--}}
{{--                                                           '1106' => __('Grozny'),--}}
{{--                                                           '54' => __('Ekaterinburg'),--}}
{{--                                                           '5' => __('Ivanovo'),--}}
{{--                                                           '63' => __('Irkutsk'),--}}
{{--                                                           '41' => __('Yoshkar-ola'),--}}
{{--                                                           '43' => __('Kazan'),--}}
{{--                                                           '22' => __('Kaliningrad'),--}}
{{--                                                           '64' => __('Kemerovo'),--}}
{{--                                                           '7' => __('Kostroma'),--}}
{{--                                                           '35' => __('Krasnodar'),--}}
{{--                                                           '62' => __('Krasnoyarsk'),--}}
{{--                                                           '53' => __('Kurgan'),--}}
{{--                                                           '8' => __('Kursk'),--}}
{{--                                                           '9' => __('Lipetsk'),--}}
{{--                                                           '28' => __('Makhachkala'),--}}
{{--                                                           '213' => __('Moscow'),--}}
{{--                                                           '23' => __('Murmansk'),--}}
{{--                                                           '1092' => __('Nazran'),--}}
{{--                                                           '30' => __('Nalchik'),--}}
{{--                                                           '47' => __('Nizhniy Novgorod'),--}}
{{--                                                           '65' => __('Novosibirsk'),--}}
{{--                                                           '66' => __('Omsk'),--}}
{{--                                                           '10' => __('Eagle'),--}}
{{--                                                           '48' => __('Orenburg'),--}}
{{--                                                           '49' => __('Penza'),--}}
{{--                                                           '50' => __('Perm'),--}}
{{--                                                           '25' => __('Pskov'),--}}
{{--                                                           '39' => __('Rostov-on-Don'),--}}
{{--                                                           '11' => __('Ryazan'),--}}
{{--                                                           '51' => __('Samara'),--}}
{{--                                                           '42' => __('Saransk'),--}}
{{--                                                           '2' => __('Saint-Petersburg'),--}}
{{--                                                           '12' => __('Smolensk'),--}}
{{--                                                           '239' => __('Sochi'),--}}
{{--                                                           '36' => __('Stavropol'),--}}
{{--                                                           '973' => __('Surgut'),--}}
{{--                                                           '13' => __('Tambov'),--}}
{{--                                                           '14' => __('Tver'),--}}
{{--                                                           '67' => __('Tomsk'),--}}
{{--                                                           '15' => __('Tula'),--}}
{{--                                                           '195' => __('Ulyanovsk'),--}}
{{--                                                           '172' => __('Ufa'),--}}
{{--                                                           '76' => __('Khabarovsk'),--}}
{{--                                                           '45' => __('Cheboksary'),--}}
{{--                                                           '56' => __('Chelyabinsk'),--}}
{{--                                                           '1104' => __('Cherkessk'),--}}
{{--                                                           '16' => __('Yaroslavl'),--}}
{{--                                                           ]), null, ['class' => 'custom-select rounded-0 region']) !!}--}}
{{--                                                </div>--}}

{{--                                                <div class="form-group required" id="ignoredDomainsBlock">--}}
{{--                                                    <label id="ignoredDomains">{{ __('Ignored domains') }}</label>--}}
{{--                                                    {!! Form::textarea("ignoredDomains", null,["class" => "form-control ignoredDomains"] ) !!}--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div class="form-group required d-flex align-items-center">--}}
{{--                                                <span>{{ __('Cut the words shorter') }}</span>--}}
{{--                                                <input type="number" class="form form-control col-2 ml-1 mr-1"--}}
{{--                                                       name="separator"--}}
{{--                                                       id="separator">--}}
{{--                                                <span>{{ __('symbols') }}</span>--}}
{{--                                            </div>--}}

{{--                                            <div class="switch mt-3 mb-3">--}}
{{--                                                <div class="d-flex">--}}
{{--                                                    <div class="__helper-link ui_tooltip_w">--}}
{{--                                                        <div--}}
{{--                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">--}}
{{--                                                            <input type="checkbox"--}}
{{--                                                                   class="custom-control-input"--}}
{{--                                                                   id="switchNoindex"--}}
{{--                                                                   name="noIndex">--}}
{{--                                                            <label class="custom-control-label"--}}
{{--                                                                   for="switchNoindex"></label>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <p>{{ __('Track the text in the noindex tag') }}</p>--}}
{{--                                                </div>--}}
{{--                                                <div class="d-flex">--}}
{{--                                                    <div class="__helper-link ui_tooltip_w">--}}
{{--                                                        <div--}}
{{--                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">--}}
{{--                                                            <input type="checkbox"--}}
{{--                                                                   class="custom-control-input"--}}
{{--                                                                   id="switchAltAndTitle"--}}
{{--                                                                   name="hiddenText">--}}
{{--                                                            <label class="custom-control-label"--}}
{{--                                                                   for="switchAltAndTitle"></label>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <p>{{ __('Track words in the alt, title, and data-text attributes') }}</p>--}}
{{--                                                </div>--}}
{{--                                                <div class="d-flex">--}}
{{--                                                    <div class="__helper-link ui_tooltip_w">--}}
{{--                                                        <div--}}
{{--                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">--}}
{{--                                                            <input type="checkbox"--}}
{{--                                                                   class="custom-control-input"--}}
{{--                                                                   id="switchConjunctionsPrepositionsPronouns"--}}
{{--                                                                   name="conjunctionsPrepositionsPronouns">--}}
{{--                                                            <label class="custom-control-label"--}}
{{--                                                                   for="switchConjunctionsPrepositionsPronouns"></label>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <p>{{ __('Track conjunctions, prepositions, pronouns') }}</p>--}}
{{--                                                </div>--}}
{{--                                                <div class="d-flex">--}}
{{--                                                    <div class="__helper-link ui_tooltip_w">--}}
{{--                                                        <div--}}
{{--                                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">--}}
{{--                                                            <input type="checkbox"--}}
{{--                                                                   class="custom-control-input"--}}
{{--                                                                   id="switchMyListWords"--}}
{{--                                                                   name="switchMyListWords">--}}
{{--                                                            <label class="custom-control-label"--}}
{{--                                                                   for="switchMyListWords"></label>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <span>{{ __('Exclude') }}<span--}}
{{--                                                            class="text-muted">{{ __('(your own list of words)') }}</span></span>--}}
{{--                                                </div>--}}
{{--                                                <div class="form-group required list-words mt-1"--}}
{{--                                                     @if($config->remove_my_list_words == 'no') style="display:none;" @endif >--}}
{{--                                                    {!! Form::textarea('listWords', $config->my_list_words,['class' => 'form-control listWords', 'cols' => 8, 'rows' => 5]) !!}--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <input type="hidden" id="hiddenId">--}}
{{--                                    <input type="hidden" id="type">--}}
{{--                                    <div class="modal-footer">--}}
{{--                                        <button type="button" class="btn btn-default"--}}
{{--                                                data-dismiss="modal">{{ __('Close') }}--}}
{{--                                        </button>--}}
{{--                                        <button type="button" class="btn btn-secondary" id="relevance-repeat-scan"--}}
{{--                                                data-dismiss="modal">--}}
{{--                                            {{ __('Repeat the analysis') }}--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <h3>{{ __("Recent checks") }}</h3>--}}
{{--                        <table id="history_table" class="table table-bordered table-hover dataTable dtr-inline w-100">--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control" type="date" name="dateMin"--}}
{{--                                           id="dateMin"--}}
{{--                                           value="{{ Carbon\Carbon::parse('2022-03-01')->toDateString() }}">--}}
{{--                                    <input class="w-100 form form-control" type="date" name="dateMax" id="dateMax"--}}
{{--                                           value="{{ Carbon\Carbon::now()->toDateString() }}">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="text"--}}
{{--                                           name="projectComment" id="projectComment" placeholder="comment">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="text"--}}
{{--                                           name="phraseSearch" id="phraseSearch" placeholder="phrase">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="text"--}}
{{--                                           name="regionSearch" id="regionSearch" placeholder="region">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="text"--}}
{{--                                           name="mainPageSearch" id="mainPageSearch" placeholder="link">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="minPosition" id="minPosition" placeholder="min">--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="maxPosition" id="maxPosition" placeholder="max">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="minPoints" id="minPoints" placeholder="min">--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="maxPoints" id="maxPoints" placeholder="max">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="minCoverage" id="minCoverage" placeholder="min">--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="maxCoverage" id="maxCoverage" placeholder="max">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="minCoverageTf" id="minCoverageTf" placeholder="min">--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="maxCoverageTf" id="maxCoverageTf" placeholder="max">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="number" name="minWidth"--}}
{{--                                           id="minWidth" placeholder="min">--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="maxWidth" id="maxWidth" placeholder="max">--}}
{{--                                </th>--}}
{{--                                <th>--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="minDensity" id="minDensity" placeholder="min">--}}
{{--                                    <input class="w-100 form form-control search-input" type="number"--}}
{{--                                           name="maxDensity" id="maxDensity" placeholder="max">--}}
{{--                                </th>--}}
{{--                                <th>--}}

{{--                                </th>--}}
{{--                                <th></th>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <th class="table-header">Дата последней проверки</th>--}}
{{--                                <th class="table-header" style="min-width: 200px">--}}
{{--                                    Комментарий--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="min-width: 160px; height: 83px">--}}
{{--                                    Фраза--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="min-width: 160px; height: 83px">--}}
{{--                                    Регион--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="min-width: 160px; max-width:160px; height: 83px">--}}
{{--                                    Посадочная страница--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="height: 83px; min-width: 69px">--}}
{{--                                    Позиция в топе--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="height: 83px; min-width: 69px">--}}
{{--                                    Баллы--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="height: 83px; min-width: 69px">--}}
{{--                                    Охват важных--}}
{{--                                    слова--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="height: 83px; min-width: 69px">--}}
{{--                                    Охват tf--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="height: 83px; min-width: 69px">--}}
{{--                                    Ширина--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="height: 83px; min-width: 69px">--}}
{{--                                    Плотность--}}
{{--                                </th>--}}
{{--                                <th class="table-header" style="height: 83px; min-width: 69px">--}}
{{--                                    Учитывать в--}}
{{--                                    расчёте общего--}}
{{--                                    балла--}}
{{--                                </th>--}}
{{--                                <th class="table-header"></th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody id="historyTbody">--}}
{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    @slot('js')--}}
{{--        <script>--}}
{{--            $('input#switchMyListWords').click(function () {--}}
{{--                if ($(this).is(':checked')) {--}}
{{--                    $('.form-group.required.list-words.mt-1').show(300)--}}
{{--                } else {--}}
{{--                    $('.form-group.required.list-words.mt-1').hide(300)--}}
{{--                }--}}
{{--            })--}}

{{--            function getRegionName(id) {--}}
{{--                switch (id) {--}}
{{--                    case '1' :--}}
{{--                        return "{{ __('Moscow') }}";--}}
{{--                    case '20' :--}}
{{--                        return "{{ __('Arkhangelsk') }}";--}}
{{--                    case '37' :--}}
{{--                        return "{{ __('Astrakhan') }}";--}}
{{--                    case '197' :--}}
{{--                        return "{{ __('Barnaul') }}";--}}
{{--                    case '4' :--}}
{{--                        return "{{ __('Belgorod') }}";--}}
{{--                    case '77' :--}}
{{--                        return "{{ __('Blagoveshchensk') }}";--}}
{{--                    case '191' :--}}
{{--                        return "{{ __('Bryansk') }}";--}}
{{--                    case '24' :--}}
{{--                        return "{{ __('Veliky Novgorod') }}";--}}
{{--                    case '75' :--}}
{{--                        return "{{ __('Vladivostok') }}";--}}
{{--                    case '33' :--}}
{{--                        return "{{ __('Vladikavkaz') }}";--}}
{{--                    case '192' :--}}
{{--                        return "{{ __('Vladimir') }}";--}}
{{--                    case '38' :--}}
{{--                        return "{{ __('Volgograd') }}";--}}
{{--                    case '21' :--}}
{{--                        return "{{ __('Vologda') }}";--}}
{{--                    case '193' :--}}
{{--                        return "{{ __('Voronezh') }}";--}}
{{--                    case '1106' :--}}
{{--                        return "{{ __('Grozny') }}";--}}
{{--                    case '54' :--}}
{{--                        return "{{ __('Ekaterinburg') }}";--}}
{{--                    case '5' :--}}
{{--                        return "{{ __('Ivanovo') }}";--}}
{{--                    case '63' :--}}
{{--                        return "{{ __('Irkutsk') }}";--}}
{{--                    case '41' :--}}
{{--                        return "{{ __('Yoshkar-ola') }}";--}}
{{--                    case '43' :--}}
{{--                        return "{{ __('Kazan') }}";--}}
{{--                    case '22' :--}}
{{--                        return "{{ __('Kaliningrad') }}";--}}
{{--                    case '64' :--}}
{{--                        return "{{ __('Kemerovo') }}";--}}
{{--                    case '7' :--}}
{{--                        return "{{ __('Kostroma') }}";--}}
{{--                    case '35' :--}}
{{--                        return "{{ __('Krasnodar') }}";--}}
{{--                    case '62' :--}}
{{--                        return "{{ __('Krasnoyarsk') }}";--}}
{{--                    case '53' :--}}
{{--                        return "{{ __('Kurgan') }}";--}}
{{--                    case '8' :--}}
{{--                        return "{{ __('Kursk') }}";--}}
{{--                    case '9' :--}}
{{--                        return "{{ __('Lipetsk') }}";--}}
{{--                    case '28' :--}}
{{--                        return "{{ __('Makhachkala') }}";--}}
{{--                    case '213' :--}}
{{--                        return "{{ __('Moscow') }}";--}}
{{--                    case '23' :--}}
{{--                        return "{{ __('Murmansk') }}";--}}
{{--                    case '1092' :--}}
{{--                        return "{{ __('Nazran') }}";--}}
{{--                    case '30' :--}}
{{--                        return "{{ __('Nalchik') }}";--}}
{{--                    case '47' :--}}
{{--                        return "{{ __('Nizhniy Novgorod') }}";--}}
{{--                    case '65' :--}}
{{--                        return "{{ __('Novosibirsk') }}";--}}
{{--                    case '66' :--}}
{{--                        return "{{ __('Omsk') }}";--}}
{{--                    case '10' :--}}
{{--                        return "{{ __('Eagle') }}";--}}
{{--                    case '48' :--}}
{{--                        return "{{ __('Orenburg') }}";--}}
{{--                    case '49' :--}}
{{--                        return "{{ __('Penza') }}";--}}
{{--                    case '50' :--}}
{{--                        return "{{ __('Perm') }}";--}}
{{--                    case '25' :--}}
{{--                        return "{{ __('Pskov') }}";--}}
{{--                    case '39' :--}}
{{--                        return "{{ __('Rostov-on') }}";--}}
{{--                    case '11' :--}}
{{--                        return "{{ __('Ryazan') }}";--}}
{{--                    case '51' :--}}
{{--                        return "{{ __('Samara') }}";--}}
{{--                    case '42' :--}}
{{--                        return "{{ __('Saransk') }}";--}}
{{--                    case '2' :--}}
{{--                        return "{{ __('Saint-Petersburg') }}";--}}
{{--                    case '12' :--}}
{{--                        return "{{ __('Smolensk') }}";--}}
{{--                    case '239' :--}}
{{--                        return "{{ __('Sochi') }}";--}}
{{--                    case '36' :--}}
{{--                        return "{{ __('Stavropol') }}";--}}
{{--                    case '973' :--}}
{{--                        return "{{ __('Surgut') }}";--}}
{{--                    case '13' :--}}
{{--                        return "{{ __('Tambov') }}";--}}
{{--                    case '14' :--}}
{{--                        return "{{ __('Tver') }}";--}}
{{--                    case '67' :--}}
{{--                        return "{{ __('Tomsk') }}";--}}
{{--                    case '15' :--}}
{{--                        return "{{ __('Tula') }}";--}}
{{--                    case '195' :--}}
{{--                        return "{{ __('Ulyanovsk') }}";--}}
{{--                    case '172' :--}}
{{--                        return "{{ __('Ufa') }}";--}}
{{--                    case '76' :--}}
{{--                        return "{{ __('Khabarovsk') }}";--}}
{{--                    case '45' :--}}
{{--                        return "{{ __('Cheboksary') }}";--}}
{{--                    case '56' :--}}
{{--                        return "{{ __('Chelyabinsk') }}";--}}
{{--                    case '1104' :--}}
{{--                        return "{{ __('Cherkessk') }}";--}}
{{--                    case '16' :--}}
{{--                        return "{{ __('Yaroslavl') }}";--}}
{{--                }--}}
{{--            }--}}
{{--        </script>--}}
{{--        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
{{--        <script src="{{ asset('plugins/relevance-analysis/history/mainHistoryTable.js') }}"></script>--}}
{{--        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>--}}
{{--        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>--}}
{{--        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>--}}
{{--        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>--}}
{{--        <script src="https://cdn.datatables.net/plug-ins/1.12.0/sorting/date-dd-MMM-yyyy.js"></script>--}}
{{--        <script>--}}
{{--            $('.remove-access').on('click', function () {--}}
{{--                let button = $(this)--}}
{{--                $.ajax({--}}
{{--                    type: "POST",--}}
{{--                    dataType: "json",--}}
{{--                    url: "{{ route('remove.guest.access') }}",--}}
{{--                    data: {--}}
{{--                        _token: $('meta[name="csrf-token"]').attr('content'),--}}
{{--                        id: button.attr('data-target'),--}}
{{--                    },--}}
{{--                    success: function (response) {--}}
{{--                        if (response.code === 201) {--}}
{{--                            $('.toast-top-right.success-message').show(300)--}}
{{--                            $('.toast-message').html(response.message)--}}
{{--                            setTimeout(() => {--}}
{{--                                $('.toast-top-right.success-message').hide(300)--}}
{{--                            }, 3000)--}}
{{--                            button.parent().parent().remove()--}}
{{--                        } else if (response.code === 415) {--}}
{{--                            $('.toast-top-right.error-message').show(300)--}}
{{--                            $('.toast-message.error-message').html(response.message)--}}
{{--                            setTimeout(() => {--}}
{{--                                $('.toast-top-right.error-message').hide(300)--}}
{{--                            }, 3000)--}}
{{--                        }--}}
{{--                    },--}}
{{--                });--}}
{{--            });--}}

{{--            $('.project_name').click(function () {--}}

{{--                let elem = $(this)--}}
{{--                $.ajax({--}}
{{--                    type: "POST",--}}
{{--                    dataType: "json",--}}
{{--                    url: "/get-stories",--}}
{{--                    data: {--}}
{{--                        history_id: $(this).attr('data-order'),--}}
{{--                    },--}}
{{--                    success: function (response) {--}}
{{--                        $('#changeAllState').prop('checked', false);--}}
{{--                        $('.search-input').val('')--}}
{{--                        $('.history').show()--}}
{{--                        $("#history_table").dataTable().fnDestroy();--}}
{{--                        $('.render').remove()--}}
{{--                        let tbody = $('#historyTbody')--}}

{{--                        $.each(response.stories, function (key, val) {--}}
{{--                            let state--}}

{{--                            if (val.state === 1) {--}}
{{--                                state =--}}
{{--                                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + val.id + '" data-toggle="modal" data-target="#staticBackdrop">' +--}}
{{--                                    '   Повторить анализ' +--}}
{{--                                    '</button>'--}}
{{--                                    +--}}
{{--                                    "<a href='/show-history/" + val.id + "' target='_blank' class='btn btn-secondary mt-3'> Подробная информация</a>"--}}

{{--                            } else if (val.state === 0) {--}}
{{--                                state =--}}
{{--                                    '<p>Обрабатывается..</p>' +--}}
{{--                                    '<div class="text-center" id="preloaderBlock">' +--}}
{{--                                    '        <div class="three col">' +--}}
{{--                                    '            <div class="loader" id="loader-1"></div>' +--}}
{{--                                    '        </div>' +--}}
{{--                                    '</div>'--}}
{{--                            } else if (val.state === -1) {--}}
{{--                                state =--}}
{{--                                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + val.id + '" data-toggle="modal" data-target="#staticBackdrop">' +--}}
{{--                                    '   Повторить анализ' +--}}
{{--                                    '</button>' +--}}
{{--                                    "<span class='text-muted'>Произошла ошибка, повторите попытку или обратитесь к администратору</span>"--}}
{{--                            }--}}

{{--                            let position = val.position--}}

{{--                            if (val.position == 0) {--}}
{{--                                position = 'Не попал в топ 100'--}}
{{--                            }--}}

{{--                            let phrase = val.phrase--}}

{{--                            if (phrase == null) {--}}
{{--                                phrase = 'Был использван анализ без ключевой фразы'--}}
{{--                            }--}}

{{--                            let calculate--}}
{{--                            if (val.calculate == 1) {--}}
{{--                                calculate = 'Учитывается при рисчёте баллов'--}}
{{--                            } else {--}}
{{--                                calculate = 'Не учитывается'--}}
{{--                            }--}}

{{--                            tbody.append(--}}
{{--                                "<tr class='render'>" +--}}
{{--                                "<td>" + val.last_check + "</td>" +--}}
{{--                                "<td>" +--}}
{{--                                "   <textarea style='height: 160px;' data-target='" + val.id + "' class='history-comment form form-control' >" + val.comment + "</textarea>" +--}}
{{--                                "</td>" +--}}
{{--                                "<td>" + phrase + "</td>" +--}}
{{--                                "<td>" + getRegionName(val.region) + "</td>" +--}}
{{--                                "<td>" + val.main_link + "</td>" +--}}
{{--                                "<td>" + position + "</td>" +--}}
{{--                                "<td>" + val.points + "</td>" +--}}
{{--                                "<td>" + val.coverage + "</td>" +--}}
{{--                                "<td>" + val.coverage_tf + "</td>" +--}}
{{--                                "<td>" + val.width + "</td>" +--}}
{{--                                "<td>" + val.density + "</td>" +--}}
{{--                                "<td>" +--}}
{{--                                calculate +--}}
{{--                                "</td>" +--}}
{{--                                "<td id='history-state-" + val.id + "'>" +--}}
{{--                                state +--}}
{{--                                "</td>" +--}}
{{--                                "</tr>"--}}
{{--                            )--}}
{{--                        })--}}

{{--                        $(document).ready(function () {--}}
{{--                            let table = $('#history_table').DataTable({--}}
{{--                                "order": [[0, "desc"]],--}}
{{--                                "pageLength": 25,--}}
{{--                                "searching": true,--}}
{{--                                dom: 'lBfrtip',--}}
{{--                                buttons: [--}}
{{--                                    'copy', 'csv', 'excel'--}}
{{--                                ]--}}
{{--                            });--}}

{{--                            $('#history_table').wrap("<div style='width: 100%; overflow-x: scroll; max-height:90vh;'></div>")--}}

{{--                            $(".dt-button").addClass('btn btn-secondary')--}}

{{--                            $('#history_table_filter').hide()--}}

{{--                            let href = '#history_table';--}}
{{--                            $('html, body').animate({--}}
{{--                                scrollTop: $(href).offset().top--}}
{{--                            }, {--}}
{{--                                duration: 370,--}}
{{--                                easing: "linear"--}}
{{--                            });--}}

{{--                            $('.history-comment').change(function () {--}}
{{--                                $.ajax({--}}
{{--                                    type: "POST",--}}
{{--                                    dataType: "json",--}}
{{--                                    url: "/edit-history-comment",--}}
{{--                                    data: {--}}
{{--                                        id: $(this).attr('data-target'),--}}
{{--                                        comment: $(this).val()--}}
{{--                                    },--}}
{{--                                    success: function () {--}}
{{--                                        $('.toast-top-right.success-message').show(300)--}}
{{--                                        $('#toast-success-message').html('Коментарий успешно сохранён')--}}
{{--                                        setInterval(function () {--}}
{{--                                            $('#toast-container').hide(300)--}}
{{--                                        }, 3000)--}}
{{--                                    },--}}
{{--                                });--}}
{{--                            });--}}


{{--                            $('.get-history-info').unbind("click").click(function () {--}}
{{--                                let id = $(this).attr('data-order')--}}
{{--                                $.ajax({--}}
{{--                                    type: "get",--}}
{{--                                    dataType: "json",--}}
{{--                                    url: "/get-history-info/" + id,--}}
{{--                                    success: function (response) {--}}
{{--                                        let history = response.history--}}
{{--                                        if (history.type === 'list') {--}}
{{--                                            $('#key-phrase').hide()--}}
{{--                                            $('#site-list').show()--}}
{{--                                            $('#siteList').val(history.siteList)--}}
{{--                                        } else {--}}
{{--                                            $('#key-phrase').show()--}}
{{--                                            $('#site-list').hide()--}}
{{--                                            $('.form-control.phrase').val(history.phrase)--}}
{{--                                        }--}}
{{--                                        $('#type').val(history.type)--}}
{{--                                        $('#hiddenId').val(id)--}}
{{--                                        $('.form-control.link').val(history.link)--}}
{{--                                        $(".custom-select#count").val(history.count).change();--}}
{{--                                        $(".custom-select.rounded-0.region").val(history.region).change();--}}
{{--                                        $(".form-control.ignoredDomains").html(history.ignoredDomains);--}}
{{--                                        $("#separator").val(history.separator);--}}

{{--                                        if (history.noIndex === "true") {--}}
{{--                                            $('#switchNoindex').trigger('click')--}}
{{--                                        }--}}

{{--                                        if (history.hiddenText === "true") {--}}
{{--                                            $('#switchAltAndTitle').trigger('click')--}}
{{--                                        }--}}

{{--                                        if (history.conjunctionsPrepositionsPronouns === "true") {--}}
{{--                                            $('#switchConjunctionsPrepositionsPronouns').trigger('click')--}}
{{--                                        }--}}

{{--                                        if (history.switchMyListWords === "true") {--}}
{{--                                            $('#switchMyListWords').trigger('click')--}}
{{--                                        }--}}
{{--                                    },--}}
{{--                                });--}}
{{--                            });--}}

{{--                            $('#relevance-repeat-scan').unbind("click").click(function () {--}}
{{--                                let id = $('#hiddenId').val()--}}
{{--                                $.ajax({--}}
{{--                                    type: "POST",--}}
{{--                                    dataType: "json",--}}
{{--                                    url: "/repeat-scan",--}}
{{--                                    data: {--}}
{{--                                        id: id,--}}
{{--                                        type: $('#type').val(),--}}
{{--                                        siteList: $('#siteList').val(),--}}
{{--                                        link: $('.form-control.link').val(),--}}
{{--                                        phrase: $('.form-control.phrase').val(),--}}
{{--                                        count: $(".custom-select#count").val(),--}}
{{--                                        region: $(".custom-select.rounded-0.region").val(),--}}
{{--                                        ignoredDomains: $(".form-control.ignoredDomains").html(),--}}
{{--                                        separator: $("#separator").val(),--}}
{{--                                        noIndex: $('#switchNoindex').is(':checked'),--}}
{{--                                        hiddenText: $('#switchAltAndTitle').is(':checked'),--}}
{{--                                        conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked'),--}}
{{--                                        switchMyListWords: $('#switchMyListWords').is(':checked'),--}}
{{--                                        listWords: $('.form-control.listWords').val(),--}}
{{--                                    },--}}
{{--                                    success: function () {--}}
{{--                                        $('#history-state-' + id).html('<p>Обрабатывается..</p>' +--}}
{{--                                            '<div class="text-center" id="preloaderBlock">' +--}}
{{--                                            '        <div class="three col">' +--}}
{{--                                            '            <div class="loader" id="loader-1"></div>' +--}}
{{--                                            '        </div>' +--}}
{{--                                            '</div>')--}}
{{--                                    },--}}
{{--                                    error: function () {--}}
{{--                                        $('#toast-container').show(300)--}}
{{--                                        $('#message-info').html('Что-то пошло не так, повторите попытку позже.')--}}
{{--                                        setInterval(function () {--}}
{{--                                            $('#toast-container').hide(300)--}}
{{--                                        }, 3500)--}}
{{--                                    }--}}
{{--                                });--}}
{{--                            });--}}

{{--                            if (elem.attr('data-access') === '1') {--}}
{{--                                $('#historyTbody > tr > td:nth-child(13) > button:first-child').hide()--}}
{{--                            } else if (elem.attr('data-access') === '2') {--}}
{{--                                $('#historyTbody > tr > td:nth-child(13) > button:first-child').show()--}}
{{--                            }--}}

{{--                            //------------------------ CUSTOM FILTERS -------------------------}}

{{--                            function isValidate(min, max, target, settings) {--}}
{{--                                return (isNaN(min) && isNaN(max)) ||--}}
{{--                                    (isNaN(min) && target <= max) ||--}}
{{--                                    (min <= target && isNaN(max)) ||--}}
{{--                                    (min <= target && target <= max);--}}
{{--                            }--}}

{{--                            function isIncludes(target, search) {--}}
{{--                                if (search.length > 0) {--}}
{{--                                    return target.includes(search)--}}
{{--                                } else {--}}
{{--                                    return true;--}}
{{--                                }--}}
{{--                            }--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var projectComment = String($('#projectComment').val()).toLowerCase();--}}
{{--                                var target = String(data[1]).toLowerCase();--}}
{{--                                return isIncludes(target, projectComment)--}}
{{--                            });--}}
{{--                            $('#projectComment').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var phraseSearch = String($('#phraseSearch').val()).toLowerCase();--}}
{{--                                var target = String(data[2]).toLowerCase();--}}
{{--                                return isIncludes(target, phraseSearch)--}}
{{--                            });--}}
{{--                            $('#phraseSearch').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var regionSearch = String($('#regionSearch').val()).toLowerCase();--}}
{{--                                var target = String(data[3]).toLowerCase();--}}
{{--                                return isIncludes(target, regionSearch)--}}
{{--                            });--}}
{{--                            $('#regionSearch').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var mainPageSearch = String($('#mainPageSearch').val()).toLowerCase();--}}
{{--                                var target = String(data[4]).toLowerCase();--}}
{{--                                return isIncludes(target, mainPageSearch)--}}
{{--                            });--}}
{{--                            $('#mainPageSearch').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var maxPosition = parseFloat($('#maxPosition').val());--}}
{{--                                var minPosition = parseFloat($('#minPosition').val());--}}
{{--                                var target = parseFloat(data[5]);--}}
{{--                                return isValidate(minPosition, maxPosition, target, settings)--}}
{{--                            });--}}
{{--                            $('#minPosition, #maxPosition').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var maxPoints = parseFloat($('#maxPoints').val());--}}
{{--                                var minPoints = parseFloat($('#minPoints').val());--}}
{{--                                var target = parseFloat(data[6]);--}}
{{--                                return isValidate(minPoints, maxPoints, target, settings)--}}
{{--                            });--}}
{{--                            $('#minPoints, #maxPoints').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var maxCoverage = parseFloat($('#maxCoverage').val());--}}
{{--                                var minCoverage = parseFloat($('#minCoverage').val());--}}
{{--                                var target = parseFloat(data[7]);--}}
{{--                                return isValidate(minCoverage, maxCoverage, target, settings)--}}
{{--                            });--}}
{{--                            $('#minCoverage, #maxCoverage').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var maxCoverageTf = parseFloat($('#maxCoverageTf').val());--}}
{{--                                var minCoverageTf = parseFloat($('#minCoverageTf').val());--}}
{{--                                var target = parseFloat(data[8]);--}}
{{--                                return isValidate(minCoverageTf, maxCoverageTf, target, settings)--}}
{{--                            });--}}
{{--                            $('#minCoverageTf, #maxCoverageTf').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var maxWidth = parseFloat($('#maxWidth').val());--}}
{{--                                var minWidth = parseFloat($('#minWidth').val());--}}
{{--                                var target = parseFloat(data[9]);--}}
{{--                                return isValidate(minWidth, maxWidth, target, settings)--}}
{{--                            });--}}
{{--                            $('#minWidth, #maxWidth').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var maxDensity = parseFloat($('#maxDensity').val());--}}
{{--                                var minDensity = parseFloat($('#minDensity').val());--}}
{{--                                var target = parseFloat(data[10]);--}}
{{--                                return isValidate(minDensity, maxDensity, target, settings)--}}
{{--                            });--}}
{{--                            $('#minDensity, #maxDensity').keyup(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}

{{--                            function isDateValid(target) {--}}
{{--                                let date = new Date(target)--}}
{{--                                let dateMin = new Date($('#dateMin').val() + ' 00:00:00')--}}
{{--                                let dateMax = new Date($('#dateMax').val() + ' 23:59:59')--}}
{{--                                if (date >= dateMin && date <= dateMax) {--}}
{{--                                    return true;--}}
{{--                                }--}}
{{--                            }--}}

{{--                            $.fn.dataTable.ext.search.push(function (settings, data) {--}}
{{--                                var target = String(data[0]);--}}
{{--                                return isDateValid(target)--}}
{{--                            });--}}
{{--                            $('#dateMin').change(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}
{{--                            $('#dateMax').change(function () {--}}
{{--                                table.draw();--}}
{{--                            });--}}
{{--                        });--}}
{{--                    },--}}
{{--                });--}}
{{--            });--}}
{{--        </script>--}}
{{--    @endslot--}}
{{--@endcomponent--}}
