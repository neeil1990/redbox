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
                    <a class="nav-link admin-link"
                       href="{{ route('cluster.projects') }}">{{ __('My projects') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link active" href="{{ route('show.cluster.result', $cluster['id']) }}">
                        {{ __('My project') }}
                    </a>
                </li>
                @if($admin)
                    <li>
                        <a class="nav-link admin-link" href="{{ route('cluster.configuration') }}">
                            {{ __('Module administration') }}
                        </a>
                    </li>
                @endif
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
                                <span class="__helper-link ui_tooltip_w" id="show-all-phrases">
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
                    <div class="col-6" id="clusters-block">
                        @foreach($cluster->result as $mainPhrase => $items)
                            @php($hash = preg_replace("/[0-9.]/", "", Str::random()))
                            <div class="card cluster-block" style="margin-bottom: 0; border-radius: 0"
                                 id="{{ str_replace(' ', '_', $mainPhrase) }}">
                                <div class="card-header" style="background-color: #343a40; color: white">
                                    <div class="d-flex justify-content-between text-white">
                                        <span>{{ $mainPhrase }}</span>
                                        <div class="btn-group btn-group-toggle w-75" style="display: none">
                                            <input type="text" value="{{ $mainPhrase }}"
                                                   class="form form-control group-name-input"
                                                   data-target="{{ $mainPhrase }}">
                                            <button class="btn btn-secondary edit-group-name">
                                                {{ __('Edit') }}
                                            </button>
                                        </div>
                                        <div>
                                            <i class="fa fa-eye mr-2"
                                               data-toggle="collapse"
                                               aria-expanded="false"
                                               data-target="#{{ $hash }}"
                                               aria-controls="{{ $hash }}"
                                               style="color: white">
                                            </i>
                                            <i class="fa fa-edit change-group-name mr-2"
                                               style="color: white; padding-top: 5px"></i>
                                            <i class="fa fa-trash remove-group-name" style="color: white"
                                               data-action="{{ $mainPhrase }}"
                                               data-toggle="modal" data-target="#removeGroup"></i>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush collapse show" id="{{ $hash }}">
                                    @foreach($items as $phrase => $item)
                                        @if($phrase === 'finallyResult')
                                            @continue
                                        @endif
                                        <li class="list-group-item" data-target="{{ $phrase }}"
                                            data-action="{{ $mainPhrase }}">
                                            <div class="d-flex justify-content-between">
                                                <div class="phrase-for-color">{{ $phrase }}</div>
                                                @if(isset($item['similarities']))
                                                    <div style="display: none">@foreach($item['similarities'] as $ph => $count){{ $ph . "\n" }}@endforeach</div>
                                                @else
                                                    <div></div>
                                                @endif
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
                                                        <button data-toggle="modal" class="dropdown-item color-phrases">
                                                            Подсветить похожие фразы
                                                        </button>
                                                        <button data-toggle="modal"
                                                                class="dropdown-item set-default-colors">
                                                            отменить выделение
                                                        </button>
                                                    </div>
                                                    <i class="fa fa-arrow-right move-phrase"
                                                       data-target="{{ $phrase }}"></i>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-6">
                        <div class="btn-group w-100 mb-2">
                            <input type="text" id="clusterFilter" class="form form-control"
                                   placeholder="{{ __('Search') }}">
                            <button class="btn btn-outline-secondary" id="searchPhrases">
                                {{ __('Search') }}
                            </button>
                            <button class="btn btn-outline-secondary" id="setDefaultVision">
                                Отменить
                            </button>
                        </div>
                        <div class="card work-place-conf">
                            <div class="card-header d-flex justify-content-between" id="workPlace"
                                 style="background-color: #343a40; color: white">
                                {{ __('Workspace') }}
                            </div>
                            <ul class="list-group list-group-flush" id="work-place"></ul>
                            <div>
                                <button class="btn btn-outline-secondary w-100" id="addNewGroupButton"
                                        style="border-top-right-radius: 0; border-top-left-radius: 0"
                                        data-toggle="modal" data-target="#addNewGroup">
                                    {{ __('Add new group') }}
                                </button>
                                <div class="btn-group w-100" style="display: none" id="actionsButton">
                                    <button class="btn btn-outline-danger w-50" id="resetChanges"
                                            style="border-top-right-radius: 0; border-top-left-radius: 0;">
                                        {{ __('Reset changes') }}
                                    </button>
                                    <button class="btn btn-outline-primary w-50" id="saveChanges"
                                            style="border-top-right-radius: 0; border-top-left-radius: 0;">
                                        {{ __('Save changes') }}
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
                    <h5 class="modal-title" id="exampleModalLabel">Перемещение фразы</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <label for="your-phrase">Перемещаемая фраза</label>
                    <input type="text" name="your-phrase" id="your-phrase" class="form form-control" disabled>

                    <label for="clusters-list"></label>
                    <select name="clusters-list" id="clusters-list" class="custom-select">
                        @foreach($cluster->result as $mainPhrase => $items)
                            <option value="{{ $mainPhrase }}">{{ $mainPhrase }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-secondary" id="save-changes"
                            data-dismiss="modal">{{ __('Edit') }}</button>
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
                    <label for="name-new-group">Название новой группы</label>
                    <input type="text" name="name-new-group" id="name-new-group" class="form form-control">
                    <span class="text-muted">
                        Если такое название уже существует, то ваш запрос будет отвергнут.
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

    <div class="modal fade" id="removeGroup" tabindex="-1" aria-labelledby="removeGroupLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeGroupLabel">Удаление группы</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        Группы удаляются автоматически, когда у них не остаётся связанных с ними фраз
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @slot('js')
        <script>
            $('#app > div > div > div.card-header').append($('#params').html())
            $('#params').remove()

            let worPlaceCreated = false
            let swapMainPhrase = ''
            let swapObject = ''

            function successMessage(message = "{{ __('Successfully copied') }}") {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html(message)
                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, 3000)
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
                        success: function () {
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

                            $('#editWorkPlace').unbind().on('click', function () {
                                $('#editWorkPlaceBlock').show()
                                $(this).hide()
                                $('#groupName').hide()
                            })

                            $('#editWorkPlaceButton').unbind().on('click', function () {
                                let newName = $('#editWorkPlaceName').val()

                                if (newName === '') {
                                    errorMessage("{{ __('The name of the group cannot be empty') }}")
                                } else {
                                    $('#groupName').html(newName)

                                    $('#editWorkPlaceBlock').hide()
                                    $('#editWorkPlace').show()
                                    $('#groupName').show()
                                }
                            })

                            $('#saveChanges').unbind().on('click', function () {
                                saveChanges()
                            })
                        },
                        error: function () {
                            errorMessage("{{ __('A group with the same name already exists or the name contains numbers') }}")
                        }
                    });
                } else {
                    errorMessage("{{ __('The name of the group cannot be empty') }}")
                }
            })

            refreshMethods()

            $('.add-to-another').unbind('click').on('click', function () {
                $('#your-phrase').val($(this).attr('data-action'))
                swapObject = $(this).parent().parent().parent().parent()
                swapMainPhrase = String(swapObject.parent().attr('id')).replaceAll('_', ' ')
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
                            console.log(response)
                            successMessage("{{ __('Successfully') }}")
                            $('#' + clusterPhrase.replaceAll(' ', '_')).children('ul').eq(0).append(
                                '<li data-target="' + phrase + '" data-action="' + clusterPhrase + '" class="list-group-item">' +
                                '    <div class="d-flex justify-content-between">' +
                                '        <div class="phrase-for-color">' + phrase + '</div>' +
                                '        <div style="display: none">'+ response.similarities +'</div>' +
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
                                '                    отменить выделение' +
                                '                </button>' +
                                '            </div>' +
                                '            <i data-target="' + phrase + '" class="fa fa-arrow-right move-phrase"></i>' +
                                '        </div>' +
                                '    </span>' +
                                '</li>')
                            swapObject.remove()

                            $('.add-to-another').unbind('click').on('click', function () {
                                $('#your-phrase').val($(this).attr('data-action'))
                                swapObject = $(this).parent().parent().parent().parent()
                                swapMainPhrase = String(swapObject.parent().attr('id')).replaceAll('_', ' ')
                            })

                            $.each($('.list-group.list-group-flush'), function (key, value) {
                                if ($(this).html().replaceAll(' ', '') === '' && $(this).parent().attr('id') !== undefined) {
                                    let removePhrase = String($(this).parent().attr('id')).replaceAll('_', ' ')
                                    $(this).parent().remove()
                                    $("#clusters-list option[value='" + removePhrase + "']").remove()
                                }
                            })

                            refreshMethods()
                        },
                        error: function (response) {
                        }
                    });
                }
            })

            function refreshMethods() {
                $('.move-phrase').unbind('click').on('click', function () {
                    if (worPlaceCreated) {
                        $(this).parent().parent().parent().hide(300)

                        $('#work-place').append(
                            '<li class="list-group-item d-flex justify-content-between" style="display: none" data-target="' + $(this).attr('data-target') + '">' +
                            '<div>' +
                            '   <i class="fa fa-arrow-left move-back mr-2" data-target="' + $(this).attr('data-target') + '"></i>' +
                            '   <i class="fa fa-brush" data-target="' + $(this).attr('data-target') + '"></i>' +
                            '</div>' +
                            '<div><div class="phrase-for-color">' + $(this).attr('data-target') + '</div></div>' +
                            '</li>'
                        )

                        $('.list-group-item.d-flex.justify-content-between').show(300)

                        $('.move-back').unbind('click').on('click', function () {
                            $("ul").find(`[data-target='${$(this).attr('data-target')}']`).eq(0).show(300)
                            $(this).parent().parent().hide(300)
                            setTimeout(() => {
                                $(this).parent().parent().remove()
                            }, 300)
                        })

                        $('.fa.fa-brush').unbind('click').on('click', function () {
                            let targetHtml = $("ul").find(`[data-target='${$(this).attr('data-target')}']`).eq(0).children('div').eq(0).children('div').eq(1).html();

                            scanArray(targetHtml.split("\n"), $(this))
                        })

                    } else {
                        errorMessage("{{ __('First you need to add a new group') }}")
                    }
                })

                $('.change-group-name').unbind('click').on('click', function () {
                    let parent = $(this).parent().parent()
                    parent.children('span').eq(0).hide()
                    parent.children('div').eq(0).show()
                })

                $('.edit-group-name').unbind('click').on('click', function () {
                    let span = $(this).parent().parent().children('span').eq(0)
                    let div = $(this).parent().parent().children('div').eq(0)
                    let newGroupName = $(this).parent().children('input').eq(0).val()
                    let oldGroupName = $(this).parent().children('input').eq(0).attr('data-target')

                    if (newGroupName === oldGroupName) {
                        span.show()
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
                            success: function () {
                                span.parent().parent().parent().attr('id', newGroupName.replaceAll(' ', '_'))
                                span.html(newGroupName)
                                span.show()
                                div.hide()

                                div.children('input').eq(0).attr('data-target', newGroupName)

                                refreshMethods()
                            },
                            error: function () {
                                errorMessage("{{ __('A group with the same name already exists or the name contains numbers') }}")
                            }
                        });
                    }
                })

                $('.color-phrases').unbind('click').on('click', function () {
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

                $('.set-default-colors').unbind('click').on('click', function () {
                    $('.phrase-for-color').parent().parent().css({
                        'background-color': 'white'
                    })
                })
            }

            $('#resetChanges').on('click', function () {
                $.each($('#work-place').children('li'), function (key, value) {
                    $(this).trigger('click')
                })
            })

            function saveChanges() {
                let phrases = []
                $.each($('#work-place').children('li'), function (key, value) {
                    phrases.push($(this).attr('data-target'))
                })

                $.ajax({
                    type: "POST",
                    url: "/confirmation-new-cluster",
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        projectId: {{ $cluster['id'] }},
                        mainPhrase: $('#groupName').html(),
                        phrases: phrases,
                    },
                    success: function (response) {
                        window.location.reload()
                    },
                    error: function (response) {
                    }
                });
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

            $('#searchPhrases').on('click', function () {
                let searchSuccess = false
                let string = $('#clusterFilter').val().trim()
                let count = 0

                if (string !== '') {
                    $('.phrase-for-color').parent().parent().css({
                        'background-color': 'white'
                    })

                    $.each($('.phrase-for-color'), function (key, value) {
                        if (!$(this).html().includes(string)) {
                            searchSuccess = true
                            count += 1
                            $(this).parent().parent().hide()
                        }
                    })
                }

                if (!searchSuccess) {
                    errorMessage("{{ __('No matches found') }}")
                } else {
                    successMessage(count + " {{ __('elements hidden') }}")
                }
            })

            $('#setDefaultVision').on('click', function () {
                $('.phrase-for-color').parent().parent().show()
            })
        </script>
    @endslot
@endcomponent
