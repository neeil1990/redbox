@component('component.card', ['title' =>  __('Настройка порядка пунктов меню') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>

        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
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

            .moved-item {
                cursor: move;
                box-shadow: 0 0 2px grey;
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

            ol {
                list-style: none;
                padding: 0;
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

            li > i:hover {
                color: black;
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
            <div class="toast-message error-msg">
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div id="sortContainer" class="col-6">
            <ol class="nested_with_switch vertical">
                @foreach($items as $key => $item)
                    @if(array_key_exists('configurationInfo', $item))
                        <li data-name="{{ $key }}" class="w-100 pb-3" data-action="dir">
                            <div class="card-header group-name d-flex justify-content-between">
                                <div>
                                    {{ $key }}
                                </div>
                                <div class="btn-group btn-group-toggle w-75 hide">
                                    <input type="text" value="{{ $key }}" class="form form-control">
                                    <button class="btn btn-secondary change-group-name">
                                        {{ __('Change') }}
                                    </button>
                                </div>
                                <div class="d-flex justify-content-between" style="align-items: center">
                                    <span class="__helper-link ui_tooltip_w">
                                        <i data-toggle="collapse"
                                           aria-expanded="true"
                                           data-target="#{{ str_replace(' ', '_', $key) }}"
                                           aria-controls="{{ str_replace(' ', '_', $key) }}"
                                           class="fa fa-eye pr-2"
                                           data-action="{{ $item['configurationInfo']['show'] }}"
                                           style="color: white"></i>
                                        <span class="ui_tooltip __bottom">
                                            <span class="ui_tooltip_content">
                                                Скрыть/показать группу <br><br>
                                                <b>Эта настройка так же влияет на то, будет ли развёрнута группа в меню или нет</b>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-edit edit-dir-name pr-2" style="color: white"></i>
                                        <span class="ui_tooltip __bottom">
                                            <span class="ui_tooltip_content">
                                                Редактировать название группы
                                            </span>
                                        </span>
                                    </span>
                                    <span class="__helper-link ui_tooltip_w">
                                        <i data-toggle="modal" data-target="#removeModal" class="fa fa-trash remove-dir"
                                           style="color: white"></i>
                                        <span class="ui_tooltip __bottom">
                                            <span class="ui_tooltip_content">
                                                Удалить группу
                                            </span>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <ol class="for-nest @if($item['configurationInfo']['show'] === 'true') show @else collapse @endif"
                                id="{{ str_replace(' ', '_', $key) }}">
                                @foreach($item as $k => $elem)
                                    @if($k === 'configurationInfo')
                                        @continue
                                    @endif
                                    <li class="p-2 moved-item d-flex justify-content-between"
                                        data-id="{{ $elem['id'] }}" data-name="{{ $elem['title'] }}">
                                        {{ __($elem['title']) }}
                                    </li>
                                @endforeach
                            </ol>
                        </li>
                    @else
                        <li class="p-2 moved-item d-flex justify-content-between alone" data-id="{{ $item['id'] }}"
                            data-name="{{ $item['title'] }}">
                            {{ __($item['title']) }}
                        </li>
                    @endif
                @endforeach
            </ol>
        </div>

        <div id="configurationBlock" class="col-6">
            <div class="btn-group btn-group-toggle w-100">
                <button class="btn btn-outline-success w-25" id="saveChanges">
                    Сохранить изменения
                </button>
                <button class="btn btn-outline-primary w-25" data-toggle="modal" data-target="#addNewDir">
                    Создать группу
                </button>
                <button class="btn btn-outline-danger w-50" data-toggle="modal" data-target="#resetAllChanges">
                    Вернуть стандартную расстановку
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addNewDir" tabindex="-1" aria-labelledby="addNewDirLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewDirLabel">Введите название группы</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="dir">Название группы</label>
                    <input type="text" class="form form-control" name="dir" id="dir">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="createDirectory">
                        {{ __('Add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">Удаление группы</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Все пункты меню находящиеся в ней автоматически будут вынесены.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button id="removeSelectedBlock" type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('Remove') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resetAllChanges" tabindex="-1" aria-labelledby="resetAllChangesLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetAllChangesLabel">Вы можете восстановить значения по умолчанию</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Если вернуть значения по умолчанию, то порядок пунктов меню будет определена администраторами.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button id="restore" type="button" class="btn btn-danger"
                            data-dismiss="modal">{{ __('Restore standard sorting') }}</button>
                </div>
            </div>
        </div>
    </div>
    @slot('js')
        <script src="https://johnny.github.io/jquery-sortable/js/jquery-sortable.js"></script>
        <script>
            let groupBlock;
            let oldContainer;
            let jsonString;
            let group = $(".nested_with_switch").sortable({
                afterMove: function (placeholder, container) {
                    if (oldContainer !== container) {
                        if (oldContainer) {
                            oldContainer.el.removeClass('active')
                        }
                        container.el.addClass('active');
                        oldContainer = container;
                    }
                },
                onDrop: function ($item, container, _super) {
                    container.el.removeClass('active');
                    _super($item, container);
                },
                isValidTarget: function ($item, container) {
                    if (container.el.hasClass('block-nested')) {
                        return false;
                    } else {
                        return true;
                    }
                }

            });

            $(".switch-container").on("click", ".switch", function (e) {
                let method = $(this).hasClass("active") ? "enable" : "disable";
                $(e.delegateTarget).next().sortable(method);
            });

            $('#createDirectory').on('click', function () {
                let newDir = $('#dir')

                if (issetItem(newDir.val().trim())) {
                    errorMessage('Группа с таким названием уже существует')
                    return;
                }

                if (newDir.val().trim() !== '') {
                    $('.nested_with_switch.vertical').prepend(
                        '<li data-name="' + newDir.val() + '" class="w-100 pb-3" data-action="dir">' +
                        '    <div class="card-header group-name d-flex justify-content-between">' +
                        '        <div>' + newDir.val() + '</div>' +
                        '        <div class="btn-group btn-group-toggle w-75 hide">' +
                        '            <input type="text" value="' + newDir.val() + '" class="form form-control">' +
                        '                <button class="btn btn-secondary change-group-name">' +
                        '                    {{ __('Change') }} ' +
                        '                </button>' +
                        '        </div>' +
                        '        <div class="d-flex justify-content-between" style="align-items: center">' +
                        '                <span class="__helper-link ui_tooltip_w">' +
                        '                    <i data-toggle="collapse"' +
                        '                       aria-expanded="true"' +
                        '                       data-target="#' + newDir.val().replaceAll(' ', '_') + '"' +
                        '                       aria-controls="' + newDir.val().replaceAll(' ', '_') + '"' +
                        '                       class="fa fa-eye pr-2"' +
                        '                       data-action="true"' +
                        '                       style="color: white"></i>' +
                        '                    <span class="ui_tooltip __bottom">' +
                        '                        <span class="ui_tooltip_content">' +
                        '                            Скрыть/показать группу <br> <br>' +
                        '                            <b>Эта настройка так же влияет на то, будет ли развёрнута группа в меню или нет</b>' +
                        '                        </span>' +
                        '                    </span>' +
                        '                </span>' +
                        '            <span class="__helper-link ui_tooltip_w">' +
                        '                    <i class="fa fa-edit edit-dir-name pr-2" style="color: white"></i>' +
                        '                    <span class="ui_tooltip __bottom">' +
                        '                        <span class="ui_tooltip_content">' +
                        '                            Редактировать название группы' +
                        '                        </span>' +
                        '                    </span>' +
                        '                </span>' +
                        '            <span class="__helper-link ui_tooltip_w">' +
                        '                    <i class="fa fa-trash remove-dir" style="color: white"></i>' +
                        '                    <span class="ui_tooltip __bottom">' +
                        '                        <span class="ui_tooltip_content">' +
                        '                            Удалить группу' +
                        '                        </span>' +
                        '                    </span>' +
                        '                </span>' +
                        '        </div>' +
                        '    </div>' +
                        '    <ol id="' + newDir.val().replaceAll(' ', '_') + '" class="for-nest show"></ol>' +
                        '</li>'
                    )
                    newDir.val('')

                    refreshMethod()
                } else {
                    errorMessage('Название группы не может быть пустым')
                }

            });

            $('#saveChanges').unbind().on('click', function () {
                saveChanges()
            })

            $('#removeSelectedBlock').on('click', function () {
                $.each(groupBlock.children('ol').children('li'), function () {
                    $('.nested_with_switch').prepend($(this))
                })
                groupBlock.remove()
                $('#saveChanges').trigger('click')
            })

            $('#restore').on('click', function () {
                $.ajax({
                    type: "POST",
                    url: "{{ route('restore.configuration.menu') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        errorMessage("{{ __('Error') }}")
                    }
                });
            })

            function refreshMethod() {
                $('.group-name').unbind().mousedown(function () {
                    $('.for-nest').addClass('block-nested')
                }).mouseup(function () {
                    $('.for-nest').removeClass('block-nested')
                });

                $('.moved-item').unbind().mousedown(function () {
                    $('.for-nest').removeClass('block-nested')
                })

                $('.edit-dir-name').unbind().on('click', function () {
                    $(this).parent().parent().parent().children('div').eq(0).addClass('hide')
                    $(this).parent().parent().parent().children('div').eq(1).removeClass('hide')
                    $(this).parent().parent().parent().children('div').eq(2).addClass('hide')
                });

                $('.change-group-name').unbind().on('click', function () {
                    let val = $(this).parent().children('input').eq(0).val()
                    let parent = $(this).parent().parent().parent()
                    parent.attr('data-name', val)
                    parent.children('div').eq(0).children('div').eq(0).html(val)
                    parent.children('div').eq(0).children('div').eq(0).removeClass('hide')
                    parent.children('div').eq(0).children('div').eq(1).addClass('hide')
                    parent.children('div').eq(0).children('div').eq(2).removeClass('hide')
                })

                $('.remove-dir').unbind().on('click', function () {
                    groupBlock = $(this).parent().parent().parent().parent()
                })

                $('.__helper-link.ui_tooltip_w .fa.fa-eye.pr-2').unbind().on('click', function () {
                    if ($(this).attr('data-action') === 'false') {
                        $(this).attr('data-action', 'true')
                    } else {
                        $(this).attr('data-action', 'false')
                    }
                })
            }

            function issetItem(name) {
                let bool = false;
                $.each($(".nested_with_switch.vertical li"), function () {
                    if ($(this).attr('data-name') === name) {
                        bool = true;
                    }
                })

                return bool;
            }

            function errorMessage(message) {
                $('.toast.toast-error').show(300)
                $('.toast-message.error-msg').html(message)
                setTimeout(() => {
                    $('.toast.toast-error').hide(300)
                }, 5000)
            }

            function successMessage(message, timeout = 8000) {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html(message)
                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, timeout)
            }

            function configurationJson() {
                let items = []
                $.each($('.nested_with_switch.vertical').children('li'), function (key, value) {
                    let action = $(this).attr('data-action')
                    if (action === undefined) {
                        let id = $(this).attr('data-id')
                        let name = $(this).attr('data-name')
                        let obj = {
                            id: id,
                            name: name,
                        }
                        items.push(obj)
                    }
                    let dir = [];
                    let show = $(this).children('div').eq(0).children('div').eq(2).children('span').eq(0).children('i').eq(0).attr('data-action')
                    dir.push({
                        dirName: $(this).attr('data-name'),
                        dir: true,
                        show: show
                    })
                    $.each($(this).children('ol').eq(0).children('li'), function (key, value) {
                        let id = $(this).attr('data-id')
                        let name = $(this).attr('data-name')
                        dir.push({
                            id: id,
                            name: name,
                        })
                    })
                    items.push(dir)
                });

                return items
            }

            function saveChanges() {
                let items = configurationJson()

                $.ajax({
                    type: "POST",
                    url: "{{ route('configuration.menu') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        menuItems: JSON.stringify(items),
                    },
                    success: function () {
                        successMessage("{{ __('Successfully') }}", 1000)
                    },
                    error: function () {
                        errorMessage("{{ __('Error') }}")
                    }
                });
            }

            refreshMethod()
        </script>
    @endslot
@endcomponent
