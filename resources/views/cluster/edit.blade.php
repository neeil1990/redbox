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
                                {{ __('Number of clusters') }}: <span id="countClusters">{{ $cluster['count_clusters'] }}</span>
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
                                            <i class="fa fa-edit change-group-name mr-2"
                                               style="color: white; padding-top: 5px"></i>
                                            <i class="fa fa-trash remove-group-name" style="color: white"
                                               data-action="{{ $mainPhrase }}"
                                               data-toggle="modal" data-target="#removeGroup"></i>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach($items as $phrase => $item)
                                        @if($phrase !== 'finallyResult')
                                            <li class="list-group-item move-phrase" data-target="{{ $phrase }}"
                                                data-action="{{ $mainPhrase }}">
                                                <span class="d-flex justify-content-between">
                                                    {{ $phrase }}
                                                    <i class="fa fa-arrow-right"></i>
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <div class="work-place-block" style="width: 50%;">
                        <div class="card" style="margin-bottom: 0; border-radius: 0; position: sticky; top: 15px;">
                            <div class="card-header d-flex justify-content-between"
                                 style="background-color: #343a40; color: white">
                                {{ __('Workspace') }}
                            </div>
                            <ul class="list-group list-group-flush" id="work-place">
                            </ul>
                            <div>
                                <button class="btn btn-outline-secondary w-100"
                                        style="border-top-right-radius: 0; border-top-left-radius: 0"
                                        data-toggle="modal" data-target="#addNewGroup">
                                    {{ __('Add new group') }}
                                </button>
                            </div>
                        </div>
                    </div>
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
                        <br>
                        Если вы создали группу, и не заполнили её фразами, в момент перезагрузки страницы она будет удалена автоматически
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
            let lastBlock = ''

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
                }, 3000)
            }

            $('.work-place-block').css({
                'height': ($('#app > div > div > div.card-body > div.card > div.card-body').height() - 100) + 'px'
            })

            $('#app > div > div > div.card-header').append($('#params').html())
            $('#params').remove()

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

            refreshMethods()

            $('#save-changes').on('click', function () {
                let clusterPhrase = $('#clusters-list').val();
                let phrase = $('#your-phrase').val()

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
                        $('#countClusters').html(response.countClusters)
                        successMessage("{{ __('Successfully changed') }}")
                        $("ul").find(`[data-target='${phrase}']`).eq(0).remove()

                        let targetDiv = $('#' + clusterPhrase.replaceAll(' ', '_')).children('ul').eq(0)
                        let newBlock = '<li class="list-group-item move-phrase" style="background: #17a2b861" data-target="' + phrase + '" data-action="' + clusterPhrase + '"> ' +
                            '<span class="d-flex justify-content-between">' + phrase + '<i class="fa fa-arrow-right"></i> ' +
                            '</span> ' +
                            '</li>'
                        targetDiv.append(newBlock)
                        lastBlock.hide(300)
                        setTimeout(() => {
                            lastBlock.remove()
                        }, 300)
                        refreshMethods()

                        $.each($('.cluster-block'), function (key, value) {
                            if ($(this).children('ul').eq(0).html().replaceAll(' ', '') === '') {
                                $(this).remove()
                            }
                        })
                    },
                    error: function (response) {
                    }
                });
            })

            $('#add-new-group').on('click', function () {
                let error = false
                let groupName = $('#name-new-group').val()
                $.each($('#clusters-list').children(), function (key, value) {
                    if ($(this).attr('value') === groupName) {
                        error = true
                    }
                })

                if (error) {
                    errorMessage("{{ __('A group with the same name already exists') }}")
                } else {
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
                            $('#clusters-block').prepend('<div class="card cluster-block" style="margin-bottom: 0; border-radius: 0" id="' + groupName.replaceAll(' ', '_') + '"> ' +
                                '<div class="card-header" style="background-color: #343a40; color: white">' +
                                '    <div class="d-flex justify-content-between text-white">' +
                                '        <span>' + groupName + '</span>' +
                                '        <div class="btn-group btn-group-toggle w-75" style="display: none">' +
                                '            <input type="text" value="' + groupName + '"' +
                                '                   class="form form-control group-name-input"' +
                                '                   data-target="' + groupName + '">' +
                                '                <button class="btn btn-secondary edit-group-name">' +
                                '                    {{ __('Edit') }}' +
                                '                </button>' +
                                '        </div>' +
                                '    <div>' +
                                '        <i class="fa fa-edit change-group-name mr-2" style="color: white; padding-top: 5px"></i>' +
                                '        <i class="fa fa-trash remove-group-name" style="color: white" data-action="' + groupName + '"' +
                                '       data-toggle="modal" data-target="#removeGroup"></i>' +
                                '   </div>' +
                                '</div>' +
                                '</div>' +
                                '   <ul class="list-group list-group-flush"></ul>' +
                                '</div>')
                            $('#clusters-list').append('<option value="' + groupName + '">' + groupName + '</option>')

                            refreshMethods()
                        },
                        error: function () {
                            errorMessage("{{ __('A group with the same name already exists') }}")
                        }
                    });
                }
            })

            function refreshMethods() {
                $('.move-phrase').unbind('click').on('click', function () {
                    $(this).hide(300)

                    $('#work-place').append(
                        '<li class="list-group-item d-flex justify-content-between" data-target="' + $(this).attr('data-target') + '">' +
                        '<div>' +
                        '   <i class="fa fa-arrow-left move-back mr-2" data-target="' + $(this).attr('data-target') + '"></i>' +
                        '   <div class="btn-group"> ' +
                        '       <i class="fa fa-ellipsis" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>' +
                        '       <div class="dropdown-menu"> ' +
                        '           <a data-toggle="modal" data-target="#exampleModal" class="dropdown-item add-to-another"' +
                        '              href="#" data-order="' + $(this).attr('data-target') + '"' +
                        '              data-action="' + $(this).attr('data-action') + '">' +
                        '               Добавить фразу к другому кластеру' +
                        '           </a> ' +
                        '           <a class="dropdown-item move-back-from-menu" href="#" data-target="' + $(this).attr('data-target') + '">Вернуть назад</a> ' +
                        '       </div> ' +
                        '   </div>' +
                        '</div>' +
                        $(this).attr('data-target') +
                        '</li>'
                    )

                    $('.move-back').unbind('click').on('click', function () {
                        $("ul").find(`[data-target='${$(this).attr('data-target')}']`).eq(0).show(300)
                        $(this).parent().parent().hide(300)
                        setTimeout(() => {
                            $(this).parent().parent().remove()
                        }, 300)
                    })

                    $('.move-back-from-menu').unbind('click').on('click', function () {
                        $("ul").find(`[data-target='${$(this).attr('data-target')}']`).eq(0).show(300)
                        $(this).parent().parent().parent().parent().hide(300)
                        setTimeout(() => {
                            $(this).parent().parent().parent().parent().remove()
                        }, 300)
                    })

                    $('.add-to-another').unbind('click').on('click', function () {
                        lastBlock = $(this).parent().parent().parent().parent()
                        $('#your-phrase').val($(this).attr('data-order'))
                        $('#clusters-list option').prop('disabled', false);
                        $('#clusters-list option[value="' + $(this).attr('data-action') + '"]').prop('disabled', true);
                        $('#clusters-list option[value="' + $(this).attr('data-action') + '"]').prop('selected', false);
                    })
                })

                $('.change-group-name').unbind('click').on('click', function () {
                    let parent = $(this).parent()
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
                                // change id
                                span.parent().parent().parent().attr('id', newGroupName.replaceAll(' ', '_'))
                                // change name
                                span.html(newGroupName)
                                span.show()
                                div.hide()

                                //change data-target
                                div.children('input').eq(0).attr('data-target', newGroupName)

                                // change option
                                $.each($('#clusters-list').children(), function (key, value) {
                                    if ($(this).attr('value') === oldGroupName) {
                                        $(this).attr('value', newGroupName)
                                        $(this).html(newGroupName)
                                    }
                                })

                                // refresh methods
                                refreshMethods()
                            },
                            error: function () {
                                errorMessage("{{ __('A group with the same name already exists') }}")
                            }
                        });
                    }
                })
            }
        </script>
    @endslot
@endcomponent
