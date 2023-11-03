@component('component.card', ['title' =>  "SEO чеклист: проект $host"])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            i {
                transition: 0.3s;
                cursor: pointer;
            }

            .card-header::after {
                display: none;
            }

            .card ol {
                list-style: none;
                counter-reset: li;
            }

            .icon {
                width: 32px;
                height: 32px;
            }

            .border {
                list-style: none;
                padding: 0;
            }

            #tasks li, #new-tasks li, #stubs li, .stubs > .example {
                font-family: "Trebuchet MS", "Lucida Sans";
                padding: 7px 20px;
                border-radius: 5px;
                margin-bottom: 10px;
                border-left: 10px solid #f05d22;
                box-shadow: 2px -2px 5px 0 rgba(0, 0, 0, .1),
                -2px -2px 5px 0 rgba(0, 0, 0, .1),
                2px 2px 5px 0 rgba(0, 0, 0, .1),
                -2px 2px 5px 0 rgba(0, 0, 0, .1);
                font-size: 20px;
                letter-spacing: 2px;
                transition: 0.3s;
            }

            #tasks li.new {
                border-color: #007bff !important;
            }

            #tasks li.ready {
                border-color: #8bc63e !important;
            }

            #tasks li.expired {
                border-color: #f05d22 !important;
            }

            #tasks li.in_work {
                border-color: #1ccfc9 !important;
            }

            #tasks li.default,
            .stubs > .example {
                border-color: #5a6268 !important;
            }

            #tasks li:hover {
                cursor: pointer;
                box-shadow: 0 0 10px grey;
            }

            #tasks {
                padding-left: 0;
            }

            .hide-border {
                border: none;
            }

            .hide-border:active, .hide-border:focus {
                border: 1px solid #ced4da !important;
            }

            .fa-square:before {
                font-size: 1.6rem;
            }

            #new-tasks {
                padding-left: 0;
                padding-right: 10px;
                padding-top: 10px;
                overflow: auto;
                max-height: 600px;
            }

            .stub-style {
                width: 85px;
                height: 20px;
                font-size: 1rem;
                letter-spacing: 0;
                float: left;
            }
        </style>
    @endslot

    <div id="project-info" class="d-flex justify-content-between w-75">
        <div class="d-flex row align-items-center">
            <div>
                <span class="checklist-icon"></span>
            </div>
            <div>
                <span class="checklist-name"></span>
            </div>
        </div>
        <div>
            Количество задач:
            <span class="checklist-counter"></span>
        </div>
        <div>
            В работе:
            <span class="checklist-work"></span>
        </div>
        <div>
            Готово:
            <span class="checklist-ready"></span>
        </div>
        <div>
            Просрочено:
            <span class="checklist-expired"></span>
        </div>
    </div>

    <div class="d-flex align-items-baseline">
        <h3>Метки проекта:</h3>
        <ol class="d-flex" id="project-labels">
            @foreach($labels as $label)
                <li class="checklist-label mr-2"
                    data-target="{{ $checklist[0]['id'] }}"
                    data-id="{{ $label['id'] }}"
                    data-toggle="tooltip"
                    data-placement="top" title="{{ $label['name'] }}">
                     <span class="fas fa-square"
                           style="color: {{ $label['color'] }}"
                           data-toggle="modal"
                           data-target="#removeRelationModal"></span>
                </li>
            @endforeach
        </ol>
        <a href="#" data-toggle="modal"
           data-target="#addRelationModal">
            <u>
                Добавить метку
            </u>
        </a>
    </div>

    <div class="modal fade" id="removeRelationModal" tabindex="-1" aria-labelledby="removeRelationModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeRelationModalLabel">Подтвердите действие</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Вы собираетесь убрать метку с чеклиста
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="removeRelation">{{ __('Remove') }}</button>
                    <button type="button" class="btn btn-default" id="closeRemoveRelationModal"
                            data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRelationModal" tabindex="-1" aria-labelledby="addRelationModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRelationModalLabel">Добавить метку к проекту</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="labels">Ваши метки</label>
                    <select name="labels" id="new-label" class="form form-control">
                        @foreach($allLabels as $label)
                            <option value="{{ $label['id'] }}" id="option-tag-{{ $label['id'] }}"
                                    style="color: {{ $label['color'] }}">
                                {{ $label['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="add-relation">{{ __('Add') }}</button>
                    <button type="button" class="btn btn-default" id="closeRemoveRelationModal"
                            data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="block-from-notifications"></div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Фильтры
            </h3>
        </div>
        <div class="card-body row">
            <div class="form-group col-2">
                <label for="count">Количество задач</label>
                <select name="count" id="count" class="custom custom-select">
                    <option value="1">1</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                </select>
            </div>
            <div class="form-group col-2">
                <label for="name">Название</label>
                <input type="text" id="name" name="name" class="form form-control">
            </div>
            <div class="form-group col-2">
                <label for="sort">Сортировка</label>
                <select name="sort" id="sort" class="custom custom-select">
                    <option value="all" selected>Любые</option>
                    <option value="new">Сначала новые</option>
                    <option value="old">Сначала старые</option>
                    <option value="ready">Завершённые</option>
                    <option value="work">В работе</option>
                    <option value="expired">Просроченные</option>
                </select>
            </div>
        </div>
    </div>

    <ol id="tasks">
        <div class="d-flex justify-content-center align-items-center w-100 mt-5"
             style="width: 100%;">
            <img src="/img/1485.gif" style="width: 80px; height: 80px;">
        </div>
    </ol>

    <ul class="pagination d-flex justify-content-end w-100" id="pagination"></ul>

    <div class="d-flex justify-content-end">
        <button class="btn btn-secondary mr-1" data-toggle="modal"
                data-target="#createNewProject"
                id="add-new-tasks">
            Добавить новую задачу
        </button>
    </div>

    <div class="modal fade" id="createNewProject" tabindex="-1" aria-labelledby="createNewProjectLabel"
         aria-hidden="true">
        <div class="modal-dialog d-flex" style="min-width: 95vw;">
            <div class="modal-content col-9 mr-2">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewProjectLabel">Добавление новых задач к
                        проекту {{ $checklist[0]['url'] }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body d-flex">
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between mb-3">
                                <label for="tasks">Новые задачи</label>
                                <button class="btn btn-secondary" id="add-new-task">Добавить задачу</button>
                            </div>
                            <div id="accordionExample">
                                <ol id="new-tasks"></ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div></div>
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-success" id="save-new-checklist">
                            {{ __('Save') }}
                        </button>
                        <img id="loader" src="/img/1485.gif" style="width: 30px; height: 30px; display: none">
                    </div>
                </div>
            </div>
            <div class="modal-content col-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewProjectLabel">Шаблоны</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="stubs-place" style="overflow: auto; height: 80vh">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="set-stub">Применить шаблон</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="projectModal" tabindex="-1" role="dialog" aria-labelledby="projectModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectModalLabel">Подтвердите действие</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Перенести проект в архив ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="move-to-archive"
                            data-dismiss="modal">{{ __('Archive it') }}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            let stubs
            $(document).on('click', '#set-stub', function () {
                let basicID = $('.ribbon-wrapper.ribbon-lg').parent().attr('data-id')

                if (basicID === undefined) {
                    errorMessage(['Шаблон не выбран'])
                } else {
                    $('#new-tasks').html(generateTasks(JSON.parse(stubs[basicID].tree)))
                    refreshTooltips()
                }
            })

            function generateTasks(tasks) {
                let html = ''
                $.each(tasks, function (index, task) {
                    let ID = getRandomInt(9999999)
                    task = task[0] ?? task

                    let $listItem = '<li data-id="' + ID + '" class="default">' +
                        '    <input type="text" class="form form-control hide-border d-inline w-75" data-type="name" placeholder="Без названия" data-target="' + ID + '">' +
                        '    <div class="tools d-flex" style="float: right">' +
                        '        <input class="form form-control hide-border" data-type="start" type="datetime-local" data-target="' + ID + '" data-toggle="tooltip" data-placement="top" title="Дата начала">' +
                        '        <input class="form form-control hide-border" data-type="deadline" type="datetime-local" data-target="' + ID + '" data-toggle="tooltip" data-placement="top" title="Дата окончания">' +
                        '        <select data-id="status-' + ID + '" data-target="' + ID + '" class="custom custom-select" data-type="status">' +
                        '            <option value="new" selected>Новая</option>' +
                        '            <option value="in_work">В работе</option>' +
                        '            <option value="ready">Готово</option>' +
                        '            <option value="expired">Просрочено</option>' +
                        '        </select>' +
                        '        <div class="btn-group pl-2">' +
                        '            <button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-description-' + ID + '" role="button" aria-expanded="false" aria-controls="collapse-description-' + ID + '"><i class="fa fa-eye"></i></button>' +
                        '            <button class="btn btn-sm btn-default add-new-pre-subtask" data-id="' + ID + '"><i class="fa fa-plus"></i></button>' +
                        '            <button class="btn btn-sm btn-default remove-pre-task"><i class="fa fa-trash"></i></button>' +
                        '        </div>' +
                        '    </div>' +
                        '</li>' +
                        '<div class="collapse" id="collapse-description-' + ID + '">' +
                        '    <div class="card card-body"><textarea class="pre-description" data-id="' + ID + '"></textarea></div>' +
                        '</div>'

                    let $subList = '<ol id="subtasks-' + ID + '" class="mt-3">';

                    if (task.subtasks) {
                        $subList += generateTasks(task.subtasks);
                    }
                    $listItem += $subList + '</ol>'
                    html += $listItem
                })

                return html
            }

            $(document).on('click', '.accordion.stubs.card.card-body', function (e) {
                if (!$(e.target).hasClass('remove-stub')) {
                    $('.ribbon-wrapper.ribbon-lg').remove();

                    $(this).append(
                        '<div class="ribbon-wrapper ribbon-lg">' +
                        '    <div class="ribbon bg-primary">' +
                        '        Выбрано' +
                        '    </div>' +
                        '</div>'
                    );
                }
            });

            $('#add-new-tasks').on('click', function () {
                if ($('#new-tasks').html() === '') {
                    $('#add-new-task').trigger('click')
                }

                $.ajax({
                    type: 'get',
                    url: "{{ route('checklist.stubs') }}",
                    data: {
                        labelID: labelID,
                        checkListID: {{ $checklist[0]['id'] }}
                    },
                    success: function (response) {
                        stubs = response
                        renderStubs(response)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            function getParameterByName(name, url) {
                if (!url) {
                    url = window.location.href
                }

                name = name.replace(/[\[\]]/g, "\\$&");
                let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);

                if (!results) return null;

                if (!results[2]) return '';

                return decodeURIComponent(results[2].replace(/\+/g, " "));
            }

            let notificationBlocks = 0
            let checklist = {!! json_encode($checklist) !!};

            $('#app > div > div > div.card-header').append($('#project-info'))
            $('#app > div > div > div.card-header > .card-title').remove()

            $(function () {
                if (localStorage.getItem('REDBOX_SEO_TASKS_COUNT') !== null) {
                    $('#count').val(localStorage.getItem('REDBOX_SEO_TASKS_COUNT'))
                }
                let searchTaskValue = getParameterByName("search_task");
                $('#name').val(searchTaskValue)

                getTasks(0, true)
            })

            let editedID
            let editedTimeout

            $('#add-new-task').on('click', function () {
                let id = getRandomInt(99999)
                $('#new-tasks').append(stub(id))

                $('.pre-description').summernote({
                    callbacks: {
                        onChange: function (contents, $editable) {
                            editedID = $editable.parents().eq(2).find('textarea:first-child').attr('data-id')
                            clearTimeout(editedTimeout)
                            editedTimeout = setTimeout(() => {
                                $.ajax({
                                    type: 'post',
                                    url: "{{ route('edit.checklist.task') }}",
                                    data: {
                                        id: editedID,
                                        type: 'description',
                                        value: contents,
                                    },
                                    success: function (response) {
                                        successMessage('Успешно')
                                    },
                                    error: function (response) {
                                        errorMessage(response.responseJSON.errors)
                                    }
                                })
                            }, 1000)
                        }
                    },
                    minHeight: 350,
                })
            })

            function generateNestedLists(tasks, ribbion = false) {
                let $listItem = ''
                $.each(tasks, function (k, task) {
                    task = task[0] ?? task
                    $listItem +=
                        ' <li class="default example">' +
                        '     <div style="height: 20px;">' +
                        '         <span class="stub-style text-muted">' +
                        '             Название' +
                        '         </span>' +
                        '         <div style="float: right" class="d-flex">' +
                        '             <div class="btn btn-sm btn-default" style="width: 35px; height: 20px; border-radius: 4px"></div>' +
                        '             <div class="btn btn-sm btn-default" style="width: 25px; height: 20px;"></div>' +
                        '         </div>' +
                        '     </div>' +
                        ' </li>'

                    let $subList = '<ol class="accordion stubs">';
                    if (task.subtasks && task.subtasks.length > 0) {
                        task.subtasks.forEach(function (subtask) {
                            $subList += generateNestedLists(subtask);
                        });
                    }
                    $subList += '</ol>';
                    $listItem += $subList
                });

                if (ribbion) {
                    $listItem += '<div class="ribbon-wrapper ribbon-lg">' +
                        '    <div class="ribbon bg-primary">' +
                        '        Выбрано' +
                        '    </div>' +
                        '</div>'
                }

                return $listItem
            }

            function refreshTooltips() {
                $('[data-toggle="tooltip"]').tooltip('dispose');
                $('[data-toggle="tooltip"]').tooltip()

                $('.description').summernote({
                    callbacks: {
                        onChange: function (contents, $editable) {
                            editedID = $editable.parents().eq(2).find('textarea:first-child').attr('data-id')
                            clearTimeout(editedTimeout)
                            editedTimeout = setTimeout(() => {
                                $.ajax({
                                    type: 'post',
                                    url: "{{ route('edit.checklist.task') }}",
                                    data: {
                                        id: editedID,
                                        type: 'description',
                                        value: contents,
                                    },
                                    success: function (response) {
                                        successMessage('Успешно')
                                    },
                                    error: function (response) {
                                        errorMessage(response.responseJSON.errors)
                                    }
                                })
                            }, 1000)
                        }
                    },
                    minHeight: 350,
                });

            }

            function errorMessage(errors) {
                let messages = ''
                $.each(errors, function (k, v) {
                    messages += v + "<br>"
                })

                let margin = notificationBlocks * 70
                notificationBlocks++

                let $block =
                    $(
                        '<div id="toast-container" class="toast-top-right error-message" style="display:none; top: ' + margin + 'px">' +
                        '    <div class="toast toast-error" aria-live="polite">' +
                        '        <div class="toast-message" id="toast-error-message">' + messages + '</div>' +
                        '    </div>' +
                        '</div>'
                    )


                $('#block-from-notifications').append($block)

                $block.show(300)

                setTimeout(() => {
                    $block.remove()
                    notificationBlocks--
                }, 5000)
            }

            function successMessage(message) {
                let margin = notificationBlocks * 70
                notificationBlocks++

                let $block =
                    $('<div id="toast-container" class="toast-top-right success-message" style="display: none; top: ' + margin + 'px">' +
                        '    <div class="toast toast-success" aria-live="polite">' +
                        '        <div class="toast-message" id="toast-success-message">' + message + '</div>' +
                        '    </div>' +
                        '</div>')

                $('#block-from-notifications').append($block)

                $block.show(300)

                setTimeout(() => {
                    $block.remove()
                    notificationBlocks--
                }, 5000)
            }

            $(document).on('change', '.edit-checklist', function () {
                let targetBlock = $(this)
                let type = $(this).attr('data-type')
                let val = $(this).val()
                let ID = $(this).attr('data-target')

                $.ajax({
                    type: 'post',
                    url: "{{ route('edit.checklist.task') }}",
                    data: {
                        id: $(this).attr('data-target'),
                        type: type,
                        value: val,
                    },
                    success: function (response) {
                        if (type === 'status') {
                            let parent = targetBlock.parents().eq(1)
                            parent.removeClass()
                            parent.addClass(val)
                        }

                        if (response.newStatus === 'expired') {
                            $("select[data-target='" + ID + "']").find('option[value="expired"]').prop('selected', true);
                        }

                        successMessage('Успешно')

                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            $(document).on('click', '.remove-pre-task', function () {
                if ($(this).parent().find('.save-new-task').length > 0) {
                    $('.add-new-subtask').show(300)
                    $('#add-new-task').attr('disabled', false)
                }

                let $parent = $(this).parents().eq(2)
                $('#collapse-description-' + $parent.attr('data-id')).remove()
                $('#subtasks-' + $parent.attr('data-id')).remove()

                $parent.remove()
            })

            $(document).on('click', '.add-new-pre-subtask', function () {
                let ID = $(this).attr('data-id')
                let randomID = getRandomInt(999999)

                $('#subtasks-' + ID).append(stub(randomID))
            })

            $(document).on('click', '.add-new-subtask', function () {
                let ID = $(this).attr('data-id')
                let randomID = getRandomInt(999999)

                $('#add-new-task').attr('disabled', true)
                $('.add-new-subtask').hide(300)
                $('#subtasks-' + ID).append(stub(randomID))
            })

            $(document).on('click', '.remove-real-task', function () {
                let taskID = $(this).attr('data-id')
                if (confirm('Подтвердите удаление задачи')) {
                    if ($('#subtasks-' + taskID).children('li').length > 0) {
                        removeTasks(taskID, confirm('Удалить связанные подзадачи?'))
                        return;
                    }
                    removeTasks(taskID, false)
                }
            })

            $(document).on('click', '.save-new-task', function () {
                let $parent = $(this).parents().eq(2)
                let objects = parseTree($parent)

                let parentID = null

                if ($parent.parent().attr('id').includes('subtasks-')) {
                    parentID = $parent.parent().attr('id').replace('subtasks-', '')
                }

                $.ajax({
                    type: 'post',
                    url: "{{ route('add.new.tasks.in.checklist') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: checklist[0].id,
                        parentID: parentID,
                        tasks: objects
                    },
                    success: function (message) {
                        successMessage(message)
                        getTasks()
                        $('#add-new-task').attr('disabled', false)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            function parseTree($object) {
                let $dataId = $object.attr('data-id')
                let object = []
                let $subtasks = []

                let test = {
                    name: $('input[data-target="' + $dataId + '"][data-type="name"]').val(),
                    status: $('select[data-target="' + $dataId + '"][data-type="status"]').val(),
                    description: $('.pre-description[data-id="' + $dataId + '"]').val(),
                    deadline: $('input[data-type="deadline"][data-target="' + $dataId + '"]').val(),
                }

                if ($('#subtasks-' + $dataId).children('li').length > 0) {
                    $.each($('#subtasks-' + $dataId).children('li'), function () {
                        $subtasks.push(parseTree($(this)))
                    })
                }

                test.subtasks = $subtasks
                object.push(test)

                return object
            }

            function getTasks(page = 0, renderPaginate = false) {
                $('#tasks').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5"' +
                    '     style="width: 100%;">' +
                    '    <img src="/img/1485.gif" style="width: 80px; height: 80px;">' +
                    '</div>'
                )

                $.ajax({
                    type: 'post',
                    url: "{{ route('checklist.tasks') }}",
                    data: {
                        id: checklist[0].id,
                        sort: $('#sort').val(),
                        count: $('#count').val(),
                        search: $('#name').val(),
                        skip: page * $('#count').val(),
                    },
                    success: function (response) {
                        let checklist = response.checklist[0]

                        $(".checklist-icon").html('<img src="/storage/' + checklist.icon + '" alt="' + checklist.icon + '" class="icon mr-2">')
                        $(".checklist-name").html('<a href="' + checklist.url + '" target="_blank" data-toggle="tooltip" data-placement="top" title="' + checklist.url + '">' + new URL(checklist.url)['host'] + '</a>')
                        $(".checklist-counter").html(checklist.work + checklist.ready + checklist.expired)
                        $(".checklist-work").html(checklist.work)
                        $(".checklist-expired").html(checklist.expired)
                        $(".checklist-ready").html(checklist.ready)


                        $('#tasks-ready').html(checklist.ready)
                        $('#tasks-work').html(checklist.work)
                        $('#tasks-expired').html(checklist.expired)

                        $('#tasks').html('')
                        if (response.tasks.length > 0) {
                            response.tasks.forEach(function (task) {
                                $('#tasks').append(generateNestedLists(task))
                            });

                            refreshTooltips()
                        }

                        if (renderPaginate) {
                            renderPagination(response)
                        }
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            }

            function renderStubs(basicTasks) {
                let html = ''

                $.each(basicTasks, function (index, tasks) {
                    let button = ''
                    let stubType = ''
                    if (tasks.type === 'personal') {
                        stubType = '<span class="text-primary mb-3">Ваш шаблон</span>'
                        button = '<button class="btn btn-sm btn-default remove-stub mt-3" data-id="' + tasks.id + '"><i class="fa fa-trash"></i></button>'
                    } else {
                        stubType = '<span class="text-info mb-3">Базовый шаблон</span>'
                    }

                    html += '<ol class="accordion stubs card card-body" data-id="' + index + '">'
                    html += stubType
                    html += generateNestedLists(JSON.parse(tasks.tree), index === 0)
                    html += button
                    html += '</ol>'
                });

                $('#stubs-place').html(html)
            }

            function getRandomInt(max) {
                return Math.floor(Math.random() * max);
            }

            function stub(id) {
                return '<li data-id="' + id + '" class="default d-flex">' +
                    '    <input type="text" class="form form-control hide-border" data-type="name" placeholder="Без названия" data-target="' + id + '">' +
                    '    <div class="tools d-flex" style="float: right">' +
                    '        <input class="form form-control hide-border" data-type="start" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="top" title="Дата начала">' +
                    '        <input class="form form-control hide-border" data-type="deadline" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="top" title="Дата окончания">' +
                    '        <select data-id="status-' + id + '" data-target="' + id + '" class="custom custom-select" data-type="status" style="width: 135px">' +
                    '            <option value="new" selected>Новая</option>' +
                    '            <option value="in_work">В работе</option>' +
                    '            <option value="ready">Готово</option>' +
                    '            <option value="expired">Просрочено</option>' +
                    '        </select>' +
                    '        <div class="btn-group pl-2">' +
                    '            <button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-description-' + id + '" role="button" aria-expanded="false" aria-controls="collapse-description-' + id + '"><i class="fa fa-eye"></i></button>' +
                    '            <button class="btn btn-sm btn-default add-new-pre-subtask" data-id="' + id + '"><i class="fa fa-plus"></i></button>' +
                    '            <button class="btn btn-sm btn-default remove-pre-task"><i class="fa fa-trash"></i></button>' +
                    '        </div>' +
                    '    </div>' +
                    '</li>' +
                    '<div class="collapse" id="collapse-description-' + id + '">' +
                    '    <div class="card card-body"><textarea class="pre-description" data-id="' + id + '"></textarea></div>' +
                    '</div>' +
                    '<ol id="subtasks-' + id + '" class="mt-3"></ol>'
            }

            function removeTasks(taskID, removeSubTasks) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('remove.checklist.task') }}",
                    data: {
                        id: taskID,
                        removeSubTasks: removeSubTasks
                    },
                    success: function (message) {
                        successMessage(message)
                        getTasks()
                    },
                    error: function (response) {
                        errorMessage(response.resposeJSON.errors)
                    }
                })
            }

            function renderPagination(response) {
                let pagination = ''
                for (let i = 0; i < response.paginate; i++) {
                    let html = i + 1

                    if (i === 0) {
                        pagination += '<li class="page-item active"><a href="#" class="page-link" data-id="' + i + '">' + html + '</a></li>'
                    } else {
                        pagination += '<li class="page-item"><a href="#" class="page-link" data-id="' + i + '">' + html + '</a></li>'
                    }
                }
                $('#pagination').html(pagination)
            }

            $(document).on('click', '.page-link', function () {
                $('.page-item.active').removeClass('active')
                $(this).parent().addClass('active')

                getTasks($(this).attr('data-id'), false)
            })

            $(document).on('change', '#count', function () {
                localStorage.setItem('REDBOX_SEO_TASKS_COUNT', $(this).val())
                getTasks(0, true)
            })

            let nameTimeout

            $(document).on('input', '#name', function () {
                clearTimeout(nameTimeout)

                nameTimeout = setTimeout(() => {
                    getTasks(0, true)
                }, 1000)
            })

            $(document).on('change', '#sort', function () {
                getTasks(0, true)
            })

            let labelID
            let removedLI

            $(document).on('click', '.checklist-label', function () {
                labelID = $(this).attr('data-id')
                removedLI = $(this)
            })

            $('#removeRelation').on('click', function () {
                $.ajax({
                    type: 'post',
                    url: "{{ route('remove.checklist.relation') }}",
                    data: {
                        labelID: labelID,
                        checkListID: {{ $checklist[0]['id'] }}
                    },
                    success: function (message) {
                        successMessage(message)
                        removedLI.remove()
                        $('#closeRemoveRelationModal').trigger('click')
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            $('#add-relation').on('click', function () {
                let checklistID = "{{ $checklist[0]['id'] }}";
                let labelID = $('#new-label').val()

                $.ajax({
                    type: 'post',
                    url: "{{ route('create.checklist.relation') }}",
                    data: {
                        checklistId: checklistID,
                        labelId: labelID
                    },
                    success: function (label) {
                        successMessage('Метка добавлена к проекту')
                        $('#project-labels').append(
                            '<li class="checklist-label mr-2" data-target="' + checklistID + '" data-id="' + labelID + '" data-toggle="tooltip"' +
                            '    data-placement="top" title="' + label.name + '">' +
                            '         <span class="fas fa-square"' +
                            '               style="color: ' + label.color + ' !important;"' +
                            '               data-toggle="modal"' +
                            '               data-target="#removeRelationModal"></span>' +
                            '</li>'
                        )

                        refreshTooltips()
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })
        </script>
    @endslot
@endcomponent
