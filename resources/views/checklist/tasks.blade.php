@component('component.card', ['title' =>  "SEO чеклист: проект $host"])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/keyword-generator/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            .datetime-counter {
                width: 75px;
            }

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

            #tasks li, #new-tasks li, #new-sub-tasks li, #stubs li, .stubs > .example {
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

            #tasks li.new, #new-sub-tasks.new {
                border-color: #007bff !important;
            }

            #tasks li.ready, #new-sub-tasks.ready {
                border-color: #8bc63e !important;
            }

            #tasks li.expired, #new-sub-tasks.expired {
                border-color: #f05d22 !important;
            }

            #tasks li.in_work, #new-sub-tasks.in_work {
                border-color: #1ccfc9 !important;
            }

            #tasks li.default,
            #new-sub-tasks.default,
            .stubs > .example {
                border-color: #5a6268 !important;
            }

            #tasks li:hover, #new-sub-tasks li:hover {
                cursor: pointer;
                box-shadow: 0 0 10px grey;
            }

            #tasks, #new-sub-tasks {
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
        <style>
            .callout a:hover {
                color: #007bff !important;
            }

            .stub-style {
                width: 85px;
                height: 20px;
                font-size: 1rem;
                letter-spacing: 0;
                float: left
            }

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

            .card ol {
                list-style: none;
                counter-reset: li;
            }

            .card-header::after {
                display: none;
            }

            .icon {
                width: 20px;
                height: 20px;
            }

            #tasks li, .stubs > .example,
            #stubs li, .stubs > .example {
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

            #tasks li.ready,
            #stubs li.ready {
                border-color: #8bc63e !important;
            }

            #tasks li.expired, #stubs li.expired {
                border-color: #f05d22 !important;
            }

            #tasks li.in_work, #stubs li.in_work {
                border-color: #1ccfc9 !important;
            }

            #tasks li.default, .stubs > .default,
            #stubs li.default, .stubs > .default {
                border-color: #5a6268 !important;
            }

            #tasks li:hover
            #stubs li:hover {
                cursor: pointer;
                box-shadow: 0 0 10px grey;
            }

            #tasks, #stubs {
                padding-left: 0;
                padding-right: 10px;
                padding-top: 10px;
                overflow: auto;
            }

            .accordion.stubs.card.card-body {
                cursor: pointer;
            }

            #tasks .custom.custom-select {
                width: 100px;
            }

            .hide-border {
                border: none;
            }

            .hide-border:active, .hide-border:focus {
                border: 1px solid #ced4da !important;
            }

            .hide-li {
                display: block;
                width: 15px;
                height: 15px;
                background: url("/img/down_arrow.svg");
                float: left;
                transition: 0.5s ease;
                transform: rotate(0deg);
            }

            .rotate {
                transform: rotate(180deg);
            }

            div > div > div.modal.note-modal.show > div > div > div.modal-header > button,
            div > div > div.modal.note-modal.link-dialog.show > div > div > div.modal-header > button,
            div > div > div.modal.note-modal.show > div > div > div.modal-header > button {
                display: none;
            }

            .default.example {
                height: 38px;
            }
        </style>
    @endslot

    <div id="project-info" class="d-flex justify-content-between w-100">
        <div class="d-flex row align-items-center">
            <div>
                <span id="checklist-icon"></span>
            </div>
            <div>
                <span id="checklist-name"></span>
            </div>
        </div>
        <div>
            Количество задач:
            <span id="checklist-counter"></span>
        </div>
        <div>
            Новые:
            <span id="checklist-new"></span>
        </div>
        <div>
            В работе:
            <span id="checklist-work"></span>
        </div>
        <div>
            Готовые:
            <span id="checklist-ready"></span>
        </div>
        <div>
            Просроченые:
            <span id="checklist-expired"></span>
        </div>
        <div>
            Отложенные:
            <span id="checklist-inactive"></span>
        </div>
        <div>
            Повторяющиеся:
            <span id="checklist-repeat"></span>
        </div>
        <div>
            <a href="{{ route('checklist') }}">Вернутся к списку проектов</a>
        </div>
    </div>

    <div class="d-flex align-items-baseline">
        <h3>Метки проекта:</h3>
        <ol class="d-flex" id="project-labels" style="padding-left: 10px;">
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
            <div class="d-flex col-8">
                <div class="form-group mr-3">
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
                <div class="form-group mr-3">
                    <label for="name">Название</label>
                    <input type="text" id="name" name="name" class="form form-control">
                </div>
                <div class="form-group mr-3">
                    <label for="sort">Сортировка</label>
                    <select name="sort" id="sort" class="custom custom-select">
                        <option value="all" selected>Любые</option>
                        <option value="new-sort">Сначала новые</option>
                        <option value="old-sort">Сначала старые</option>
                        <option value="deactivated">Отложенные</option>
                        <option value="expired">Просроченные</option>
                        <option value="repeat">Повторяющиеся</option>
                        <option value="in_work">В работе</option>
                        <option value="ready">Готовые</option>
                        <option value="new">Новые</option>
                    </select>
                </div>
            </div>
            <div class="d-flex col-4 justify-content-end align-items-center">
                <button class="btn btn-secondary" data-toggle="modal"
                        data-target="#createNewProject"
                        style="height: 38px"
                        id="add-new-tasks">
                    Добавление задач к текущему проекту
                </button>
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

    <div class="modal fade" id="createNewProject" tabindex="-1" aria-labelledby="createNewProjectLabel"
         aria-hidden="true">
        <div class="modal-dialog d-flex" style="min-width: 95vw;">
            <div class="modal-content col-9 mr-2">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewProjectLabel">Добавление новых задач к
                        проекту {{ $checklist[0]['url'] }}</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
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
                        <button type="button" class="btn btn-default" id="close-create-tasks-modal"
                                data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-success" id="save-new-tasks">
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

    <div class="modal fade" id="addNewSubtasks" tabindex="-1" aria-labelledby="addNewSubtasksLabel"
         aria-hidden="true">
        <div class="modal-dialog d-flex" style="min-width: 95vw;">
            <div class="modal-content col-9 mr-2">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewSubtasksLabel">Добавление новых подзадач к
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
                                <button class="btn btn-secondary" id="add-new-sub-task">Добавить новую подзадачу
                                </button>
                            </div>
                            <div id="accordionExample">
                                <ol id="new-sub-tasks"></ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div></div>
                    <div>
                        <button type="button" class="btn btn-default" id="close-create-sub-tasks-modal"
                                data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-success" id="save-new-sub-tasks">
                            {{ __('Save') }}
                        </button>
                        <img id="new-subtasks-loader" src="/img/1485.gif"
                             style="width: 30px; height: 30px; display: none">
                    </div>
                </div>
            </div>
            <div class="modal-content col-3">
                <div class="modal-header">
                    <h5 class="modal-title">Шаблоны</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="stubs-place-2" style="overflow: auto; height: 80vh">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="set-stub-2">Применить шаблон</button>
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
        <script src="{{ asset('plugins/checklist/common.js') }}"></script>
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            $(document).on('click', '#save-new-tasks', function () {
                $('#save-new-tasks').attr('disabled', true)
                $('#loader').show(300)

                let tasks = [];
                $.each($('#new-tasks').children('li'), function () {
                    tasks.push(parseTree(($(this))))
                })

                $.ajax({
                    type: 'post',
                    url: "{{ route('update.checklist') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        tasks: tasks,
                        projectID: {{ $checklist[0]['id']}}
                    },
                    success: function (message) {
                        successMessage(message)
                        $('#close-create-tasks-modal').trigger('click')

                        $('#save-new-tasks').attr('disabled', false)
                        $('#loader').hide(300)
                        getTasks(0, true)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)

                        $('#save-new-tasks').attr('disabled', false)
                        $('#loader').hide(300)
                    }
                })
            })

            $(document).on('click', '#save-new-sub-tasks', function () {
                $('#save-new-sub-tasks').attr('disabled', true)
                $('#new-subtasks-loader').show(300)

                let subtasks = [];
                $.each($('#new-sub-tasks').children('li'), function () {
                    subtasks.push(parseTree(($(this))))
                })

                $.ajax({
                    type: 'post',
                    url: "{{ route('update.checklist') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        tasks: subtasks,
                        parentTask: targetTask,
                        projectID: {{ $checklist[0]['id'] }}
                    },
                    success: function (message) {
                        successMessage(message)
                        $('#save-new-sub-tasks').attr('disabled', false)
                        $('#new-subtasks-loader').hide(300)
                        $('#close-create-sub-tasks-modal').trigger('click')
                        getTasks(0, true)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)

                        $('#save-new-sub-tasks').attr('disabled', false)
                        $('#new-subtasks-loader').hide(300)
                    }
                })
            })

            let stubs
            $(document).on('click', '#set-stub', function () {
                setStubs('#new-tasks')
            })

            $(document).on('click', '#set-stub-2', function () {
                setStubs('#new-sub-tasks')
            })

            function setStubs(target) {
                let basicID = $('.ribbon-wrapper.ribbon-lg').parent().attr('data-id')

                if (basicID === undefined) {
                    errorMessage(['Шаблон не выбран'])
                } else {
                    $(target).html(generateTasks(JSON.parse(stubs[basicID].tree)))
                    refreshTooltips()
                }
            }

            function generateTasks(tasks) {
                let date = new Date().toISOString().slice(0, 16);
                let html = ''

                $.each(tasks, function (index, task) {
                    let id = getRandomInt(9999999)
                    task = task[0] ?? task

                    let $listItem = '<li data-id="' + id + '" class="default d-flex">' +
                        '    <input type="text" class="form form-control hide-border" data-type="name" placeholder="Без названия" data-target="' + id + '">' +
                        '    <div class="tools d-flex" style="float: right">' +
                        '        <input class="form form-control datetime-counter" type="number" step="1" value="0" min="0" data-target="' + id + '" value="0" data-toggle="tooltip" data-placement="left" title="Количество дней на выполнение">' +
                        '        <input class="form form-control datetime" value="' + date + '" data-type="start" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата начала">' +
                        '        <input class="form form-control datetime" value="' + date + '" data-type="deadline" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата окончания">' +
                        '        <select data-id="status-' + id + '" data-target="' + id + '" class="custom custom-select task-status" data-type="status" data-toggle="tooltip" data-placement="left" title="Статус задачи" style="width: 135px">' +
                        '            <option value="new" selected>Новая</option>' +
                        '            <option value="in_work">В работе</option>' +
                        '            <option value="ready">Готово</option>' +
                        '            <option value="expired">Просрочено</option>' +
                        '            <option value="deactivated">Отложенная</option>' +
                        '        </select>' +
                        '        <input class="form form-control deactivated" data-type="active_after" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Сделать задачу активной после:">' +
                        '        <div class="btn-group pl-2">' +
                        '            <button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-description-' + id + '" role="button" aria-expanded="false" aria-controls="collapse-description-' + id + '"><i class="fa fa-eye"></i></button>' +
                        '            <button class="btn btn-sm btn-default add-new-pre-subtask" data-id="' + id + '"><i class="fa fa-plus"></i></button>' +
                        '            <button class="btn btn-sm btn-default remove-pre-task"><i class="fa fa-trash"></i></button>' +
                        '        </div>' +
                        '    </div>' +
                        '</li>' +
                        '<div class="collapse" id="collapse-description-' + id + '">' +
                        '    <div class="card card-body"><textarea class="pre-description" data-id="' + id + '"></textarea></div>' +
                        '</div>'

                    let $subList = '<ol id="subtasks-' + id + '" class="mt-3">';

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

                getStubs('#stubs-place')
            })

            let targetTask
            $(document).on('click', '.add-new-subtasks', function () {
                targetTask = $(this).attr('data-id')
                if ($('#new-sub-tasks').html() === '') {
                    $('#add-new-sub-task').trigger('click')
                }

                getStubs('#stubs-place-2')
            })

            $(document).on('click', '#add-new-sub-task', function () {
                let id = getRandomInt(99999)
                $('#new-sub-tasks').append(stub(id))
                refreshTooltips()

                $('.pre-description').summernote({
                    minHeight: 350,
                    lang: "ru-RU"
                })
            })

            function getStubs(target) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('checklist.stubs') }}",
                    data: {
                        labelID: labelID,
                        checkListID: {{ $checklist[0]['id'] }}
                    },
                    success: function (response) {
                        stubs = response
                        renderStubs(response, target)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            }

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
                refreshTooltips()
            })

            function generateNestedStubs(stubs, each = true) {
                let $listItem = ''

                if (each) {
                    $.each(stubs, function (k, stub) {
                        stub = stub[0] ?? stub
                        $listItem +=
                            ' <li class="default example">' +
                            '     <div>' +
                            '         <span class="stub-style text-muted">' +
                            '             Название' +
                            '         </span>' +
                            '     </div>' +
                            ' </li>'

                        let $subList = '<ol class="accordion stubs">';
                        if (stub.subtasks && stub.subtasks.length > 0) {
                            stub.subtasks.forEach(function (subtask) {
                                $subList += generateNestedStubs(subtask, true);
                            });
                        }

                        $subList += '</ol>';
                        $listItem += $subList
                    });
                } else {
                    $listItem +=
                        ' <li class="default example">' +
                        '     <div>' +
                        '         <span class="stub-style text-muted">' +
                        '             Название' +
                        '         </span>' +
                        '     </div>' +
                        ' </li>'

                    let $subList = '<ol class="accordion stubs">';
                    if (stubs.subtasks && stubs.subtasks.length > 0) {
                        stubs.subtasks.forEach(function (subtask) {
                            $subList += generateNestedStubs(subtask, false);
                        });
                    }

                    $subList += '</ol>';
                    $listItem += $subList
                }

                return $listItem
            }

            function generateNestedLists(task) {
                console.log(task)
                let newState = '<option value="new">Новая</option>'
                let work = '<option value="in_work">В работе</option>'
                let ready = '<option value="ready">Готово</option>'
                let expired = '<option value="expired">Просрочено</option>'
                let deactivated = '<option value="deactivated">Отложенная</option>'
                let repeat = '<option value="repeat">Повторяющаяся</option>'

                if (task.status === 'new') {
                    newState = '<option value="new" selected>Новая</option>'
                } else if (task.status === 'in_work') {
                    work = '<option value="in_work" selected>В работе</option>'
                } else if (task.status === 'ready') {
                    ready = '<option value="ready" selected>Готово</option>'
                } else if (task.status === 'expired') {
                    expired = '<option value="expired" selected>Просрочено</option>'
                } else if (task.status === 'deactivated') {
                    deactivated = '<option value="deactivated" selected>Отложенная</option>'
                } else if (task.status === 'repeat') {
                    repeat = '<option value="repeat" selected>Повторяющаяся</option>'
                }

                let html
                let start = new Date(task.date_start).toISOString().slice(0, 16);
                let end = new Date(task.deadline).toISOString().slice(0, 16);

                if (task.status === 'repeat') {
                    if (task.weekends) {
                        html = '<select class="custom custom-select" data-target="' + task.id + '" data-type="weekends" data-toggle="tooltip" data-placement="left" title="Учитывать выходные дни?">' +
                            '       <option value="1" selected>Да</option>' +
                            '       <option value="0">Нет</option>' +
                            '</select>'
                    } else {
                        html = '<select class="custom custom-select" data-target="' + task.id + '" data-type="weekends" data-toggle="tooltip" data-placement="left" title="Учитывать выходные дни?">' +
                            '       <option value="1">Да</option>' +
                            '       <option value="0" selected>Нет</option>' +
                            '</select>'
                    }

                    html += '<input class="form form-control datetime-repeat-counter" type="number" step="1" min="1" data-target="' + task.id + '" data-type="repeat_after" value="1" data-toggle="tooltip" data-placement="left" title="Повторять каждые N дней" style="width: 55px">'
                    html += '<input class="form form-control datetime-counter" type="number" step="1" value="' + task.repeat_every + '" min="0" data-target="' + task.id + '" data-toggle="tooltip" data-placement="left" title="Количество дней на выполнение" style="width: 75px;">'
                    html += '<input class="form form-control hide-border edit-checklist" data-type="date_start" type="datetime-local" data-target="' + task.id + '" value="' + start + '" data-toggle="tooltip" data-placement="top" title="Дата следующего запуска задачи">'
                } else {
                    html = '<input class="form form-control hide-border edit-checklist" data-type="date_start" type="datetime-local" data-target="' + task.id + '" value="' + start + '" ' +
                        'data-toggle="tooltip" data-placement="top" title="Дата начала">' +
                        '<input class="form form-control hide-border edit-checklist" data-type="deadline" type="datetime-local" data-target="' + task.id + '" value="' + end + '" ' +
                        'data-toggle="tooltip" data-placement="top" title="Дата окончания">'
                }

                let button = ''
                let $listItem =
                    '<li data-id="' + task.id + '" class="' + task.status + '" class="d-flex">' + button +
                    '    <input type="text" class="form form-control hide-border d-inline edit-checklist w-auto" data-type="name" data-target="' + task.id + '" value="' + task.name + '">' +
                    '    <div class="tools d-flex" style="float: right">' +
                    '       <select data-id="status-' + task.id + '" data-target="' + task.id + '" class="custom custom-select edit-checklist" data-type="status" style="width: 170px">' +
                    newState +
                    work +
                    ready +
                    expired +
                    deactivated +
                    repeat +
                    '       </select>' +
                    html +
                    '       <div class="btn-group pl-2">' +
                    '           <button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-description-' + task.id + '" role="button" aria-expanded="false" aria-controls="collapse-description-' + task.id + '"><i class="fa fa-eye"></i></button>' +
                    '           <button class="btn btn-sm btn-default add-new-subtasks" data-toggle="modal" data-target="#addNewSubtasks" data-id="' + task.id + '"><i class="fa fa-plus"></i></button>' +
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
                    lang: "ru-RU"
                });

                $('.pre-description').summernote({
                    minHeight: 350,
                    lang: "ru-RU"
                })
            }

            $(document).on('change', '.edit-checklist', function () {
                let targetBlock = $(this)
                let type = $(this).attr('data-type')
                let val = $(this).val()
                let $id = $(this).attr('data-target')

                $.ajax({
                    type: 'post',
                    url: "{{ route('edit.checklist.task') }}",
                    data: {
                        id: $id,
                        type: type,
                        value: val,
                    },
                    success: function (response) {
                        if (type === 'status') {
                            let parent = targetBlock.parents().eq(1)
                            parent.removeClass()
                            parent.addClass(val)

                            getTasks($('.page-item.active > a').attr('data-id'), false)
                        }

                        if (response.newStatus === 'expired') {
                            $("select[data-target='" + $id + "']").find('option[value="expired"]').prop('selected', true);
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
                refreshTooltips()
            })

            $(document).on('click', '.add-new-subtask', function () {
                let ID = $(this).attr('data-id')
                let randomID = getRandomInt(999999)

                $('#add-new-task').attr('disabled', true)
                $('.add-new-subtask').hide(300)
                $('#subtasks-' + ID).append(stub(randomID))
                refreshTooltips()
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
                        id: {{ $checklist[0]['id'] }},
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

            const count_ml_in_day = 86400000
            let lastStartDate
            let lastEndDate

            $(document).on('click', '.datetime[data-type="start"]', function () {
                lastStartDate = $(this).val()
            })

            $(document).on('click', '.datetime[data-type="deadline"]', function () {
                lastEndDate = $(this).val()
            })

            $(document).on('input', '.datetime', function () {
                let $id = $(this).attr('data-target')
                let status = $('select[data-id="status-' + $id + '"]').val()

                if (status === 'repeat' || status === 'deactivated') {
                    return;
                }

                let $start = $('.datetime[data-type="start"][data-target="' + $id + '"]')
                let $startDate = new Date($start.val());

                let $deadline = $('.datetime[data-type="deadline"][data-target="' + $id + '"]')
                let $endDate = new Date($deadline.val())

                let countDays = ($endDate - $startDate) / count_ml_in_day

                if ($(this).attr('data-type') === 'start' && countDays < 0) {
                    $start.val(lastStartDate)
                    errorMessage(['Дата начала должна быть раньше даты окончания'])
                } else if ($(this).attr('data-type') === 'deadline' && countDays < 0) {
                    $deadline.val(lastEndDate)
                    errorMessage(['Дата окончания не может быть раньше даты начала'])
                } else {
                    $('.datetime-counter[data-target="' + $id + '"]').val(Math.round(countDays))
                }
            })

            $(document).on('input', '.datetime-counter', function () {
                let $id = $(this).attr('data-target')
                let value = $(this).val()

                let $start = $('.datetime[data-type="start"][data-target="' + $id + '"]')
                let $deadline = $('.datetime[data-type="deadline"][data-target="' + $id + '"]')
                let newDate = new Date(new Date($start.val()).getTime() + (value * count_ml_in_day) + 10800000).toISOString().slice(0, 16)

                $deadline.val(newDate)
            })

            $(document).on('input', '.datetime-after', function () {
                let $id = $(this).attr('data-target')
                let value = $(this).val()

                let $days = $('.datetime-counter[data-target="' + $id + '"]')
                let $start = $('.datetime[data-type="start"][data-target="' + $id + '"]')
                let $deadline = $('.datetime[data-type="deadline"][data-target="' + $id + '"]')

                $start.val(value)

                let newDate = new Date(new Date(value).getTime() + ($days.val() * count_ml_in_day) + 10800000).toISOString().slice(0, 16)
                $deadline.val(newDate)
            })

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
                        id: {{ $checklist[0]['id'] }},
                        sort: $('#sort').val(),
                        count: $('#count').val(),
                        search: $('#name').val(),
                        skip: page * $('#count').val(),
                    },
                    success: function (response) {
                        console.log(response)
                        let checklist = response.checklist[0]

                        $("#checklist-icon").html('<img src="/storage/' + checklist.icon + '" alt="' + checklist.icon + '" class="icon mr-2">')
                        $("#checklist-name").html('<a href="' + checklist.url + '" target="_blank" data-toggle="tooltip" data-placement="top" title="' + checklist.url + '">' + new URL(checklist.url)['host'] + '</a>')
                        $("#checklist-counter").html(checklist.tasks.length)
                        $("#checklist-new").html(checklist.new)
                        $("#checklist-work").html(checklist.work)
                        $("#checklist-inactive").html(checklist.inactive)
                        $("#checklist-expired").html(checklist.expired)
                        $("#checklist-ready").html(checklist.ready)
                        $("#checklist-repeat").html(checklist.repeat)

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

            function renderStubs(tasks, target) {
                let html = ''

                $.each(tasks, function (index, task) {
                    let button = '<button class="btn btn-sm btn-default" data-toggle="collapse" href="#collapse-example-' + index + '" aria-expanded="false" aria-controls="collapse-example-' + index + '" id="heading-example' + index + '"><i class="fa fa-eye"></i></button>'
                    let stubType = ''
                    if (task.type === 'personal') {
                        stubType = '(личный шаблон)'
                        button += '<button class="btn btn-sm btn-default remove-stub" data-id="' + task.id + '"><i class="fa fa-trash"></i></button>'
                    } else {
                        stubType = '(базовый шаблон)'
                    }

                    html += '<ol class="card pl-0">' +
                        '    <p class="card-header">' +
                        '        <span class="d-flex justify-content-between">' +
                        '            <span>' + task.name + '</span>' +
                        '            <span>' + stubType + '</span>' +
                        '            <span>' + button + '</span>' +
                        '        </span>' +
                        '    </p>' +
                        '    <div id="collapse-example-' + index + '" aria-labelledby="heading-example" class="collapse" style="">' +
                        '    <div class="accordion stubs card-body" data-id="' + index + '">'
                    html += generateNestedStubs(JSON.parse(task.tree), true)
                    html += '</div>' + '</div>' + '</ol>'
                });

                $(target).html(html)
            }

            function getRandomInt(max) {
                return Math.floor(Math.random() * max);
            }

            $(document).on('change', '.task-status', function () {
                let $id = $(this).attr('data-target')
                refreshTooltips()

                if ($(this).val() === 'deactivated') {
                    $('.deactivated[data-target="' + $id + '"]').show()
                    $('.datetime[data-target="' + $id + '"][data-type="start"]').hide()
                    $('.datetime[data-target="' + $id + '"][data-type="deadline"]').hide()
                    $('.datetime-repeat-counter[data-target="' + $id + '"]').hide()
                    $('select[data-type="weekends"][data-target="' + $id + '"]').hide()
                } else if ($(this).val() === 'repeat') {
                    $('.datetime-repeat-counter[data-target="' + $id + '"]').show()
                    $('.datetime[data-target="' + $id + '"][data-type="deadline"]').hide()
                    $('.datetime-repeat-counter[data-type="weekends"][data-target="' + $id + '"]').show()
                    $('select[data-type="weekends"][data-target="' + $id + '"]').show()
                } else {
                    $('.deactivated[data-target="' + $id + '"]').hide()
                    $('.datetime[data-target="' + $id + '"][data-type="deadline"]').show()
                    $('.datetime[data-target="' + $id + '"][data-type="start"]').show()
                    $('.datetime-repeat-counter[data-target="' + $id + '"]').hide()
                    $('select[data-type="weekends"][data-target="' + $id + '"]').hide()
                }
            })

            function stub(id) {
                let date = new Date().toISOString().slice(0, 16);

                return '<li data-id="' + id + '" class="default d-flex">' +
                    '    <input type="text" class="form form-control hide-border" data-type="name" placeholder="Без названия" data-target="' + id + '">' +
                    '    <select data-id="status-' + id + '" data-target="' + id + '" class="custom custom-select task-status" data-type="status" style="width: 160px" data-toggle="tooltip" data-placement="left" title="Статус задачи">' +
                    '        <option value="new" selected>Новая</option>' +
                    '        <option value="in_work">В работе</option>' +
                    '        <option value="ready">Готово</option>' +
                    '        <option value="expired">Просрочено</option>' +
                    '        <option value="deactivated">Отложенная</option>' +
                    '        <option value="repeat">Повторяющаяся</option>' +
                    '    </select>' +
                    '    <select class="custom custom-select" data-target="' + id + '" data-type="weekends" data-toggle="tooltip" data-placement="left" title="Учитывать выходные дни?" style="width: 61px; display: none">' +
                    '           <option value="1">Да</option>' +
                    '           <option value="0">Нет</option>' +
                    '    </select>' +
                    '    <div class="tools d-flex" style="float: right">' +
                    '        <input class="form form-control datetime-repeat-counter" type="number" step="1" min="1" data-target="' + id + '" data-type="repeat_after" value="1" data-toggle="tooltip" data-placement="left" title="Повторять каждые N дней" style="width: 55px; display: none">' +
                    '        <input class="form form-control datetime-counter" type="number" step="1" value="0" min="0" data-target="' + id + '" value="0" data-toggle="tooltip" data-placement="left" title="Количество дней на выполнение">' +
                    '        <input class="form form-control datetime" value="' + date + '" data-type="start" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата начала">' +
                    '        <input class="form form-control datetime" value="' + date + '" data-type="deadline" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата окончания">' +
                    '        <input class="form form-control deactivated datetime-after" data-type="active_after" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Сделать задачу активной после:" style="display: none">' +
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

                if (response.paginate > 1) {
                    for (let i = 0; i < response.paginate; i++) {
                        let html = i + 1

                        if (i === 0) {
                            pagination += '<li class="page-item active"><a href="#" class="page-link" data-id="' + i + '">' + html + '</a></li>'
                        } else {
                            pagination += '<li class="page-item"><a href="#" class="page-link" data-id="' + i + '">' + html + '</a></li>'
                        }
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
                        $('#closeRemoveRelationModal').trigger('click')
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })


            $('#tasks').on('click', 'li', function (event) {
                if (!$(event.target).is('input, a, div, select')) {
                    let taskId = $(this).attr('data-id');
                    let subtasks = $(this).siblings('#subtasks-' + taskId);
                    if (subtasks.length > 0) {
                        if (subtasks.is(':visible')) {
                            subtasks.slideUp();
                        } else {
                            subtasks.slideDown();
                        }
                    }
                }
            })

            $('#tasks').on('click', '#tasks li input,#tasks li select, #tasks li a, #tasks li div', function (event) {
                event.stopPropagation();
            })
        </script>
    @endslot
@endcomponent
