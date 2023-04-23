@php use Illuminate\Support\Str; @endphp
@component('component.card', ['title' =>  __('Editing Clusters') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <style>
            .phrase-for-color {
                width: 370px;
            }

            i:hover {
                cursor: pointer;
                color: black;
            }

            .card-header:after {
                content: none;
            }

            .work-place-conf {
                margin-bottom: 0;
                border-radius: 0;
                position: sticky;
                top: 15px;
                max-height: 80vh;
                overflow: auto
            }

            .cluster-block {
                margin-bottom: 0;
                border-radius: 0;
            }

            .radius {
                border-top-right-radius: 0;
                border-top-left-radius: 0;
            }

            div.sortable {
                width: 100px;
                background-color: lightgrey;
                font-size: large;
                float: left;
                margin: 6px;
                text-align: center;
                border: medium solid black;
                padding: 10px;
            }

            .remove-sort-block {
                color: white;
            }

            .card-header.group-name {
                cursor: move;
            }

            #configurationBlock {
                margin-bottom: 0;
                border-radius: 0;
                position: sticky;
                top: 15px;
                max-height: 80vh;
                overflow: auto;
            }

            .dragged {
                position: absolute;
                top: 0;
                opacity: 0.5;
                z-index: 2000;
            }

            .group-name {
                background-color: rgb(52, 58, 64);
                color: white;
                direction: initial;
            }

            .placeholder {
                height: 40px;
                background-color: rgba(52, 58, 64, 0.27);
            }

            li li.placeholder {
                margin-left: 20px;
            }

            .w-100.pb-3 li:hover {
                background-color: lightgrey;
            }

            .alone:hover {
                background-color: lightgrey;
            }

            .card-header:after {
                content: none;
            }

            .hide {
                display: none !important;
            }

            ol {
                padding: 0 !important;
                list-style: none;
            }

            ol > li > ol > li {
                padding: 0 0 0 20px;
            }

            #clusters-block > li {
                padding: 10px;
            }

            ul .card-header {
                background-color: rgb(52, 58, 64);
                color: white;
            }

            .relevance-link {
                width: 550px !important;
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
                transition: .3s -webkit-line-clamp;
            }

            .relevance-link:hover {
                -webkit-line-clamp: 4;
            }

            .list-group-item, .work-place-li {
                border: 1px solid rgba(128, 128, 128, 0.4) !important;
                padding: 0.75rem 1.25rem;
            }

            .selected-group {
                background-color: #0c84ff !important;
            }

            .selected-group-list .fa-down-left-and-up-right-to-center {
                display: none;
            }

            .cluster-block > .card-header {
                background-color: rgb(52, 58, 64);
                color: white;
            }

            .Clusters {
                background: oldlace;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message">
        <div class="toast toast-success" aria-live="polite" style="display:none; opacity: 1">
            <div class="toast-message success-msg"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message">
        <div class="toast toast-error" aria-live="assertive" style="display:none;">
            <div class="toast-message error-msg"></div>
        </div>
    </div>

    <div class="modal fade" id="resetAllChanges" tabindex="-1" aria-labelledby="resetAllChangesLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetAllChangesLabel">{{ __('Rolling back all changes') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ __('You can roll back the scan results to the initial state.') }}
                    <br>
                    <br>
                    <span class="text-danger">{{ __('This action cannot be undone.') }}</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-secondary"
                            id="confirmResetChanges">{{ __('Roll back changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="groupModalLabel">{{ __('Download results') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="sortContainer" class="col-12">
                        {{ __('Ready to download') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-secondary download-file" data-action="xls"
                            data-dismiss="modal">{{ __('Download xls') }}</button>
                    <button type="button" class="btn btn-secondary download-file" data-action="csv"
                            data-dismiss="modal">{{ __('Download csv') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Moving a phrase') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="your-phrase">{{ __('Movable phrase') }}</label>
                    <input type="text" name="your-phrase" id="your-phrase" class="form form-control" disabled>

                    <label for="clusters-list"></label>
                    <select name="clusters-list" id="clusters-list" class="custom-select">
                        @foreach($clusters as $mainPhrase => $items)
                            @if(count($items) === 2)
                                @continue
                            @endif
                            <option value="{{ $mainPhrase }}">{{ $mainPhrase }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-secondary" id="save-changes" data-dismiss="modal">
                        {{ __('Edit') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addNewGroup" tabindex="-1" aria-labelledby="addNewGroupLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewGroupLabel">{{ __('Adding a new group') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="name-new-group">{{ __('Name of the new group') }}</label>
                    <input type="text" name="name-new-group" id="name-new-group" class="form form-control">
                    <span class="text-muted">
                        {{ __('The name must be unique') }}
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-secondary" id="add-new-group"
                            data-dismiss="modal">{{ __('Add') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="setRelevanceLink" tabindex="-1" aria-labelledby="setRelevanceLinkLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setRelevanceLinkLabel">{{ __('Set relevant page') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ __('Selecting a relevant page') }}
                    <select class="custom-select" name="relevanceSelect" id="relevanceSelect"></select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            id="setRelevanceUrls">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('download.cluster.group') }}" method="POST" style="display: none">
        @csrf
        <input type="text" name="type" id="fileType">
        <input type="text" name="json" id="json">
        <input type="number" name="id" value="{{ $cluster['id'] }}">
        <input type="submit" id="sendForm">
    </form>

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
                <li class="nav-item">
                    <a class="nav-link admin-link" href="{{ route('show.cluster.result', $cluster['id']) }}">
                        {{ __('My project') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link active" href="{{ route('edit.clusters', $cluster['id']) }}">
                        {{ __('Hands editor') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link" href="#" data-toggle="modal" data-target="#groupModal">
                        {{ __('Download file') }}
                    </a>
                </li>
                @if($admin)
                    <li>
                        <a class="nav-link admin-link" href="{{ route('cluster.configuration') }}">
                            {{ __('Module administration') }}
                        </a>
                    </li>
                @endif
                @isset($cluster['default_result'])
                    <li>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#resetAllChanges">
                            {{ __('Rolling back all changes') }}
                        </button>
                    </li>
                @endisset
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div id="params">
                        <div class="d-flex w-100 justify-content-between" style="margin-top: 40px;">
                            <div>
                                {{ __('Number of phrases') }}: {{ $cluster['count_phrases'] }}
                            </div>
                            <div>
                                {{ __('Number of clusters') }}: <span
                                    id="countClusters">{{ $cluster['count_clusters'] }}</span>
                            </div>
                            <div>
                                {{ __('Phrases') }}:
                                <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-paperclip"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 450px !important;"
                                              id="all-phrases">
                                            @foreach(explode("\n", $cluster['request']['phrases']) as $phrase)
                                                {{ $phrase }}<br>
                                            @endforeach
                                        </span>
                                    </span>
                                </span>
                                <i class="fa fa-copy" id="copyUsedPhrases"></i>
                                <textarea name="usedPhrases" id="usedPhrases"
                                          style="display: none"></textarea>
                            </div>
                            <div>
                                {{ __('Region') }}: {{ \App\Common::getRegionName($cluster['request']['region']) }}
                            </div>
                            <div>
                                {{ __('Search Engine') }}: {{ $cluster['request']['searchEngine'] ?? 'yandex' }}
                            </div>
                            <div>
                                {{ __('Top') }}: {{ $cluster['request']['count'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <ol class="col-6" id="clusters-block">
                        @if(isset($html))
                            {!! $html !!}
                        @else
                            @foreach($clusters as $mainPhrase => $items)
                                @if(count($items) <= 2)
                                    @continue
                                @endif
                                @php($hash = preg_replace("/[0-9]/", "", Str::random()))
                                <li class="cluster-block" id="{{ str_replace(' ', '_', $mainPhrase) }}">
                                    <div class="card-header" style="background-color: #343a40; color: white">
                                        <div class="d-flex justify-content-between text-white">
                                            <span class="w-50">
                                                {{ $mainPhrase }}
                                            </span>
                                            <span></span>
                                            <span></span>
                                            <div class="btn-group btn-group-toggle w-75" style="display: none">
                                                <input type="text" value="{{ $mainPhrase }}"
                                                       class="form form-control group-name-input"
                                                       data-target="{{ $mainPhrase }}">
                                                <button class="btn btn-secondary edit-group-name">
                                                    {{ __('Edit') }}
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                            <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-eye mr-2" style="color: white" data-action="hide">
                                                </i>
                                                <span class="ui_tooltip __bottom">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('Hide a group') }}
                                                    </span>
                                                </span>
                                            </span>
                                                <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-edit change-group-name mr-2"
                                                   style="color: white; padding-top: 5px"></i>
                                                <span class="ui_tooltip __bottom">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('Change the name') }}
                                                    </span>
                                                </span>
                                            </span>
                                                <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-check select-group mr-2"
                                                   style="color: white; padding-top: 5px"></i>
                                                <span class="ui_tooltip __bottom">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('Select a group to move phrases quickly') }}
                                                    </span>
                                                </span>
                                            </span>
                                                <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-arrow-right move-group mr-2"
                                                   style="color: white; padding-top: 5px"></i>
                                                <span class="ui_tooltip __bottom">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('Move the entire group') }}
                                                    </span>
                                                </span>
                                            </span>
                                            </div>
                                            <span class="__helper-link ui_tooltip_w hide set-relevance-link">
                                            <button class="btn btn-secondary" style="border-radius: 0 !important;"
                                                    data-toggle="modal" data-target="#setRelevanceLink">
                                                <i class="fa fa-save" style="color: white;"></i>
                                            </button>
                                            <span class="ui_tooltip __bottom">
                                                <span class="ui_tooltip_content">
                                                    {{ __('Select one url for the entire group of phrases') }}
                                                </span>
                                            </span>
                                        </span>
                                        </div>
                                    </div>
                                    <ol id="{{ $hash }}" class="list-group list-group-flush show">
                                        @foreach($items as $phrase => $item)
                                            @if($phrase === 'finallyResult')
                                                @continue
                                            @endif
                                            <div class="list-group-item" data-target="{{ $phrase }}"
                                                 data-action="{{ $mainPhrase }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="phrase-for-color"
                                                         style="width: 370px">{{ $phrase }}</div>
                                                    <span class="relevance-link hide">
                                                    {!! \App\Cluster::getRelevanceLink($item) !!}
                                                    </span>
                                                    <div style="display: none">@if(isset($item['similarities']))
                                                            {{ implode("\n", array_keys($item['similarities'])) }}
                                                        @endif</div>
                                                    <div>
                                                    <span class="__helper-link ui_tooltip_w frequency">
                                                        <span>{{ $item['based']['number'] ?? $item['based'] }}</span> /
                                                        <span>{{ $item['phrased']['number'] ?? $item['phrased'] }}</span> /
                                                        <span>{{ $item['target']['number'] ?? $item['target'] }}</span>
                                                        <span class="ui_tooltip __bottom">
                                                            <span class="ui_tooltip_content">
                                                                <span>{{ __('Base') }}</span> /
                                                                <span>{{ __('Phrasal') }}</span> /
                                                                <span>{{ __('Target') }}</span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                    </div>
                                                    <div class="btn-group">
                                                        <i class="fa fa-ellipsis mr-2"
                                                           data-toggle="dropdown"
                                                           aria-haspopup="true"
                                                           aria-expanded="false"></i>
                                                        <div class="dropdown-menu">
                                                            <button data-toggle="modal" data-target="#exampleModal"
                                                                    class="dropdown-item add-to-another"
                                                                    data-action="{{ $phrase }}">
                                                                {{ __("Add a phrase to another cluster") }}
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item select-for-analyse">
                                                                {{ __('Select phase for analyse') }}
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item color-phrases">
                                                                {{ __('Highlight similar phrases') }}
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item set-default-colors">
                                                                {{ __('Cancel selection') }}
                                                            </button>
                                                        </div>
                                                        <i class="fa fa-arrow-right move-phrase"
                                                           data-target="{{ $phrase }}"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </ol>
                                </li>
                            @endforeach
                            <li class="cluster-block" id="{{ __('unallocated_words') }}">
                                <div class="card-header" style="background-color: #343a40; color: white">
                                    <div class="d-flex justify-content-between text-white">
                                        <span class="w-50">{{ __('Unallocated words') }}</span>
                                        <span></span>
                                        <span></span>
                                        <div class="d-flex justify-content-between">
                                            <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-eye mr-2 alone-eye" data-action="hide"
                                                   style="color: white">
                                                </i>
                                                <span class="ui_tooltip __bottom">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('Hide a group') }}
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-arrow-right move-group"
                                                   style="color: white; padding-top: 5px"></i>
                                                <span class="ui_tooltip __bottom">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('Move the entire group') }}
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-arrow-right move-group mr-2"
                                                   style="color: white; padding-top: 5px"></i>
                                                <span class="ui_tooltip __bottom">
                                                    <span class="ui_tooltip_content">
                                                        {{ __('Move the entire group') }}
                                                    </span>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <ol class="list-group list-group-flush show" id="alone_phrases">
                                    @foreach($clusters as $mainPhrase => $items)
                                        @if(count($items) != 2)
                                            @continue
                                        @endif
                                        @php($hash = preg_replace("/[0-9]/", "", Str::random()))
                                        @foreach($items as $phrase => $item)
                                            @if($phrase === 'finallyResult')
                                                @continue
                                            @endif
                                            <div class="list-group-item" data-target="{{ $phrase }}"
                                                 data-action="alone">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="phrase-for-color" style="width: 440px">
                                                        {{ $phrase }}
                                                    </div>
                                                    <span class="relevance-link hide">
                                                        {!! \App\Cluster::getRelevanceLink($item) !!}
                                                    </span>
                                                    @if(isset($item['similarities']))
                                                        <div
                                                            style="display: none">{{ implode("\n", array_keys($item['similarities'])) }}</div>
                                                    @else
                                                        <div></div>
                                                    @endif
                                                    <div>
                                                    <span class="__helper-link ui_tooltip_w frequency">
                                                        <span>{{ $item['based']['number'] ?? $item['based'] }}</span> /
                                                        <span>{{ $item['phrased']['number'] ?? $item['phrased'] }}</span> /
                                                        <span>{{ $item['target']['number'] ?? $item['target'] }}</span>
                                                        <span class="ui_tooltip __bottom">
                                                            <span class="ui_tooltip_content">
                                                                <span>{{ __('Base') }}</span> /
                                                                <span>{{ __('Phrasal') }}</span> /
                                                                <span>{{ __('Target') }}</span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                    </div>
                                                    <div class="btn-group">
                                                        <i class="fa fa-ellipsis mr-2"
                                                           data-toggle="dropdown"
                                                           aria-haspopup="true"
                                                           aria-expanded="false"></i>
                                                        <div class="dropdown-menu">
                                                            <button data-toggle="modal" data-target="#exampleModal"
                                                                    class="dropdown-item add-to-another"
                                                                    data-action="{{ $phrase }}">
                                                                {{ __('Add a phrase to another cluster') }}
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item select-for-analyse">
                                                                {{ __("Select phase for analyse") }}
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item color-phrases">
                                                                {{ __('Highlight similar phrases') }}
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item set-default-colors">
                                                                {{ __('Cancel selection') }}
                                                            </button>
                                                        </div>
                                                        <i class="fa fa-arrow-right move-phrase"
                                                           data-target="{{ $phrase }}"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </ol>
                            </li>
                        @endif
                    </ol>
                    <div class="col-6">
                        <div class="work-place-conf">
                            <div class="card collapsed-card" style="box-shadow: none">
                                <div class="card-header shadow-none border-0">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title mr-2">{{ __('Phrases for analysis') }}</h3>
                                        <button type="button" data-card-widget="collapse" class="btn btn-tool">
                                            <i class="fas fa-plus" id="selected-phrases-i"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="display: none;" id="selected-phrases-block">
                                    <textarea class="form form-control" name="selected-phrases" id="selected-phrases"
                                              cols="8" rows="8"></textarea>
                                    <div class="d-flex justify-content-end mt-3">
                                        <div class="btn-group mr-2">
                                            <button class="btn btn-outline-secondary" id="go-to-competitors-analyse">
                                                {{ __('Analyse phrases') }}
                                            </button>
                                            <button class="btn btn-secondary">
                                                <span class="__helper-link ui_tooltip_w">
                                                    <i class="fa fa-question-circle" style="color: white"></i>
                                                    <span class="ui_tooltip __bottom">
                                                        <span class="ui_tooltip_content">
                                                            {{ __('You will be redirected to the "Competitor Analysis"') }} <br>
                                                            {{ __('The selected phrases will be filled in automatically.') }}
                                                        </span>
                                                    </span>
                                                </span>
                                            </button>
                                        </div>
                                        <button class="btn btn-outline-secondary" id="copy-selected-phrases">
                                            {{ __('Copy phrases') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="switch-container mb-3 d-flex btn-group">
                                <button id="change-sortable" class="btn btn-outline-secondary w-25"
                                        data-action="enable">
                                    {{ __('Moving groups') }}
                                </button>
                                <button id="relevance" class="btn btn-outline-secondary w-25" data-action="show">
                                    {{ __('Show relevant') }}
                                </button>
                                <button class="hide-or-show btn btn-outline-secondary w-50 w-50"
                                        data-action="hide"
                                        style="float: right">
                                    {{ __('Close groups') }}
                                </button>
                            </div>
                            <div class="btn-group w-100 mb-2">
                                <input type="text" id="clusterFilter" class="form form-control"
                                       placeholder="{{ __('Search') }}">
                                <button class="btn btn-outline-secondary" id="searchPhrases">
                                    {{ __('Search') }}
                                </button>
                                <button class="btn btn-outline-secondary" id="setDefaultVision">
                                    {{ __('Reset') }}
                                </button>
                            </div>
                            <div class="card-header d-flex justify-content-between" id="workPlace"
                                 style="background-color: #343a40; color: white">
                                {{ __('Workspace') }}
                            </div>
                            <ul class="list-group list-group-flush" id="work-place-ul"></ul>
                            <div>
                                <div id="addNewGroupButton">
                                    <button class="btn btn-outline-secondary w-100 radius" style="float: left"
                                            data-toggle="modal" data-target="#addNewGroup">
                                        {{ __('Add new group') }}
                                    </button>
                                </div>
                                <div class="btn-group w-100" style="display: none" id="actionsButton">
                                    <button class="btn btn-outline-primary w-50 radius" id="saveChanges" disabled>
                                        {{ __('Save changes') }}
                                    </button>
                                    <button class="btn btn-outline-danger w-50 radius" id="resetChanges" disabled>
                                        {{ __('Reset changes') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/sortable/sortable.min.js') }}"></script>
        <script>
            $('#app > div > div > div.card-header').append($('#params').html())
            $('#params').remove()

            if ($('#alone_phrases').length && $('#alone_phrases').html().trim() === '') {
                $('#alone_phrases').parent().remove()
            }
            $(document).ready(function () {
                recalculateFrequency()
                $('#нераспределённые_слова > div > div > div.d-flex.justify-content-between > span:nth-child(2)').remove()
                $('#unallocated_words > div > div > div.d-flex.justify-content-between > span:nth-child(2)').remove()
                $('#нераспределённые_слова > div > div > div.d-flex.justify-content-between > span:nth-child(1)').remove()
                $('#unallocated_words > div > div > div.d-flex.justify-content-between > span:nth-child(1)').remove()
            })

            let worPlaceCreated = false
            let selectedGroup = false

            let swapMainPhrase = ''
            let swapObject = ''
            let group

            $(document).keypress(function (e) {
                if (e.which === 13 && $('#clusterFilter').is(':focus')) {
                    searchPhrases()
                }
            });

            $('#copy-selected-phrases').on('click', function () {
                if ($('#selected-phrases').val().trim() !== '') {
                    let text = $('#selected-phrases');
                    text.select();
                    document.execCommand("copy");

                    successMessage("{{ __('Phrases copied to the clipboard') }}", 3000)
                }

            });

            $('#go-to-competitors-analyse').on('click', function () {
                let phrases = $('#selected-phrases').val().trim()

                if (phrases !== '') {
                    localStorage.setItem('lk_redbox_phrases_for_analyse', phrases)
                    window.open("{{ route('competitor.analysis') }}", '_blank');
                }
            })

            $('#copyUsedPhrases').click(function () {
                let object = $('#usedPhrases')
                if (object.html() === '') {
                    $.ajax({
                        type: "POST",
                        url: "/download-cluster-phrases",
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            projectId: {{ $cluster['id'] }},
                        },
                        success: function (response) {
                            let phrases = response.phrases
                            object.html(' ')
                            object.html(phrases.join("\n"))
                            object.css('display', 'block')
                            let text = document.getElementById("usedPhrases");
                            text.select();
                            document.execCommand("copy");
                            object.css('display', 'none')
                            successMessage()
                        },
                        error: function (response) {
                        }
                    });
                } else {
                    object.css('display', 'block')
                    let text = document.getElementById("usedPhrases");
                    text.select();
                    document.execCommand("copy");
                    object.css('display', 'none')
                    successMessage()
                }
            })

            $('#add-new-group').on('click', function () {
                let groupName = $('#name-new-group').val()
                if (groupName !== '') {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('check.group.name') }}",
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: {{ $cluster['id'] }},
                            groupName: groupName,
                        },
                        success: function (response) {
                            $('#workPlace').html(
                                '<div class="btn-group btn-group-toggle w-75" id="editWorkPlaceBlock" style="display: none">' +
                                '   <input class="form form-control" id="editWorkPlaceName" value="' + groupName + '">' +
                                '   <button class="btn btn-secondary" id="editWorkPlaceButton">{{ __('Change') }}</button>' +
                                '</div>' +
                                '<span id="groupName">' + groupName + '</span>' +
                                '<i class="fa fa-edit" style="color: white" id="editWorkPlace"></i>'
                            )
                            $('#addNewGroupButton').hide()
                            $('#actionsButton').show()
                            worPlaceCreated = true
                            $('#change-sortable').attr('disabled', 'disabled')
                            refreshMethods()

                            let res = response.result
                            if (res.error === false) {
                                $.each($('#alone_phrases').children('li'), function () {
                                    if ($(this).attr('data-target') === groupName) {
                                        successMessage(res.message, 5000)
                                        $(this).children('div').eq(0).children('div').eq(3).children('i').eq(1).trigger('click')
                                        return;
                                    }
                                })
                            }
                        },
                        error: function (response) {
                            errorMessage(response.responseJSON.message)
                        }
                    });
                } else {
                    errorMessage("{{ __('The group name cannot be empty and contain numbers') }}")
                }
            })

            $('#save-changes').unbind().on('click', function () {
                let clusterPhrase = $('#clusters-list').val();
                let phrase = $('#your-phrase').val()

                if (clusterPhrase === '' || clusterPhrase === swapMainPhrase) {
                    return;
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('edit.cluster') }}",
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: {{ $cluster['id'] }},
                            mainPhrase: clusterPhrase,
                            phrase: phrase,
                        },
                        success: function (response) {
                            $('#' + clusterPhrase.replaceAll(' ', '_')).children('ol').eq(0).append(
                                '<div data-target="' + phrase + '" data-action="' + clusterPhrase + '" class="list-group-item">' +
                                '    <div class="d-flex justify-content-between">' +
                                '        <div class="phrase-for-color" style="width: 440px">' + phrase + '</div>' +
                                '        <div style="display: none">similarities</div>' +
                                '        <div>' +
                                '            <span class="__helper-link ui_tooltip_w">' +
                                '                        <span>' + response.based + '</span> /' +
                                '                        <span>' + response.phrased + '</span> /' +
                                '                        <span>' + response.target + '</span>' +
                                '                    <span class="ui_tooltip __bottom">' +
                                '                    <span class="ui_tooltip_content">' +
                                '                        <span>' + "{{ __('Base') }}" + '</span> /' +
                                '                        <span>' + "{{ __('Phrasal') }}" + '</span> /' +
                                '                        <span>' + "{{ __('Target') }}" + '</span>' +
                                '                    </span>' +
                                '                </span>' +
                                '            </span>' +
                                '        </div>' +
                                '        <div class="btn-group">' +
                                '            <i data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="fa fa-ellipsis mr-2"></i> ' +
                                '            <div class="dropdown-menu">' +
                                '                <button data-toggle="modal" data-target="#exampleModal"' +
                                '                        class="dropdown-item add-to-another"' +
                                '                        data-action="' + phrase + '">' + "{{ __('Add a phrase to another cluster') }}" +
                                '                </button>' +
                                '                <button data-toggle="modal" class="dropdown-item select-for-analyse">' + "{{ __("Select phase for analyse") }}" + '</button>' +
                                '                <button data-toggle="modal" class="dropdown-item color-phrases">' + "{{ __('Highlight similar phrases') }}" +
                                '                </button>' +
                                '                <button data-toggle="modal" class="dropdown-item set-default-colors">' + "{{ __('Cancel selection') }}" +
                                '                </button>' +
                                '            </div>' +
                                '            <i data-target="' + phrase + '" class="fa fa-arrow-right move-phrase"></i>' +
                                '        </div>' +
                                '    </span>' +
                                '</div>')
                            swapObject.remove()

                            $('.add-to-another').unbind('click').on('click', function () {
                                $('#your-phrase').val($(this).attr('data-action'))
                                swapObject = $(this).parent().parent().parent().parent()
                                swapMainPhrase = String(swapObject.parent().attr('id')).replaceAll('_', ' ')
                            })

                            refreshMethods()

                            recalculateFrequency()

                            saveClusters("{{ __('Successfully') }}")
                        },
                    });
                }
            })


            $('#searchPhrases').on('click', function () {
                searchPhrases()
            })

            $('#setDefaultVision').on('click', function () {
                $('.phrase-for-color').parent().parent().show()
                $('.cluster-block').show()
                $('#clusterFilter').val('')
            })

            $('#confirmResetChanges').on('click', function () {
                $.ajax({
                    type: "POST",
                    url: "{{ route('reset.all.cluster.changes') }}",
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        projectId: {{ $cluster['id'] }},
                    },
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        errorMessage("{{ __("You don't have rights") }}")
                    }
                });
            })

            $('.hide-or-show').on('click', function () {
                if ($(this).attr('data-action') === 'hide') {
                    $.each($('.fa.fa-eye.mr-2'), function () {
                        if ($(this).attr('data-action') === 'hide') {
                            $(this).trigger('click')
                        }
                    })
                    $('.hide-or-show').attr('data-action', 'show')
                    $('.hide-or-show').html("{{ __('Reveal groups') }}")
                } else {
                    $.each($('.fa.fa-eye.mr-2'), function () {
                        if ($(this).attr('data-action') === 'show') {
                            $(this).trigger('click')
                        }
                    })
                    $('.hide-or-show').attr('data-action', 'hide')
                    $('.hide-or-show').html("{{ __('Close groups') }}")
                }
            })

            $('.download-file').on('click', function () {
                let scan = scanTree($('#clusters-block').children());
                let type = $(this).attr('data-action')
                $('#fileType').val(type)
                $('#json').val(JSON.stringify(scan))

                $('#sendForm').trigger('click')
            })

            let oldC;
            $('#clusters-block').sortable({
                afterMove: function (placeholder, container) {
                    if (oldC != container) {
                        if (oldC)
                            oldC.el.removeClass("active");
                        container.el.addClass("active");

                        oldC = container;
                    }
                },
                onDrop: function ($item, container, _super) {
                    container.el.removeClass("active");
                    _super($item, container);
                    saveClusters("{{ __('Successfully') }}")
                },
                onMouseDown: function ($item, _super, event) {
                    console.log(322)
                }
            })

            $('#clusters-block').sortable('disable')

            $('#change-sortable').on('click', function () {
                if (selectedGroup !== false) {
                    errorMessage("{{ __("You cannot enable group movement mode if you have a selected group") }}")
                    return;
                }
                $('ol#clusters-block').sortable($(this).attr('data-action'));
                let place = $('.work-place-conf')

                if ($(this).attr('data-action') === 'disable') {
                    $('ol#clusters-block').sortable('disable');

                    $(this).attr('data-action', 'enable')
                    $(this).html("{{ __('Moving groups') }}")

                    $('.fa.fa-arrow-right.move-group').show()
                    $('.fa.fa-edit.change-group-name.mr-2').show()
                    $('.fa.fa-ellipsis.mr-2').children('div').eq(0).children('button').eq(0).show()
                    $('.fa.fa-check.select-group.mr-2').parent().show()
                    place.children('div').eq(2).removeClass('hide')
                    place.children('div').eq(3).show()
                    $('#relevance').removeAttr('disabled')
                } else {
                    if (worPlaceCreated) {
                        errorMessage("{{ __('The workspace should be empty') }}")
                    } else {
                        $(this).attr('data-action', 'disable')
                        $(this).html("{{ __('Moving phrases') }}")

                        $('.fa.fa-arrow-right.move-group').hide()
                        $('.fa.fa-edit.change-group-name.mr-2').hide()
                        $('.fa.fa-ellipsis.mr-2').children('div').eq(0).children('button').eq(0).hide()
                        $('.fa.fa-check.select-group.mr-2').parent().hide()
                        place.children('div').eq(2).addClass('hide')
                        place.children('div').eq(3).hide()
                        $('#relevance').attr('disabled', 'disabled')
                    }
                }
            })

            $('#relevance').on('click', function () {
                if (worPlaceCreated) {
                    errorMessage("{{ __('The workspace should be empty') }}")
                    return;
                }
                if ($(this).attr('data-action') === 'show') {
                    $('.relevance-link').removeClass('hide')
                    $(this).attr('data-action', 'hide')
                    $(this).attr('class', 'btn btn-outline-secondary w-50')
                    $(this).html("{{ __('Hide relevant') }}")

                    $('.work-place-conf').parent().attr('class', 'col-4')
                    $('#clusters-block').attr('class', 'col-8')
                    $('#workPlace').addClass('hide')
                    $('#addNewGroupButton').addClass('hide')
                    $('#change-sortable').addClass('hide')
                    $('.frequency').addClass('hide')
                    $('.cluster-block').find('.btn-group').addClass('hide')
                    $('.cluster-block .card-header div div.d-flex.justify-content-between').addClass('hide')
                    $('.set-relevance-link').removeClass('hide')
                    $('.relevance-link').css({
                        'width': 'auto'
                    })

                } else {
                    $('.relevance-link').addClass('hide')
                    $(this).attr('data-action', 'show')
                    $(this).html("{{ __('Show relevant') }}")
                    $(this).attr('class', 'btn btn-outline-secondary w-25')

                    $('.work-place-conf').parent().show()
                    $('.cluster-block').find('.btn-group').removeClass('hide')
                    $('#clusters-block').attr('class', 'col-6')
                    $('.work-place-conf').parent().attr('class', 'col-6')
                    $('#workPlace').removeClass('hide')
                    $('#addNewGroupButton').removeClass('hide')
                    $('#change-sortable').removeClass('hide')
                    $('.frequency').removeClass('hide')
                    $('.cluster-block .card-header div div.d-flex.justify-content-between').removeClass('hide')
                    $('.set-relevance-link').addClass('hide')

                    $('.relevance-link').css({
                        'width': '250px'
                    })
                }
            })

            $('.save-relevance-url').unbind().on('click', function () {
                let select = $(this).parent().children('select').eq(0)
                let phrase = $(this).parent().parent().parent().children('div').eq(0).html().trim()

                $.ajax({
                    type: "POST",
                    url: "{{ route('set.cluster.relevance.url') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        phrase: phrase,
                        url: select.val(),
                        projectId: {{ $cluster['id'] }},
                        type: 'notDefault'
                    },
                    success: function () {
                        select.parent().parent().html('<a href="' + select.val() + '" target="_blank">' + select.val() + '</a>')
                        saveClusters("{{ __('Successfully') }}")
                    },
                    error: function (response) {
                    }
                });
            })

            let divs
            let targetButton
            $('.set-relevance-link').unbind().on('click', function () {
                targetButton = $(this)
                $('#relevanceSelect').html('')
                divs = targetButton.parents().eq(2).children('ol').eq(0).children('div')
                let array = []
                $.each(divs, function () {
                    $.each($(this).children('div').eq(0).find('select').children('option'), function () {
                        array.push($(this).val())
                    })
                })

                let uniqueLinks = new Set([...array])

                for (let value of uniqueLinks) {
                    $('#relevanceSelect').append('<option value="' + value + '">' + value + '</option>')
                }
            })

            $('#setRelevanceUrls').on('click', function () {
                let phrases = []
                $.each(divs, function () {
                    if ($(this).children('div').eq(0).find('select').length) {
                        phrases.push($(this).children('div').eq(0).find('.phrase-for-color').html())
                    }
                })
                $.ajax({
                    type: "POST",
                    url: "{{ route('set.cluster.relevance.urls') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        phrases: phrases,
                        url: $('#relevanceSelect').val(),
                        projectId: {{ $cluster['id'] }},
                        type: 'notDefault'
                    },
                    success: function () {
                        $.each(divs, function () {
                            if ($(this).children('div').eq(0).find('select').length) {
                                $(this).children('div').eq(0).find('select').parent().parent().html('<a href="' + $('#relevanceSelect').val() + '" target="_blank">' + $('#relevanceSelect').val() + '</a>')
                            }
                        })

                        targetButton.remove()
                        saveClusters()
                    },
                });
            })

            function saveClusters(message, interval = 3000) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('save.clusters.tree') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        projectId: {{ $cluster['id'] }},
                        html: JSON.stringify(scanTree($('#clusters-block').children()))
                    },
                    success: function () {
                        successMessage(message, interval)
                    }
                });
            }

            function moveSelectedPhrase(selectedGroup, element, phrase) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('edit.cluster') }}",
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: {{ $cluster['id'] }},
                        mainPhrase: selectedGroup.replaceAll('_', ' '),
                        phrase: phrase,
                    },
                    success: function () {
                        $('#' + selectedGroup).children('ol').eq(0).prepend(element)
                        element.attr('data-action', selectedGroup.replaceAll('_', ' '))
                        element.show()
                        refreshMethods()
                        recalculateFrequency()
                    }
                });
            }

            function moveNewGroup(phrases, newGroupName, newId) {
                let newUl = '<ol id="' + newId + '" class="list-group list-group-flush show">'

                $.each(phrases, function (key, value) {
                    newUl += '<div data-target="' + value + '" data-action="' + newGroupName + '" class="list-group-item">' + $("div[data-target='" + value + "']").html() + '</div>'
                    $("div[data-target='" + value + "']").remove()
                })

                if ($('#' + newGroupName.replaceAll(' ', '_')).length) {
                    $.each($('#' + newGroupName.replaceAll(' ', '_')).children('ol').eq(0).children('div'), function (key, value) {
                        if ($(this).is(':visible')) {
                            $(this).attr('data-action', newGroupName)
                            newUl += '<li data-target="' + value + '" data-action="' + newGroupName + '" class="list-group-item">' + $(this).html() + '</li>'
                            $(this).remove()
                        }
                    })
                }

                newUl += '</ol>'

                $('#clusters-block').prepend(
                    '<li id="' + newGroupName.replaceAll(' ', '_') + '" class="cluster-block">' +
                    '    <div class="card-header" style="background-color: rgb(52, 58, 64); color: white;">' +
                    '        <div class="d-flex justify-content-between text-white">' +
                    '            <span class="w-50">' + newGroupName + '</span>' +
                    '            <span>кол-во фраз: ' + phrases.length + '</span>' +
                    '            <span>0 / 0 / 0</span> ' +
                    '            <div class="btn-group btn-group-toggle w-75" style="display: none;">' +
                    '                <input type="text" value="' + newGroupName + '" data-target="' + newGroupName + '" class="form form-control group-name-input">' +
                    '                    <button class="btn btn-secondary edit-group-name">{{ __("Change") }}</button>' +
                    '                </div> ' +
                    '            <div class="d-flex justify-content-between">' +
                    '                <span class="__helper-link ui_tooltip_w">' +
                    '                    <i class="fa fa-eye mr-2" style="color: white;" data-action="hide"></i> ' +
                    '                    <span class="ui_tooltip __bottom">' +
                    '                        <span class="ui_tooltip_content">{{ __("Hide a group") }}</span>' +
                    '                    </span>' +
                    '                </span> ' +
                    '                <span class="__helper-link ui_tooltip_w">' +
                    '                    <i class="fa fa-edit change-group-name mr-2" style="color: white; padding-top: 5px;"></i>' +
                    '                    <span class="ui_tooltip __bottom">' +
                    '                        <span class="ui_tooltip_content">' +
                    '                            {{ __('Change the name') }}' +
                    '                        </span>' +
                    '                    </span>' +
                    '                </span> ' +
                    '                <span class="__helper-link ui_tooltip_w">' +
                    '                    <i class="fa fa-check select-group mr-2"' +
                    '                       style="color: white; padding-top: 5px"></i>' +
                    '                    <span class="ui_tooltip __bottom">' +
                    '                        <span class="ui_tooltip_content">' +
                    '                            {{ __('Select group') }}' +
                    '                        </span>' +
                    '                    </span>' +
                    '                </span>' +
                    '                <span class="__helper-link ui_tooltip_w">' +
                    '                    <i class="fa fa-arrow-right move-group" style="color: white; padding-top: 5px;"></i> ' +
                    '                    <span class="ui_tooltip __bottom">' +
                    '                        <span class="ui_tooltip_content">' + "{{ __('Move the entire group') }}" +
                    '                        </span>' +
                    '                    </span>' +
                    '                </span>' +
                    '            </div>' +
                    '        </div>' +
                    '    </div>' +
                    newUl +
                    '</li>'
                );

                defaultWorkPlace()
            }

            function recalculateFrequency() {
                $.each($('.cluster-block'), function (key, value) {
                    let base = 0
                    let phrase = 0
                    let target = 0
                    $.each($(this).children('ol').eq(0).children('div'), function () {
                        let span = $(this).children('div').eq(0).children('div').eq(2).children('span').eq(0)
                        base += +span.children('span').eq(0).html()
                        phrase += +span.children('span').eq(1).html()
                        target += +span.children('span').eq(2).html()
                    })

                    let span = $(this).children('div').eq(0).children('div').eq(0).children('span')
                    span.eq(2).html(base + ' / ' + phrase + ' / ' + target)
                    span.eq(1).html("{{ __('number of phrases: ') }}" + $(this).children('ol').eq(0).children('div').length)
                })
            }

            function searchPhrases() {
                $('.phrase-for-color').parent().parent().show()
                $('.cluster-block').show()

                let searchSuccess = false
                let string = $('#clusterFilter').val().trim()
                let totalCount = 0

                if (string !== '') {
                    $.each($('.cluster-block'), function (key, value) {
                        let ol = $(this).children('ol').eq(0)
                        $.each($(ol.children('div')), function (key, value) {
                            if (!$(this).children('div').eq(0).children('div').eq(0).html().includes(string)) {
                                searchSuccess = true
                                totalCount += 1
                                $(this).hide()
                            }
                        })
                    })

                    if (!searchSuccess) {
                        errorMessage("{{ __('No matches found') }}")
                    } else {
                        successMessage(totalCount + " {{ __('elements hidden') }}")
                    }
                } else {
                    errorMessage("{{ __('The search field is empty') }}")
                }
            }

            function scanTree(elems) {
                let result = {};

                $.each(elems, function () {
                    let $id = $(this).attr('id').replaceAll('_', ' ')
                    let children = $(this).children('ol').eq(0).children()
                    let array = []
                    $.each(children, function () {
                        if ($(this).attr('data-target') !== undefined) {
                            array.push($(this).attr('data-target'))
                        } else {
                            array.push(scanTree($(this)))
                        }
                    })

                    result[$id] = array
                })

                return result;
            }

            function refreshMethods() {
                $('.move-phrase').unbind().on('click', function () {
                    $(this).parent().parent().parent().hide(300)

                    $('#work-place-ul').append(
                        '<li class="work-place-li" style="display: none" data-target="' + $(this).attr('data-target') + '">' +
                        '    <div style="float: left">' +
                        '        <i class="fa fa-arrow-left move-back mr-2" data-target="' + $(this).attr('data-target') + '"></i>' +
                        '        <i style="display: none" class="fa fa-down-left-and-up-right-to-center mr-2 move-to-selected-group render-merge-i" data-target="' + $(this).attr('data-target') + '"></i>' +
                        '        <i class="fa fa-brush" data-target="' + $(this).attr('data-target') + '"></i>' +
                        '    </div>' +
                        '    <div style="float: right"><div class="phrase-for-color">' + $(this).attr('data-target') + '</div></div>' +
                        '</li>'
                    )
                    $('.work-place-li').show(300)

                    $('.move-back').unbind('click').on('click', function () {
                        let search = $("ol").find(`[data-target='${$(this).attr('data-target')}']`);
                        $('.cluster-block').show()
                        search.show(300)

                        $(this).parent().parent().hide(300)
                        setTimeout(() => {
                            $(this).parent().parent().remove()

                            if ($('#work-place-ul').html().trim() === '') {
                                defaultWorkPlace()
                            }
                        }, 300)
                    })

                    $('.move-to-selected-group').unbind().on('click', function () {
                        if (selectedGroup !== false) {
                            let search = $("ol").find(`div[data-target='${$(this).attr('data-target')}']`);
                            let element = search.eq(0)
                            let phrase = element.attr('data-target')
                            moveSelectedPhrase(selectedGroup, element, phrase)
                            $(this).parent().parent().hide(300)
                            setTimeout(() => {
                                $(this).parent().parent().remove()
                                if ($('#work-place-ul').html().trim() === '') {
                                    defaultWorkPlace()
                                }
                            }, 300)

                        } else {
                            errorMessage("{{ __('First you need to select a group') }}")
                        }
                    })

                    $('.fa.fa-brush').unbind('click').on('click', function () {
                        let targetHtml = $("ol").find(`[data-target='${$(this).attr('data-target')}']`).eq(0).children('div').eq(0).children('div').eq(1).html();
                        if (targetHtml !== undefined) {
                            scanArray(targetHtml.split("\n"), $(this))
                        }
                    })

                    $('#saveChanges').prop('disabled', false)
                    $('#resetChanges').prop('disabled', false)

                    if (!worPlaceCreated) {
                        let id = "{{ __('New group') }}".replaceAll('_', ' ')

                        $('#workPlace').html(
                            '<div class="btn-group btn-group-toggle w-75" id="editWorkPlaceBlock" style="display: none">' +
                            '   <input class="form form-control" id="editWorkPlaceName" value="' + id + '">' +
                            '   <button class="btn btn-secondary" id="editWorkPlaceButton">{{ __('Change') }}</button>' +
                            '</div>' +
                            '<span id="groupName">' + id + '</span>' +
                            '<i class="fa fa-edit" style="color: white" id="editWorkPlace"></i>'
                        )

                        worPlaceCreated = true
                        $('#change-sortable').attr('disabled', 'disabled')
                        $('#addNewGroupButton').hide()
                        $('#actionsButton').show()
                        refreshMethods()
                    }
                })

                $('.change-group-name').unbind().on('click', function () {
                    let parent = $(this).parent().parent().parent()
                    parent.children('span').eq(0).hide()
                    parent.children('span').eq(1).hide()
                    parent.children('span').eq(2).hide()
                    parent.children('div').eq(0).show()
                })

                $('.edit-group-name').unbind().on('click', function () {
                    let span = $(this).parent().parent().children('span').eq(0)
                    let span1 = $(this).parent().parent().children('span').eq(1)
                    let span2 = $(this).parent().parent().children('span').eq(2)
                    let div = $(this).parent().parent().children('div').eq(0)
                    let newGroupName = $(this).parent().children('input').eq(0).val()
                    let oldGroupName = $(this).parent().children('input').eq(0).attr('data-target')

                    if (newGroupName === oldGroupName) {
                        span.show()
                        span1.show()
                        span2.show()
                        div.hide()
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('change.group.name') }}",
                            dataType: 'json',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                id: {{ $cluster['id'] }},
                                newGroupName: newGroupName,
                                oldGroupName: oldGroupName,
                            },
                            success: function (response) {
                                $("li[data-action='" + oldGroupName + "']").attr('data-action', newGroupName);
                                span.parent().parent().parent().attr('id', newGroupName.replaceAll(' ', '_'))
                                span.html(newGroupName)
                                span.show()
                                span1.show()
                                span2.show()
                                div.hide()
                                div.children('input').eq(0).attr('data-target', newGroupName)
                                if (response.move) {
                                    $.each($('#alone_phrases').children('li'), function () {
                                        let object = $(this)
                                        if (object.attr('data-target') === newGroupName) {
                                            object.attr('data-action', newGroupName)
                                            $('#' + newGroupName.replaceAll(' ', '_')).append(object)
                                            return;
                                        }
                                    })
                                }

                                refreshMethods()
                                saveClusters("{{ __('A phrase similar to the name of the group was automatically added to the group') }}", 5000)
                            },
                            error: function () {
                                errorMessage("{{ __('A group with the same name already exists or the name contains numbers') }}")
                            }
                        });
                    }
                })

                $('.color-phrases').unbind().on('click', function () {
                    let searchSuccess = false
                    $.each($('.phrase-for-color'), function (key, value) {
                        $(this).parent().parent().css({
                            'background-color': 'white'
                        })
                    })

                    let targetHtml = $(this).parent().parent().parent().children('div').eq(1).html().trim();
                    let array = targetHtml.split("\n");

                    $.each($('.phrase-for-color'), function (key, value) {
                        if (array.indexOf($(this).html()) != -1) {
                            searchSuccess = true
                            $(this).parent().parent().css({
                                'background-color': '#cbe0f5'
                            })
                        }
                    })

                    if (searchSuccess) {
                        $(this).parent().parent().parent().parent().css({
                            'background-color': '#59abfa'
                        })
                    } else {
                        errorMessage("{{ __('No matches found') }}")
                    }
                })

                $('.set-default-colors').unbind().on('click', function () {
                    $('.phrase-for-color').parent().parent().css({
                        'background-color': 'white'
                    })
                })

                $('#editWorkPlace').unbind().on('click', function () {
                    $('#editWorkPlaceBlock').show()
                    $(this).hide()
                    $('#groupName').hide()
                })

                $('#editWorkPlaceButton').unbind().on('click', function () {
                    let newName = $('#editWorkPlaceName').val().trim()

                    if (newName === '') {
                        errorMessage("{{ __('The group name cannot be empty') }}")
                    } else {
                        $('#groupName').html(newName)

                        $('#editWorkPlaceBlock').hide()
                        $('#editWorkPlace').show()
                        $('#groupName').show()
                    }
                })

                $('#saveChanges').unbind().on('click', function () {
                    let phrases = []
                    $.each($('#work-place-ul').children('li'), function (key, value) {
                        phrases.push($(this).attr('data-target'))
                    })

                    let newGroupName = $('#groupName').html();

                    $.ajax({
                        type: "POST",
                        url: "/confirmation-new-cluster",
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            projectId: {{ $cluster['id'] }},
                            mainPhrase: newGroupName,
                            phrases: phrases,
                        },
                        success: function (response) {
                            moveNewGroup(phrases, newGroupName, response.groupId)
                            recalculateFrequency()
                            saveClusters("{{ __('Successfully') }}")
                        },
                        error: function (response) {
                        }
                    });

                })

                $('#resetChanges').unbind().on('click', function () {
                    $.each($('#work-place-ul').children('li'), function (key, value) {
                        let target = $(this).attr('data-target')
                        $("div[data-target='" + target + "']").show(300)
                        $(this).remove()
                    })

                    defaultWorkPlace()
                })

                $('.move-group').unbind().on('click', function () {
                    group = $(this).parents().eq(4)

                    if (!worPlaceCreated) {
                        let id = group.attr('id').replaceAll('_', ' ')

                        $('#workPlace').html(
                            '<div class="btn-group btn-group-toggle w-75" id="editWorkPlaceBlock" style="display: none">' +
                            '   <input class="form form-control" id="editWorkPlaceName" value="' + id + '">' +
                            '   <button class="btn btn-secondary" id="editWorkPlaceButton">{{ __('Change') }}</button>' +
                            '</div>' +
                            '<span id="groupName">' + id + '</span>' +
                            '<i class="fa fa-edit" style="color: white" id="editWorkPlace"></i>'
                        )

                        worPlaceCreated = true
                        $('#change-sortable').attr('disabled', 'disabled')
                    }

                    $.each(group.children('ol').eq(0).children('div'), function () {
                        if ($(this).children('div').eq(0).children('div').eq(3).children('i').eq(1).is(':visible')) {
                            $(this).children('div').eq(0).children('div').eq(3).children('i').eq(1).trigger('click')
                        }
                    })

                    $('#addNewGroupButton').hide()
                    $('#actionsButton').show()
                    refreshMethods()
                })

                $.each($('.list-group.list-group-flush'), function (key, value) {
                    if ($(this).html().replaceAll(' ', '') === '' && $(this).parent().attr('id') !== undefined) {
                        let removePhrase = String($(this).parent().attr('id')).replaceAll('_', ' ')
                        $(this).parent().remove()
                        $("#clusters-list option[value='" + removePhrase + "']").remove()
                    }
                })

                $('.add-to-another').unbind().on('click', function () {
                    $('#your-phrase').val($(this).attr('data-action'))
                    swapObject = $(this).parent().parent().parent().parent()
                    swapMainPhrase = String(swapObject.parent().attr('id')).replaceAll('_', ' ')
                })

                $('.select-group').unbind().on('click', function () {
                    $('.fa-down-left-and-up-right-to-center.elem').remove()
                    $('.selected-group-list').removeClass('selected-group-list')

                    if ($(this).parent().parent().parent().parent().hasClass('selected-group')) {
                        selectedGroup = false

                        $(this).attr('class', 'fa fa-check select-group mr-2')
                        $('#change-sortable').removeAttr("disabled");
                        $('#relevance').removeAttr("disabled");
                        $('.selected-group').removeClass('selected-group')

                        $('#clusters-block div > div > div.btn-group').show()
                        $('.btn-group.btn-group-toggle.w-75').hide()
                        $('.work-place-li').unbind()
                        $('.render-merge-i').hide()
                        $('.move-back').show()
                        $('.move-group').show()
                        saveClusters("{{ __('Changes saved') }}")
                    } else {
                        selectedGroup = $(this).parent().parent().parent().parent().parent().attr('id')
                        $('#change-sortable').attr('disabled', 'disabled')
                        $('#relevance').attr('disabled', 'disabled')
                        $('.selected-group').removeClass('selected-group')
                        $('#clusters-block div > div > div.btn-group').hide()

                        $('.card-header .fa.fa-minus.select-group.mr-2').attr('class', 'fa fa-check select-group mr-2')
                        $(this).attr('class', 'fa fa-minus select-group mr-2')
                        $(this).parent().parent().parent().parent().addClass('selected-group')
                        $(this).parent().parent().parent().parent().parent().addClass('selected-group-list')

                        $('.list-group-item').children('div,div.btn-group').append('<i class="fa fa-down-left-and-up-right-to-center elem"></i>')
                        $('.render-merge-i').show()
                        $('.move-back').hide()
                        $('.move-group').hide()
                        $('.fa-down-left-and-up-right-to-center.elem').unbind().on('click', function () {
                            let element = $(this).parent().parent()
                            let phrase = element.attr('data-target')
                            moveSelectedPhrase(selectedGroup, element, phrase)
                        })
                    }
                })

                $('.fa-eye').unbind().on('click', function () {
                    if ($(this).attr('data-action') === 'hide') {
                        $(this).attr('data-action', 'show')
                        $.each($(this).parent().parent().parent().parent().parent().children('ol').eq(0).children('div'), function () {
                            $(this).addClass('hide')
                        })
                    } else {
                        $(this).attr('data-action', 'hide')
                        $.each($(this).parent().parent().parent().parent().parent().children('ol').eq(0).children('div'), function () {
                            $(this).removeClass('hide')
                        })
                    }
                })

                $('.select-for-analyse').unbind('click').on('click', function () {
                    let phrase = $(this).parent().parent().parent().children('div').eq(0).html().trim();
                    if (!$('#selected-phrases').val().includes(phrase)) {
                        $('#selected-phrases').val($('#selected-phrases').val() + phrase + "\n")
                    }

                    if ($('#selected-phrases-block').is(':visible') === false) {
                        $('#selected-phrases-i').trigger('click')
                    }
                })
            }

            function scanArray(array, object) {
                let searchSuccess = false

                $.each($('.phrase-for-color'), function (key, value) {
                    $(this).parent().parent().css({
                        'background-color': 'white'
                    })
                })

                $.each($('.phrase-for-color'), function (key, value) {
                    if (array.indexOf($(this).html()) != -1) {
                        searchSuccess = true
                        $(this).parent().parent().css({
                            'background-color': '#cbe0f5'
                        })
                    }
                })

                if (searchSuccess) {
                    object.parent().parent().css({
                        'background-color': '#59abfa'
                    })
                } else {
                    errorMessage("{{ __('No matches found') }}")
                }
            }

            function successMessage(message = "{{ __('Successfully') }}", timeout = 3000) {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html(message)
                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, timeout)
            }

            function errorMessage(message = "{{ __('Error') }}") {
                $('.toast.toast-error').show(300)
                $('.toast-message.error-msg').html(message)
                setTimeout(() => {
                    $('.toast.toast-error').hide(300)
                }, 5000)
            }

            refreshMethods()

            function defaultWorkPlace() {
                $('#workPlace').html("{{ __('Workspace') }}")
                $('#work-place-ul').html("")
                $('#addNewGroupButton').show()
                $('#actionsButton').hide()
                worPlaceCreated = false
                $('#change-sortable').removeAttr('disabled')
                refreshMethods()
            }
        </script>
    @endslot
@endcomponent
