@component('component.card', ['title' =>  __('Statistics')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
        <style>
            i:hover {
                opacity: 1 !important;
                transition: .3s;
            }

            .empty-td {
                background: #dee2e6;
                width: 0;
                padding: 0 !important;
                border: none;
            }

            .fixed-width {
                max-width: 50px !important;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message" id="message-info"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message" id="message-error-info"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
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
                        <a href="{{ route('access.project') }}"
                           class="nav-link">{{ __('Projects available to you') }}</a>
                    </li>
                    @if($admin)
                        <li class="nav-item">
                            <a class="nav-link active"
                               href="{{ route('all.relevance.projects') }}">{{ __('Statistics') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('show.config') }}">{{ __('Module administration') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <h3>{{ __('General statistics of the module') }}</h3>
                    <table style="margin: 0 0 35px 0 !important; width: 35%" id="statistics_table"
                           class="table table-bordered table-hover dataTable dtr-inline mb-5">
                        <tbody>
                        <tr>
                            <th class="col-10">{{ __('Number of checks for the current day') }}</th>
                            <td> {{ $statistics['toDay']['count_checks'] ?? 0 }} </td>
                        </tr>
                        <tr>
                            <th class="col-10">{{ __('Number of checks for the current month') }}</th>
                            <td> {{ $statistics['month']}} </td>
                        </tr>
                        <tr>
                            <th class="col-10">{{ __('Number of errors for the current day') }}</th>
                            <td> {{ $statistics['toDay']['count_fails'] ?? 0 }} </td>
                        </tr>
                        <tr>
                            <th class="col-10">{{ __('Number of unique landing pages') }}</th>
                            <td> {{ $statistics['pages'] }} </td>
                        </tr>
                        <tr>
                            <th class="col-10">{{ __('Number of unique landing domains') }}</th>
                            <td> {{ $statistics['domains'] }} </td>
                        </tr>
                        <tr>
                            <th class="col-10">{{ __('Total number of unique analyzed domains') }}</th>
                            <td> {{ $statistics['allDomains'] }} </td>
                        </tr>
                        <tr>
                            <th class="col-10">{{ __('Total number of unique analyzed sites') }}</th>
                            <td> {{ $statistics['allPages'] }} </td>
                        </tr>
                        <tr>
                            <th class="col-10">{{ __('Number of tasks in the queue') }}</th>
                            <td> {{ $statistics['countJobs'] }} </td>
                        </tr>
                        </tbody>
                    </table>

                    <h3>{{ __('All user projects') }}</h3>
                    <table id="users_projects" class="table table-bordered table-hover dataTable dtr-inline mb-3">
                        <thead>
                        <tr>
                            <th class="table-header">{{ __('Project name') }}</th>
                            <th class="table-header">{{ __('Tags') }}</th>
                            <th class="table-header">{{ __('Owner') }}</th>
                            <th class="table-header">{{ __('Number of analyzed pages') }}</th>
                            <th class="table-header">{{ __('Number of saved scans') }}</th>
                            <th class="table-header">{{ __('Total score') }}</th>
                            <th class="table-header">{{ __('Avg position') }}</th>
                            <th class="table-header">{{ __('Last check') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $item)
                            <div class="modal fade" id="removeModal{{ $item->id }}" tabindex="-1"
                                 aria-labelledby="removeModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="removeModalLabel">
                                                {{ __('Deleting results from a project') }} {{ $item->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{ __('Confirm the action.') }}
                                            <p>
                                                <b>{{ __('You will not be able to recover the data.') }}</b>
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary remove-empty-results"
                                                    data-target="{{ $item->id }}" data-dismiss="modal">
                                                {{ __('Remove') }}
                                            </button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                                {{ __('Do not delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="removeWithFiltersModal{{ $item->id }}" tabindex="-1"
                                 aria-labelledby="removeWithFiltersModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="removeWithFiltersModalLabel">
                                                {{ __('Deleting results from a project') }} {{ $item->name }}
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="d-flex flex-row">
                                                <div>
                                                    <label>{{ __('Scans performed after (inclusive)') }}</label>
                                                    <input class="form form-control" type="date"
                                                           id="date-filter-after-{{ $item->id }}">
                                                </div>

                                                <div>
                                                    <label>{{ __('Scans performed before (inclusive)') }}</label>
                                                    <input class="form form-control" type="date"
                                                           id="date-filter-before-{{ $item->id }}">
                                                </div>
                                            </div>

                                            <label class="mt-3">{{ __('Comment') }}</label>
                                            <input type="text" class="form form-control" name="comment-filter"
                                                   id="comment-filter-{{ $item->id }}">

                                            <label class="mt-3">{{ __('Phrase') }}</label>
                                            <input type="text" class="form form-control" name="phrase-filter"
                                                   id="phrase-filter-{{ $item->id }}">

                                            <label class="mt-3">{{ __('Region') }}</label>
                                            {!! Form::select('region', [
                                                   'none' => __("Don't search for matches by region"),
                                                   'all' => 'Любой регион',
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
                                                   ], null, ['class' => 'custom-select rounded-0 region', 'id' => 'region-filter-'. $item->id]) !!}

                                            <label class="mt-3">{{ __('Link') }}</label>
                                            <input type="text" class="form form-control"
                                                   name="link-filter"
                                                   id="link-filter-{{ $item->id }}">

                                            <div class="d-flex flex-row mt-3 mb-3" >
                                                <div>
                                                    <label>{{ __('Position from (inclusive)') }}</label>
                                                    <input class="form form-control" type="number"
                                                           id="position-filter-after-{{ $item->id }}"
                                                           placeholder="{{ __('0 - did not get into the top 100') }}">
                                                </div>

                                                <div>
                                                    <label>{{ __('Position up to (inclusive)') }}</label>
                                                    <input class="form form-control" type="number"
                                                           id="position-filter-before-{{ $item->id }}"
                                                           placeholder="{{ __('0 - did not get into the top 100') }}">
                                                </div>
                                            </div>

                                            <span class="__helper-link ui_tooltip_w">
                                                {{ __('How it works') }}
                                                <i class="fa fa-question-circle" style="color: grey"></i>
                                                <span class="ui_tooltip __right" style="width: 350px">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('According to your project') }} {{ $item->name }} {{ __('the results of the scans will be searched by the filter that you will generate.') }} <br>
                                                        {{ __('All matches found will be deleted.') }} <br>
                                                        {{ __("If you don't want to search by any parameter, then leave the field empty.") }}
                                                    </span>
                                                </span>
                                            </span>

                                            <div class="text-danger mt-3 mb-3">
                                                {{ __('You can delete all the results associated with the project') }} {{ $item->name }}, {{ __('if you leave all fields empty, be careful') }}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary remove-with-filters"
                                                    data-dismiss="modal" data-target="{{ $item->id }}">
                                                {{ __('Remove') }}
                                            </button>
                                            <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">{{ __('Do not delete') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <tr>
                                <td>
                                    <a href="#history_table_{{ $item->name }}"
                                       class="project_name"
                                       style="cursor:pointer;"
                                       data-order="{{ $item->id }}">
                                        {{ $item->name }}
                                    </a>

                                    <i class="fa fa-table project_name"
                                       data-order="{{ $item->id }}"
                                       style="opacity: 0.6; cursor:pointer;"></i>

                                    <i class="fa fa-list project_name_v2"
                                       data-order="{{ $item->id }}"
                                       style="opacity: 0.6; cursor:pointer;"></i>
                                    <div class="dropdown" style="display: inline">
                                        <i class="fa fa-cogs" id="dropdownMenuButton" data-toggle="dropdown"
                                           aria-expanded="false" style="opacity: 0.6; cursor: pointer"></i>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <span class="dropdown-item project_name"
                                                  style="cursor:pointer;"
                                                  data-order="{{ $item->id }}">
                                                <i class="fa fa-table"></i>
                                                {{ __('Show the results of the analysis') }}
                                            </span>
                                            <span class="dropdown-item project_name_v2"
                                                  style="cursor:pointer;"
                                                  data-order="{{ $item->id }}">
                                                <i class="fa fa-list"></i>
                                                {{ __('View the results in a list') }}
                                            </span>
                                            <span class="dropdown-item"
                                                  style="cursor:pointer;"
                                                  data-toggle="modal" data-target="#removeModal{{ $item->id }}">
                                                <i class="fa fa-trash"></i>
                                                {{ __('Delete results without comments') }}
                                            </span>
                                            <span class="dropdown-item"
                                                  style="cursor:pointer;"
                                                  data-toggle="modal"
                                                  data-target="#removeWithFiltersModal{{ $item->id }}">
                                                <i class="fa fa-trash"></i>
                                                {{ __('Delete using filters') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td id="project-{{ $item->id }}">
                                    @foreach($item->relevanceTags as $tag)
                                        <div style="color: {{ $tag->color }}">{{ $tag->name }}
                                            <i class="fa fa-trash remove-project-relevance-link"
                                               style="opacity: 0.5; cursor: pointer"
                                               data-tag="{{ $tag->id }}"
                                               data-history="{{ $item->id }}">
                                            </i>
                                        </div>
                                    @endforeach
                                </td>
                                <td data-target="{{ $item->user->email }}">
                                    {{ $item->user->email }}
                                    <span class="text-muted">
                                        {{ $item->user->name }}
                                        {{ $item->user->last_name }}
                                    </span>
                                </td>
                                <td class="count-sites-{{ $item->id }}">{{ $item->count_sites }}</td>
                                <td class="count-checks-{{ $item->id }}">{{ $item->count_checks }}</td>
                                <td class="total-points-{{ $item->id }}">{{ $item->total_points }}</td>
                                <td class="total-positions-{{ $item->id }}">{{ $item->avg_position }}</td>
                                <td>{{ $item->last_check }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div style="display:none;" class="history">
                        <div class="modal fade" id="staticBackdrop" data-backdrop="static" tabindex="-1" role="dialog"
                             aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="staticBackdropLabel">{{ __('Repeat the analysis') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-12">
                                            <div class="form-group required">
                                                <label>{{ __('Your landing page') }}</label>
                                                {!! Form::text("link", null ,["class" => "form-control link", "required"]) !!}
                                            </div>

                                            <div id="site-list">
                                                <div class="form-group required">
                                                    <label>{{ __('List of sites') }}</label>
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
                                                    <select name="count" id="count"
                                                            class="custom-select rounded-0 count">
                                                        <option value="10">10</option>
                                                        <option value="20">20</option>
                                                    </select>

                                                </div>

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
                                                           ]), null, ['class' => 'custom-select rounded-0 region']) !!}
                                                </div>

                                                <div class="form-group required" id="ignoredDomainsBlock">
                                                    <label id="ignoredDomains">{{ __('Ignored domains') }}</label>
                                                    {!! Form::textarea("ignoredDomains", null,["class" => "form-control ignoredDomains"] ) !!}
                                                </div>
                                            </div>

                                            <div class="form-group required d-flex align-items-center">
                                                <span>{{ __('Cut the words shorter') }}</span>
                                                <input type="number" class="form form-control col-2 ml-1 mr-1"
                                                       name="separator"
                                                       id="separator">
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
                                                                   name="noIndex">
                                                            <label class="custom-control-label"
                                                                   for="switchNoindex"></label>
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
                                                                   name="hiddenText">
                                                            <label class="custom-control-label"
                                                                   for="switchAltAndTitle"></label>
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
                                                                   name="conjunctionsPrepositionsPronouns">
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
                                                                   name="switchMyListWords">
                                                            <label class="custom-control-label"
                                                                   for="switchMyListWords"></label>
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
                                        </div>
                                    </div>
                                    <input type="hidden" id="hiddenId">
                                    <input type="hidden" id="type">
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                                data-dismiss="modal">{{ __('Close') }}
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="relevance-repeat-scan"
                                                data-dismiss="modal">
                                            {{ __('Repeat the analysis') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3>{{ __("Recent checks") }}</h3>
                        <table id="history_table" class="table table-bordered table-hover dataTable dtr-inline w-100">
                            <thead>
                            <tr>
                                <th>
                                    <input class="w-100 form form-control" type="date" name="dateMin"
                                           id="dateMin"
                                           value="{{ Carbon\Carbon::parse('2022-03-01')->toDateString() }}">
                                    <input class="w-100 form form-control" type="date" name="dateMax" id="dateMax"
                                           value="{{ Carbon\Carbon::now()->toDateString() }}">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="projectComment" id="projectComment" placeholder="comment">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="phraseSearch" id="phraseSearch" placeholder="phrase">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="regionSearch" id="regionSearch" placeholder="region">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="text"
                                           name="mainPageSearch" id="mainPageSearch" placeholder="link">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minPosition" id="minPosition" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxPosition" id="maxPosition" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minPoints" id="minPoints" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxPoints" id="maxPoints" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minCoverage" id="minCoverage" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxCoverage" id="maxCoverage" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minCoverageTf" id="minCoverageTf" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxCoverageTf" id="maxCoverageTf" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number" name="minWidth"
                                           id="minWidth" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxWidth" id="maxWidth" placeholder="max">
                                </th>
                                <th>
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="minDensity" id="minDensity" placeholder="min">
                                    <input class="w-100 form form-control search-input" type="number"
                                           name="maxDensity" id="maxDensity" placeholder="max">
                                </th>
                                <th>
                                    <div>
                                        Переключить всё
                                        <div class='d-flex w-100'>
                                            <div class='__helper-link ui_tooltip_w'>
                                                <div
                                                    class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success changeAllState'>
                                                    <input type='checkbox' class='custom-control-input'
                                                           id='changeAllState'>
                                                    <label class='custom-control-label' for='changeAllState'></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th class="table-header">{{ __('Date of last check') }}</th>
                                <th class="table-header" style="min-width: 200px">
                                    {{ __('Comment') }}
                                </th>
                                <th class="table-header" style="min-width: 160px; height: 83px">
                                    {{ __('Phrase') }}
                                </th>
                                <th class="table-header" style="min-width: 160px; height: 83px">
                                    {{ __('Region') }}
                                </th>
                                <th class="table-header" style="min-width: 160px; max-width:160px; height: 83px">
                                    {{ __('Landing page') }}
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    {{ __('Position in the top') }}
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    {{ __('Points') }}
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    {{ __('Coverage of important words') }}
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    {{ __('TF coverage') }}
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    {{ __('Width') }}
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    {{ __('Density') }}
                                </th>
                                <th class="table-header" style="height: 83px; min-width: 69px">
                                    {{ __('Take into account when calculating the total score') }}
                                </th>
                                <th class="table-header"></th>
                            </tr>
                            </thead>
                            <tbody id="historyTbody">
                            </tbody>
                        </table>
                    </div>

                    <h3 style="display: none" id="history-list-subject">{{ __('Scan history (list of phrases)') }}</h3>
                    <table class="table table-bordered table-hover dtr-inline no-footer" id="list-history"
                           style="display: none">
                        <thead>
                        <tr>
                            <th style="position: inherit;"></th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control" type="date" name="dateMinList" id="dateMinList"
                                       value="{{ Carbon\Carbon::parse('2022-03-01')->toDateString() }}">
                                <input class="w-100 form form-control" type="date" name="dateMaxList" id="dateMaxList"
                                       value="{{ Carbon\Carbon::now()->toDateString() }}">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="text"
                                       name="phraseSearchList" id="phraseSearchList" placeholder="phrase">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="text"
                                       name="regionSearchList" id="regionSearchList" placeholder="region">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="text"
                                       name="mainPageSearchList" id="mainPageSearchList" placeholder="link">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minPositionList" id="minPositionList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxPositionList" id="maxPositionList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minPointsList" id="minPointsList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxPointsList" id="maxPointsList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minCoverageList" id="minCoverageList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxCoverageList" id="maxCoverageList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minCoverageTfList" id="minCoverageTfList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxCoverageTfList" id="maxCoverageTfList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number" name="minWidthList"
                                       id="minWidthList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxWidthList" id="maxWidthList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minDensityList" id="minDensityList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxDensityList" id="maxDensityList" placeholder="max">
                            </th>
                        </tr>
                        <tr>
                            <th class="table-header" style="position: inherit;"></th>
                            <th class="table-header" style="position: inherit;">{{ __('Date of last check') }}</th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Phrase') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Region') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Landing page') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Position in the top') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Scores') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Coverage of important words') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('TF coverage') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Width') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Density') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody id="list-history-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script>
            $('input#switchMyListWords').click(function () {
                if ($(this).is(':checked')) {
                    $('.form-group.required.list-words.mt-1').show(300)
                } else {
                    $('.form-group.required.list-words.mt-1').hide(300)
                }
            })

            function getRegionName(id) {
                switch (id) {
                    case '1' :
                        return "{{ __('Moscow') }}";
                    case '20' :
                        return "{{ __('Arkhangelsk') }}";
                    case '37' :
                        return "{{ __('Astrakhan') }}";
                    case '197' :
                        return "{{ __('Barnaul') }}";
                    case '4' :
                        return "{{ __('Belgorod') }}";
                    case '77' :
                        return "{{ __('Blagoveshchensk') }}";
                    case '191' :
                        return "{{ __('Bryansk') }}";
                    case '24' :
                        return "{{ __('Veliky Novgorod') }}";
                    case '75' :
                        return "{{ __('Vladivostok') }}";
                    case '33' :
                        return "{{ __('Vladikavkaz') }}";
                    case '192' :
                        return "{{ __('Vladimir') }}";
                    case '38' :
                        return "{{ __('Volgograd') }}";
                    case '21' :
                        return "{{ __('Vologda') }}";
                    case '193' :
                        return "{{ __('Voronezh') }}";
                    case '1106' :
                        return "{{ __('Grozny') }}";
                    case '54' :
                        return "{{ __('Ekaterinburg') }}";
                    case '5' :
                        return "{{ __('Ivanovo') }}";
                    case '63' :
                        return "{{ __('Irkutsk') }}";
                    case '41' :
                        return "{{ __('Yoshkar-ola') }}";
                    case '43' :
                        return "{{ __('Kazan') }}";
                    case '22' :
                        return "{{ __('Kaliningrad') }}";
                    case '64' :
                        return "{{ __('Kemerovo') }}";
                    case '7' :
                        return "{{ __('Kostroma') }}";
                    case '35' :
                        return "{{ __('Krasnodar') }}";
                    case '62' :
                        return "{{ __('Krasnoyarsk') }}";
                    case '53' :
                        return "{{ __('Kurgan') }}";
                    case '8' :
                        return "{{ __('Kursk') }}";
                    case '9' :
                        return "{{ __('Lipetsk') }}";
                    case '28' :
                        return "{{ __('Makhachkala') }}";
                    case '213' :
                        return "{{ __('Moscow') }}";
                    case '23' :
                        return "{{ __('Murmansk') }}";
                    case '1092' :
                        return "{{ __('Nazran') }}";
                    case '30' :
                        return "{{ __('Nalchik') }}";
                    case '47' :
                        return "{{ __('Nizhniy Novgorod') }}";
                    case '65' :
                        return "{{ __('Novosibirsk') }}";
                    case '66' :
                        return "{{ __('Omsk') }}";
                    case '10' :
                        return "{{ __('Eagle') }}";
                    case '48' :
                        return "{{ __('Orenburg') }}";
                    case '49' :
                        return "{{ __('Penza') }}";
                    case '50' :
                        return "{{ __('Perm') }}";
                    case '25' :
                        return "{{ __('Pskov') }}";
                    case '39' :
                        return "{{ __('Rostov-on') }}";
                    case '11' :
                        return "{{ __('Ryazan') }}";
                    case '51' :
                        return "{{ __('Samara') }}";
                    case '42' :
                        return "{{ __('Saransk') }}";
                    case '2' :
                        return "{{ __('Saint-Petersburg') }}";
                    case '12' :
                        return "{{ __('Smolensk') }}";
                    case '239' :
                        return "{{ __('Sochi') }}";
                    case '36' :
                        return "{{ __('Stavropol') }}";
                    case '973' :
                        return "{{ __('Surgut') }}";
                    case '13' :
                        return "{{ __('Tambov') }}";
                    case '14' :
                        return "{{ __('Tver') }}";
                    case '67' :
                        return "{{ __('Tomsk') }}";
                    case '15' :
                        return "{{ __('Tula') }}";
                    case '195' :
                        return "{{ __('Ulyanovsk') }}";
                    case '172' :
                        return "{{ __('Ufa') }}";
                    case '76' :
                        return "{{ __('Khabarovsk') }}";
                    case '45' :
                        return "{{ __('Cheboksary') }}";
                    case '56' :
                        return "{{ __('Chelyabinsk') }}";
                    case '1104' :
                        return "{{ __('Cherkessk') }}";
                    case '16' :
                        return "{{ __('Yaroslavl') }}";
                }
            }
        </script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/mainHistoryTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/childHistoryTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/common.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/1.12.0/sorting/date-dd-MMM-yyyy.js"></script>
        <script>
            let usersProjects = $('#users_projects').DataTable({
                order: [[0, "desc"]],
                pageLength: 10,
                searching: true,
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel'
                ]
            });

            $(".dt-button").addClass('btn btn-secondary')

            function refreshMethods() {
                $('.fa.fa-plus.show-stories').unbind().on('click', function () {
                    let target = $(this).attr('data-target');
                    $("td[data-order='" + target + "']").show()

                    $(this).attr('class', 'fa fa-minus hide-stories')
                });

                $('.fa.fa-minus.hide-stories').unbind().on('click', function () {
                    let target = $(this).attr('data-target');
                    $("td[data-order='" + target + "']").hide()

                    $(this).attr('class', 'fa fa-plus show-stories')
                });
            }

            setInterval(() => {
                refreshMethods()
            }, 200)

            function getSuccessMessage(message) {
                $('.toast-top-right.success-message').show(300)
                $('#message-info').html(message)
                setTimeout(() => {
                    $('.toast-top-right.success-message').hide(300)
                }, 3000)
            }

            function getErrorMessage(message) {
                $('.toast-top-right.error-message').show(300)
                $('#message-error-info').html(message)
                setTimeout(() => {
                    $('.toast-top-right.error-message').hide(300)
                }, 3000)
            }
        </script>
    @endslot
@endcomponent
