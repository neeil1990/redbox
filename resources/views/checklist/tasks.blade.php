@component('component.card', ['title' =>  __('Tasks') . " $host"])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            i {
                transition: 0.3s;
                cursor: pointer;
            }

            .width {
                width: 150px;
                font-size: 1.2rem;
            }

            .updated-font-size {
                font-size: 1.2rem;
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

            #tasks li {
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

            #tasks li.ready {
                border-color: #8bc63e !important;
            }

            #tasks li.expired {
                border-color: #f05d22 !important;
            }

            #tasks li.in_work {
                border-color: #1ccfc9 !important;
            }

            #tasks li.default {
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
        </style>
    @endslot

    <div id="project-info" class="d-flex justify-content-between w-75">
        <div>
            <span class="checklist-icon"></span>
        </div>
        <div>
            <span class="checklist-name"></span>
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

    <div id="block-from-notifications"></div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Филтьры
            </h3>
        </div>
        <div class="card-body row">
            <div class="form-group col-2">
                <label for="count">Количество задач</label>
                <select name="count" id="count" class="custom custom-select">
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
        <button class="btn btn-secondary" id="add-new-task">Добавить новую задачу</button>
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

    <div class="modal fade" id="createNewProject" tabindex="-1" aria-labelledby="createNewProjectLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewProjectLabel">Добавление нового проекта</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="url">Url</label>
                        <input type="text" name="url" id="url" class="form form-control">
                    </div>
                    <div class="form-group">
                        <label for="tasks">Задачи</label>
                        <div>
                            <div class="accordion" id="accordionExample">
                                <ol id="tasks">
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button class="btn btn-default" id="add-task">Добавить задачу</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-success" id="save-new-checklist">
                            {{ __('Save') }}
                        </button>
                        <img id="loader" src="/img/1485.gif" style="width: 30px; height: 30px; display: none">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">Подтвердите действие</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Вы собираетесь удалить проект из архива, данные будут потеряны и их нельзя будет восстановить.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="remove">{{ __('Delete') }}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rotateModal" tabindex="-1" aria-labelledby="rotateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rotateModalLabel">Подтвердите действие</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Восстановление проекта из архива
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="rotate-checklist">Восстановить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
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

    <div class="modal fade" id="checklistTaskModal" tabindex="-1" aria-labelledby="checklistTaskModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="max-width: 65vw">
            <div class="modal-content">
                <div class="modal-header" style="font-size: 1.1rem">
                    <div class="d-flex row justify-content-between w-75">
                        <div id="project-name"></div>
                        <div style="margin-top: 5px">
                            Готово: <b id="tasks-ready"></b>
                        </div>
                        <div style="margin-top: 5px">
                            В работе: <b id="tasks-work"></b>
                        </div>
                        <div style="margin-top: 5px">
                            Просрочены: <b id="tasks-expired"></b>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="tasks-info" class="todo-list"></ul>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button class="btn btn-success"
                                id="save-new-tasks"
                                style="display:none;">
                            {{ __('Save') }}
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-secondary" id="add-real-task">Добавить задачу</button>
                        <button type="button" class="btn btn-default" id="close-real-tasks"
                                data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            let notificationBlocks = 0
            let checklist = {!! json_encode($checklist) !!};

            $('#app > div > div > div.card-header').append($('#project-info'))
            $('#app > div > div > div.card-header > .card-title').remove()

            $(function () {
                if (localStorage.getItem('REDBOX_SEO_TASKS_COUNT') !== null) {
                    $('#count').val(localStorage.getItem('REDBOX_SEO_TASKS_COUNT'))
                }

                getTasks(0, true)
            })

            let editedID
            let editedTimeout

            $('#add-new-task').on('click', function () {
                $(this).attr('disabled', true)
                $('.add-new-subtask').hide(300)
                let id = getRandomInt(99999)
                $('#tasks').append(stub(id, true))

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

            function generateNestedLists(task) {
                let work = '<option value="in_work">В работе</option>'
                let ready = '<option value="ready">Готово</option>'
                let expired = '<option value="expired">Просрочено</option>'

                if (task.status === 'in_work') {
                    work = '<option value="in_work" selected>В работе</option>'
                } else if (task.status === 'ready') {
                    ready = '<option value="ready" selected>Готово</option>'
                } else {
                    expired = '<option value="expired" selected>Просрочено</option>'
                }

                let $listItem =
                    '<li data-id="' + task.id + '" class="' + task.status + '">' +
                    '    <input type="text" class="form form-control hide-border d-inline edit-checklist" data-type="name" data-target="' + task.id + '" style="width: 350px" value="' + task.name + '">' +
                    '    <div class="tools d-flex" style="float: right">' +
                    '       <input class="form form-control hide-border edit-checklist" data-type="date_start" type="datetime-local" data-target="' + task.id + '" value="' + task.date_start + '" ' +
                    '       data-toggle="tooltip" data-placement="top" title="Дата начала">' +
                    '       <input class="form form-control hide-border edit-checklist" data-type="deadline" type="datetime-local" data-target="' + task.id + '" value="' + task.deadline + '" ' +
                    '       data-toggle="tooltip" data-placement="top" title="Дата окончания">' +
                    '                <select data-id="status-' + task.id + '" data-target="' + task.id + '" class="custom custom-select edit-checklist" data-type="status">' +
                    work +
                    ready +
                    expired +
                    '               </select>' +
                    '       <div class="btn-group pl-2">' +
                    '           <button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-description-' + task.id + '" role="button" aria-expanded="false" aria-controls="collapse-description-' + task.id + '"><i class="fa fa-eye"></i></button>' +
                    '           <button class="btn btn-sm btn-default add-new-subtask" data-id="' + task.id + '"><i class="fa fa-plus"></i></button>' +
                    '           <button class="btn btn-sm btn-default remove-real-task" data-id="' + task.id + '"><i class="fa fa-trash"></i></button>' +
                    '       </div>' +
                    '    </div>' +
                    '</li>' +
                    '<div class="collapse" id="collapse-description-' + task.id + '">' +
                    '    <div class="card card-body"><textarea class="description" data-id="' + task.id + '">' + task.description + '</textarea></div>' +
                    '</div>'

                let $subList = '<ol id="subtasks-' + task.id + '" class="mt-3" >';
                if (task.subtasks && task.subtasks.length > 0) {
                    task.subtasks.forEach(function (subtask) {
                        $subList += generateNestedLists(subtask);
                    });
                }
                $subList += '</ol>';

                $listItem += $subList

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
                $('#subtasks-' + ID).append(stub(randomID, true))
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
                        $(".checklist-name").html('<a href="' + checklist.url + '" target="_blank">' + checklist.url + '</a>')
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

            function getRandomInt(max) {
                return Math.floor(Math.random() * max);
            }

            function stub(id, parent = false) {
                let save = ''
                if (parent) {
                    save = '<button class="btn btn-sm btn-success save-new-task"><i class="fa fa-save"></i></button>'
                }
                return '<li data-id="' + id + '" class="default">' +
                    '    <input type="text" class="form form-control hide-border d-inline" data-type="name" value="Без названия" data-target="' + id + '" style="width: 350px">' +
                    '    <div class="tools d-flex" style="float: right">' +
                    '       <input class="form form-control hide-border" data-type="deadline" type="datetime-local" data-target="' + id + '">' +
                    '                <select data-id="status-' + id + '" data-target="' + id + '" class="custom custom-select" data-type="status">' +
                    '                    <option value="in_work" selected>В работе</option>' +
                    '                    <option value="ready">Готово</option>' +
                    '                    <option value="expired">Просрочено</option>' +
                    '                </select>' +
                    ' <div class="btn-group pl-2">' +
                    save +
                    '           <button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-description-' + id + '" role="button" aria-expanded="false" aria-controls="collapse-description-' + id + '"><i class="fa fa-eye"></i></button>' +
                    '           <button class="btn btn-sm btn-default add-new-pre-subtask" data-id="' + id + '"><i class="fa fa-plus"></i></button>' +
                    '           <button class="btn btn-sm btn-default remove-pre-task"><i class="fa fa-trash"></i></button>' +
                    '       </div>' +
                    '    </div>' +
                    '</li>' +
                    '<div class="collapse" id="collapse-description-' + id + '">' +
                    '    <div class="card card-body"><textarea class="pre-description" data-id="' + id + '"></textarea></div>' +
                    '</div>' +
                    '<ol id="subtasks-' + id + '" class="mt-3" ></ol>'
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
        </script>
    @endslot
@endcomponent
