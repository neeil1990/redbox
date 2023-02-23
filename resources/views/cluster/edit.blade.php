@php use Illuminate\Support\Str; @endphp
@component('component.card', ['title' =>  __('Editing Clusters') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <style>
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

            .emptySpace {
                background-color: rgba(52, 58, 64, 0.27);
                height: 40px;
                margin: 4px;
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
                padding-right: 20px;
            }


            ul .card-header {
                background-color: rgb(52, 58, 64);
                color: white;
            }

            .relevance-link {
                width: 250px;
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
                transition: .3s -webkit-line-clamp;
            }

            .relevance-link:hover {
                -webkit-line-clamp: 4;
            }

            .list-group-item {
                border: 1px solid rgba(128, 128, 128, 0.4) !important;
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

    <div class="modal fade" id="resetAllChanges" tabindex="-1" aria-labelledby="resetAllChangesLabel" aria-hidden="true">
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
                    {{ __('We apologize for the inconvenience.') }}
                    <br>
                    <br>
                    <span class="text-danger">{{ __('This action cannot be undone.') }}</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="resetAllChanges"
                            data-dismiss="modal">{{ __('Roll back changes') }}</button>
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
                                                {{ $phrase }} <br>
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
                        @if(isset($cluster['html']))
                            {!! $cluster['html'] !!}
                        @else
                            @foreach($clusters as $mainPhrase => $items)
                                @if(count($items) <= 2)
                                    @continue
                                @endif
                                @php($hash = preg_replace("/[0-9]/", "", Str::random()))
                                @php($base = 0)
                                @php($phrased = 0)
                                @php($target = 0)
                                @foreach($items as $phrase => $item)
                                    @if($phrase === 'finallyResult')
                                        @continue
                                    @endif
                                    @php($base += $item['based']['number'] ?? $item['based'])
                                    @php($phrased += $item['phrased']['number'] ?? $item['phrased'])
                                    @php($target += $item['target']['number'] ?? $item['target'])
                                @endforeach
                                <li class="cluster-block" id="{{ str_replace(' ', '_', $mainPhrase) }}">
                                    <div class="card-header" style="background-color: #343a40; color: white">
                                        <div class="d-flex justify-content-between text-white">
                                        <span class="w-50">
                                            {{ $mainPhrase }}
                                        </span>
                                            <span>кол-во фраз: {{ count($items) - 1 }}</span>
                                            <span>{{ $base }} / {{ $phrased }} / {{ $target }}</span>
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
                                                <i class="fa fa-eye mr-2"
                                                   data-toggle="collapse"
                                                   aria-expanded="false"
                                                   data-target="#{{ $hash }}"
                                                   aria-controls="{{ $hash }}"
                                                   style="color: white">
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
                                                <i class="fa fa-arrow-right move-group"
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
                                                    <span class="__helper-link ui_tooltip_w">
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
                                                                Добавить фразу к другому кластеру
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item color-phrases">
                                                                Подсветить похожие фразы
                                                            </button>
                                                            <button data-toggle="modal"
                                                                    class="dropdown-item set-default-colors">
                                                                Отменить выделение
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
                                        <span>{{ __('Unallocated words') }}</span>
                                        <div>
                                        <span class="__helper-link ui_tooltip_w">
                                            <i class="fa fa-eye mr-2 alone-eye"
                                               data-toggle="collapse"
                                               aria-expanded="false"
                                               data-target="#alone_phrases"
                                               aria-controls="alone_phrases"
                                               style="color: white">
                                            </i>
                                            <span class="ui_tooltip __bottom">
                                                <span class="ui_tooltip_content">
                                                    {{ __('Hide a group') }}
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
                                                    <div class="phrase-for-color"
                                                         style="width: 440px">{{ $phrase }}</div>
                                                    @if(isset($item['similarities']))
                                                        <div
                                                            style="display: none">{{ implode("\n", array_keys($item['similarities'])) }}</div>
                                                    @else
                                                        <div></div>
                                                    @endif
                                                    <div>
                                                    <span class="__helper-link ui_tooltip_w">
                                                        <span>{{ $item['based']['number'] ?? $item['based'] }}</span> /
                                                        <span>{{ $item['phrased']['number'] ?? $item['phrased'] }}</span> /
                                                        <span>{{ $item['target']['number'] ?? $item['target'] }}</span>
                                                        <span class="ui_tooltip __bottom">
                                                            <span class="ui_tooltip_content">
                                                                <span>{{ __('based') }}</span> /
                                                                <span>{{ __('phrased') }}</span> /
                                                                <span>{{ __('target') }}</span>
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
                            <div class="switch-container mb-3 d-flex">
                                <button id="change-sortable" class="btn btn-outline-secondary w-25" data-action="enable">
                                    {{ __('Moving groups') }}
                                </button>
                                <button id="relevance" class="btn btn-outline-secondary w-25" data-action="show">
                                    {{ __('Show relevant') }}
                                </button>
                                <button class="btn btn-outline-secondary w-50 radius hide-or-show w-50"
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

    @slot('js')
        <script src="{{ asset('plugins/sortable/sortable.min.js') }}"></script>
        <script>
            $('#app > div > div > div.card-header').append($('#params').html())
            $('#params').remove()

            if ($('#alone_phrases').html().trim() === '') {
                $('#alone_phrases').parent().remove()
            }

            $('.fa.fa-arrow-right.move-group').show()
            $('.fa.fa-edit.change-group-name.mr-2').show()
            $('.fa.fa-ellipsis.mr-2').parent().show()
            $('.list-group-item').show()
            $('.relevance-link').addClass('hide')

            $('.phrase-for-color').parent().parent().css({
                'background-color': 'white'
            })

            let worPlaceCreated = false
            let swapMainPhrase = ''
            let swapObject = ''
            let group

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

            refreshMethods()

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
                            successMessage("{{ __('Successfully') }}")
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
                                '                        <span>Базвая</span> /' +
                                '                        <span>Фразовая</span> /' +
                                '                        <span>Точная</span>' +
                                '                    </span>' +
                                '                </span>' +
                                '            </span>' +
                                '        </div>' +
                                '        <div class="btn-group">' +
                                '            <i data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="fa fa-ellipsis mr-2"></i> ' +
                                '            <div class="dropdown-menu">' +
                                '                <button data-toggle="modal" data-target="#exampleModal"' +
                                '                        class="dropdown-item add-to-another"' +
                                '                        data-action="' + phrase + '">' +
                                '                    Добавить фразу к другому кластеру' +
                                '                </button>' +
                                '                <button data-toggle="modal" class="dropdown-item color-phrases">' +
                                '                    Подсветить похожие фразы' +
                                '                </button>' +
                                '                <button data-toggle="modal" class="dropdown-item set-default-colors">' +
                                '                    Отменить выделение' +
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

                            saveHtml()
                        },
                        error: function (response) {
                        }
                    });
                }
            })

            function refreshMethods() {
                $('.move-phrase').unbind().on('click', function () {
                    if (worPlaceCreated) {
                        $(this).parent().parent().parent().hide(300)

                        $('#work-place-ul').append(
                            '<li class="list-group-item work-place-li" style="display: none" data-target="' + $(this).attr('data-target') + '">' +
                            '<div style="float: left">' +
                            '   <i class="fa fa-arrow-left move-back mr-2" data-target="' + $(this).attr('data-target') + '"></i>' +
                            '   <i class="fa fa-brush" data-target="' + $(this).attr('data-target') + '"></i>' +
                            '</div>' +
                            '<div style="float: right"><div class="phrase-for-color">' + $(this).attr('data-target') + '</div></div>' +
                            '</li>'
                        )

                        $('.work-place-li').show(300)

                        $('.move-back').unbind('click').on('click', function () {
                            $('.cluster-block').show()
                            $("ol").find(`[data-target='${$(this).attr('data-target')}']`).parents().eq(9).show()
                            $("ol").find(`[data-target='${$(this).attr('data-target')}']`).eq(0).show(300)
                            $(this).parent().parent().hide(300)
                            setTimeout(() => {
                                $(this).parent().parent().remove()
                                if ($('#work-place-ul').html().trim() === '') {
                                    $('#saveChanges').prop('disabled', true)
                                    $('#resetChanges').prop('disabled', true)
                                }
                            }, 300)
                        })

                        $('.fa.fa-brush').unbind('click').on('click', function () {
                            let targetHtml = $("ul").find(`[data-target='${$(this).attr('data-target')}']`).eq(0).children('div').eq(0).children('div').eq(1).html();
                            scanArray(targetHtml.split("\n"), $(this))
                        })

                        $('#saveChanges').prop('disabled', false)
                        $('#resetChanges').prop('disabled', false)
                    } else {
                        errorMessage("{{ __('First you need to add a new group') }}")
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

                                successMessage("{{ __('A phrase similar to the name of the group was automatically added to the group') }}", 5000)
                                refreshMethods()
                                saveHtml()
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

                    if (newName === '' || newName.length !== $('#editWorkPlaceName').val().trim().replace(/\d+/g, '').length) {
                        errorMessage("{{ __('The group name cannot be empty and contain numbers') }}")
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
                            saveHtml()
                        },
                        error: function (response) {
                        }
                    });

                })

                $('#resetChanges').unbind().on('click', function () {
                    $.each($('#work-place-ul').children('li'), function (key, value) {
                        $(this).children('div').eq(0).children('i').eq(0).trigger('click')
                    })

                    $('#workPlace').html("{{ __('Workspace') }}")
                    $('#addNewGroupButton').show()
                    $('#actionsButton').hide()
                    worPlaceCreated = false
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
                    }

                    $.each(group.children('ol').eq(0).children('div'), function () {
                        $(this).children('div').eq(0).children('div').eq(3).children('i').eq(1).trigger('click')
                    })

                    $('#addNewGroupButton').hide()
                    $('#actionsButton').show()

                    // group.hide()
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

            $(document).keypress(function (e) {
                if (e.which === 13 && $('#clusterFilter').is(':focus')) {
                    searchPhrases()
                }
            });

            $('#searchPhrases').on('click', function () {
                searchPhrases()
            })

            $('#setDefaultVision').on('click', function () {
                $('.phrase-for-color').parent().parent().show()
                $('.cluster-block').show()
            })

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
                    '                    <i data-toggle="collapse" aria-expanded="false" data-target="#' + newId + '" aria-controls="' + newId + '" class="fa fa-eye mr-2" style="color: white;"></i> ' +
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
                    '                    <i class="fa fa-arrow-right move-group" style="color: white; padding-top: 5px;"></i> ' +
                    '                    <span class="ui_tooltip __bottom">' +
                    '                        <span class="ui_tooltip_content">' +
                    '                             Переместить всю группу' +
                    '                        </span>' +
                    '                    </span>' +
                    '                </span>' +
                    '            </div>' +
                    '        </div>' +
                    '    </div>' +
                    newUl +
                    '</li>'
                );

                $('#workPlace').html("{{ __('Workspace') }}")
                $('#work-place-ul').html("")
                $('#addNewGroupButton').show()
                $('#actionsButton').hide()
                worPlaceCreated = false
                refreshMethods()
            }

            function recalculateFrequency() {
                $.each($('.card.cluster-block'), function (key, value) {
                    let base = 0
                    let phrase = 0
                    let target = 0
                    $.each($(this).children('ul').eq(0).children('li'), function (v, k) {
                        let span = $(this).children('div').eq(0).children('div').eq(2).children('span').eq(0)
                        base += +span.children('span').eq(0).html()
                        phrase += +span.children('span').eq(1).html()
                        target += +span.children('span').eq(2).html()
                    })

                    $(this).children('div').eq(0).children('div').eq(0).children('span').eq(2).html(base + ' / ' + phrase + ' / ' + target)
                })
            }

            function searchPhrases() {
                $('.phrase-for-color').parent().parent().show()
                $('.card.cluster-block').show()

                let searchSuccess = false
                let string = $('#clusterFilter').val().trim()
                let totalCount = 0

                if (string !== '') {
                    $.each($('.cluster-block'), function (key, value) {
                        // let thisBlock = $(this)
                        let ol = $(this).children('ol').eq(0)
                        // let countLi = ol.children('div').length
                        // let counter = 0
                        $.each($(ol.children('div')), function (key, value) {
                            if (!$(this).children('div').eq(0).children('div').eq(0).html().includes(string)) {
                                searchSuccess = true
                                totalCount += 1
                                // counter += 1
                                $(this).hide()
                            }
                        })
                        // if (counter === countLi) {
                        //     thisBlock.hide()
                        // }
                    })
                }

                if (!searchSuccess) {
                    errorMessage("{{ __('No matches found') }}")
                } else {
                    successMessage(totalCount + " {{ __('elements hidden') }}")
                }
            }

            $('#resetAllChanges').on('click', function () {
                $.ajax({
                    type: "POST",
                    url: "{{ route('reset.all.cluster.changes') }}",
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        projectId: {{ $cluster['id'] }},
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (response) {
                        alert('У вас нет прав')
                    }
                });
            })

            $('.hide-or-show').on('click', function () {
                if ($(this).attr('data-action') === 'hide') {
                    $.each($('#clusters-block').children('li'), function (key, val) {
                        let parent = $(this)
                        if (parent.children('ol').eq(0).hasClass('show')) {
                            parent.children('div').eq(0).children('div').eq(0).children('div').eq(1).children('span').eq(0).children('i').eq(0).trigger('click')
                        }
                    });

                    if ($('#alone_phrases').hasClass('show')) {
                        $('.alone-eye').trigger('click')
                    }
                    $('.hide-or-show').attr('data-action', 'show')
                    $('.hide-or-show').html("{{ __('Reveal groups') }}")
                } else {
                    $.each($('#clusters-block').children('li'), function (key, val) {
                        let parent = $(this)
                        if (!parent.children('ol').eq(0).hasClass('show')) {
                            parent.children('div').eq(0).children('div').eq(0).children('div').eq(1).children('span').eq(0).children('i').eq(0).trigger('click')
                        }
                    });

                    if (!$('#alone_phrases').hasClass('show')) {
                        $('.alone-eye').trigger('click')
                    }
                    $('.hide-or-show').attr('data-action', 'hide')
                    $('.hide-or-show').html("{{ __('Close groups') }}")
                }
            })

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
                    saveHtml()
                }
            })
            $('#clusters-block').sortable('disable')

            $('#change-sortable').on('click', function () {
                $('#clusters-block').sortable($(this).attr('data-action'));
                let place = $('.work-place-conf')

                if ($(this).attr('data-action') === 'disable') {
                    $(this).attr('data-action', 'enable')
                    $(this).html("{{ __('Moving groups') }}")

                    $('.fa.fa-arrow-right.move-group').show()
                    $('.fa.fa-edit.change-group-name.mr-2').show()
                    $('.fa.fa-ellipsis.mr-2').parent().show()
                    place.children('div').eq(2).show()
                    place.children('div').eq(2).removeClass('hide')
                    place.children('div').eq(3).show()
                } else {
                    if (worPlaceCreated) {
                        alert("{{ __('The workspace should be empty') }}")
                    } else {
                        $(this).attr('data-action', 'disable')
                        $(this).html("{{ __('Moving phrases') }}")

                        $('.fa.fa-arrow-right.move-group').hide()
                        $('.fa.fa-edit.change-group-name.mr-2').hide()
                        $('.fa.fa-ellipsis.mr-2').parent().hide()
                        place.children('div').eq(2).addClass('hide')
                        place.children('div').eq(3).hide()
                    }
                }
            })

            $('#relevance').on('click', function () {
                if ($(this).attr('data-action') === 'show') {
                    $('.relevance-link').removeClass('hide')
                    $(this).attr('data-action', 'hide')
                    $(this).html("{{ __('Hide relevant') }}")
                } else {
                    $('.relevance-link').addClass('hide')
                    $(this).attr('data-action', 'show')
                    $(this).html("{{ __('Show relevant') }}")
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
                        saveHtml()
                    },
                    error: function (response) {
                    }
                });
            })

            function saveHtml() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('save.clusters.html') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        projectId: {{ $cluster['id'] }},
                        html: $('#clusters-block').html()
                    },
                    success: function () {
                    }
                });
            }
        </script>
    @endslot
@endcomponent
