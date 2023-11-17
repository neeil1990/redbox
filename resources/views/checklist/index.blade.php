@component('component.card', ['title' =>  __('SEO Checklist') ])
    @slot('css')

        <link rel="stylesheet" href="{{ asset('plugins/keyword-generator/css/style.css') }}">

        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
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

            .datetime-counter {
                width: 75px;
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
                max-height: 460px;
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
        </style>
    @endslot

    <div id="block-from-notifications"></div>

    <div class="col-12">
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
                           href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                           aria-selected="false">Активные</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="archived-checklists" data-toggle="pill"
                           href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile"
                           aria-selected="false">Архив</a>
                    </li>
                    @if(\App\User::isUserAdmin())
                        <li class="nav-item">
                            <a class="nav-link" id="classic-stubs" data-toggle="pill"
                               href="#classic-tabs-stub" role="tab" aria-controls="classic-tabs-stub"
                               aria-selected="false">Базовые шаблоны</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" id="personal-stubs" data-toggle="pill"
                           href="#personal-tabs-stub" role="tab" aria-controls="personal-tabs-stub"
                           aria-selected="false">Личные шаблоны</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" id="notification" data-toggle="pill"
                           href="#notification-tab" role="tab" aria-controls="notification-tab"
                           aria-selected="false">
                            Уведомления
                            <span style="display: none" id="notification-counter"
                                  class="ml-2 badge badge-danger">0</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel"
                         aria-labelledby="custom-tabs-three-home-tab">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Фильтры
                                </h3>
                            </div>
                            <div class="card-body row">
                                <div class="d-flex col-6 align-items-center"
                                     style="margin-top: 10px;">
                                    <button class="btn btn-secondary relevance-star mr-1" data-toggle="modal"
                                            data-target="#exampleModal">
                                        <i class="fa-regular fa-star"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Добавить проекты из анализа релевантности"></i>
                                    </button>
                                    <button class="btn btn-secondary position-star mr-1" data-toggle="modal"
                                            data-target="#exampleModal">
                                        <i class="fas fa-chart-line" data-toggle="tooltip" data-placement="top"
                                           title="Добавить проекты из мониторинга позиций"></i>
                                    </button>
                                    <button class="btn btn-secondary metatag-star mr-1" data-toggle="modal"
                                            data-target="#exampleModal">
                                        <i class="fas fa-heading" data-toggle="tooltip" data-placement="top"
                                           title="Добавить проекты из мониторинга метатегов"></i>
                                    </button>
                                    <button class="btn btn-secondary domain-monitoring-star mr-2" data-toggle="modal"
                                            data-target="#exampleModal">
                                        <i class="fas fa-edit" data-toggle="tooltip" data-placement="top"
                                           title="Добавить проекты из мониторинга сайтов"></i>
                                    </button>

                                    <button type="button" class="btn btn-secondary mr-1" data-toggle="modal"
                                            data-target="#modalLabel">
                                        Управление метками
                                    </button>

                                    <button class="btn btn-secondary mr-1" data-toggle="modal"
                                            data-target="#createNewProject"
                                            id="add-new-checklist">
                                        Добавить проект
                                    </button>

                                    <button id="create-new-stub" class="btn btn-secondary" data-toggle="modal"
                                            data-target="#createNewSTub">
                                        Добавить шаблон
                                    </button>
                                </div>
                                <div class="form-group col-2">
                                    <label for="count">Количество проектов</label>
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
                                    <label for="name">URL проекта</label>
                                    <input type="text" id="name" name="name" class="form form-control">
                                </div>
                                <div class="form-group col-2">
                                    <label for="tags">Фильтр по меткам</label>
                                    <input type="text" id="tags" name="tags" class="form form-control">
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Добавление проектов</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" id="place-from-projects"></div>
                                    <div class="modal-footer">
                                        <img id="multiple-loader" src="/img/1485.gif"
                                             style="width: 30px; height: 30px; display: none">
                                        <button type="button" class="btn btn-secondary" id="add-multiply-projects">
                                            Добавить
                                        </button>
                                        <button type="button" class="btn btn-default" id="close-multiply-projects"
                                                data-dismiss="modal">
                                            Закрыть
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="lists" class="row d-flex"></div>

                        <p id="empty-message" style="display: none">У вас нет активных проектов</p>
                        <ul class="pagination d-flex justify-content-end w-100" id="pagination"></ul>
                    </div>
                    <div class="tab-pane fade row d-flex" id="custom-tabs-three-profile" role="tabpanel"
                         aria-labelledby="archived-checklists"></div>
                    @if(\App\User::isUserAdmin())
                        <div class="tab-pane fade" id="classic-tabs-stub" role="tabpanel"
                             aria-labelledby="classic-stubs">
                            <div id="classic-stubs-place" class="d-flex row"></div>
                        </div>
                    @endif
                    <div class="tab-pane fade" id="personal-tabs-stub" role="tabpanel"
                         aria-labelledby="personal-stubs">
                        <div id="personal-stubs-place" class="d-flex row"></div>
                    </div>
                    <div class="tab-pane fade" id="notification-tab" role="tabpanel">
                    </div>
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

    <div class="modal fade" id="createNewProject" tabindex="-1" aria-labelledby="createNewProjectLabel"
         aria-hidden="true">
        <div class="modal-dialog d-flex" style="min-width: 95vw;">
            <div class="modal-content col-9 mr-2">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewProjectLabel">Добавление нового проекта</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body d-flex">
                    <div class="col-12">

                        <div class="mb-3">
                            <label for="save-stub">Тип шаблона</label>
                            <select name="save-stub" id="save-stub" class="custom-select">
                                <option value="no" selected>Не сохранять шаблон</option>
                                <option value="personal">Личный шаблон</option>
                                @if(\App\User::isUserAdmin())
                                    <option value="classic">Базовый шаблон</option>
                                    <option value="all">Базовый и личный шаблон</option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-3" style="display: none">
                            <label for="dynamic-stub">Динамичный шаблон</label>
                            <span class="__helper-link ui_tooltip_w">
                                <i class="fa fa-question-circle" style="color: grey"></i>
                                <span class="ui_tooltip __bottom">
                                    <span class="ui_tooltip_content" style="width: 300px">
                                        Если шаблон динамичный, то он будет иметь такую же структуру задач, которую имеет ваш проект
                                    </span>
                                </span>
                            </span>
                            <select name="dynamic-stub" id="dynamic-stub" class="custom-select">
                                <option value="1">Да</option>
                                <option value="0" selected>Нет</option>
                            </select>
                        </div>

                        <div class="form-group block-from-hide mb-3">
                            <label for="url">Ссылка</label>
                            <input type="text" name="url" id="url" class="form form-control"
                                   placeholder="https://example.com или example.com">
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="tasks">Задачи</label>
                                <button class="btn btn-secondary" id="add-new-task">Добавить задачу</button>
                            </div>
                            <div id="accordionExample">
                                <ol id="tasks"></ol>
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
                <div class="modal-body" id="stubs-place" style="overflow: auto;">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="set-stub">Применить шаблон</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createNewSTub" tabindex="-1" aria-labelledby="createNewSTubLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg d-flex">
            <div class="modal-content col-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewSTubLabel">Добавление нового шаблона</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body d-flex">
                    <div class="col-12">
                        <div class="form-group block-from-hide">
                            <label for="save-stub-action">Выбор сохранения</label>
                            <select name="save-stub-action" id="save-stub-action" class="custom-select">
                                <option value="personal">Сохранить как личный шаблон</option>
                                @if(\App\User::isUserAdmin())
                                    <option value="classic" selected>Сохранить как базовый шаблон</option>
                                    <option value="all">Сохранить как базовый и личный шаблон</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <label for="stubs">Настройте ваш шаблон:</label>
                                <button class="btn btn-secondary" id="add-new-stub">Добавить</button>
                            </div>
                            <div id="accordionExample">
                                <ol id="stubs"></ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-success" id="save-new-stubs">
                            {{ __('Save') }}
                        </button>
                        <img id="loader-stubs" src="/img/1485.gif" style="width: 30px; height: 30px; display: none">
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

    <div class="modal fade" id="modalLabel" tabindex="-1" role="dialog" aria-labelledby="modalLabelLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a href="#add-to-project" data-toggle="tab" class="nav-link active">
                                Добавить метку к проекту
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#managing" data-toggle="tab" class="nav-link">Мои метки</a>
                        </li>
                    </ul>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div id="add-to-project" class="tab-pane active">
                                <label for="checklist-select">Ваши чеклисты</label>
                                <select name="checklist-select" id="checklist-select" class="form form-control mb-3">

                                </select>

                                <label for="labels">Ваши метки</label>
                                <select name="labels" id="labels" class="form form-control">
                                    @foreach($labels as $label)
                                        <option value="{{ $label->id }}" id="option-tag-{{ $label->id }}"
                                                style="color: {{ $label->color }}">
                                            {{ $label->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-3 d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary mr-1" id="create-new-relations">
                                        Сохранить
                                    </button>
                                    <button type="button" data-dismiss="modal" class="btn btn-default">
                                        Закрыть
                                    </button>
                                </div>
                            </div>
                            <div id="managing" class="tab-pane">
                                <div class="mb-3">
                                    <label>Ваши созданные метки:</label>
                                    <ul id="labels-list" class="mt-3" style="list-style: none; padding-left: 0;">
                                        @foreach($labels as $label)
                                            <li>
                                                <div class="btn-group mb-2">
                                                    <input type="color" data-target="{{ $label->id }}"
                                                           value="{{ $label->color }}"
                                                           class="label-color-input"
                                                           style="height: 37px;">
                                                    <input type="text" data-target="{{ $label->id }}"
                                                           value="{{ $label->name }}"
                                                           class="form form-control w-100 label-name-input d-inline"
                                                           style="display: inline !important;">
                                                    <button type="button" data-target="{{ $label->id }}"
                                                            class="btn btn-secondary col-2 remove-label"><i
                                                            class="fa fa-trash text-white"></i></button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="border-top">
                                    <label class="mt-3">Добавить новую метку</label>
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" id="label-name" name="label-name"
                                                   placeholder="Название метки"
                                                   class="form form-control">
                                            <input type="color" name="label-color" id="label-color"
                                                   style="height: 38px;">
                                        </div>
                                    </div>
                                </div>
                                <button id="create-label" class="btn btn-secondary">Создать метку</button>
                                <button type="button" data-dismiss="modal" class="btn btn-default">Закрыть</button>
                            </div>
                        </div>
                    </div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            id="closeRemoveRelationModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Выберите, что вы хотите сделать
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button"
                            class="btn btn-primary" id="add-in-filter">{{ __('Search label') }}</button>
                    <button type="button" class="btn btn-danger"
                            id="removeRelation">{{ __('Remove label') }}</button>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            $(function () {
                if (localStorage.getItem('SEO_CHECKLIST_COUNT') !== null) {
                    $('#count').val(localStorage.getItem('SEO_CHECKLIST_COUNT'))
                }
                loadChecklists(0, true)
            })

            let editedID
            let editedTimeout
            let notificationBlocks = 0
            let labelID;
            let checkListID;
            let removedLI;
            let rotateButton
            let removedChecklist
            let removedButton
            let counter = 1;
            let subTaskCounter = 1;
            let lastDownloadedChecklistID

            $('#create-label').on('click', function () {
                let name = $('#label-name').val()

                if (name === '') {
                    errorMessage(['Название метки не может быть пустым'])
                } else {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('create.label') }}",
                        data: {
                            name: name,
                            color: $('#label-color').val()
                        },
                        success: function (response) {
                            successMessage(response.message)

                            $('#labels-list').append(
                                '<li>' +
                                '    <div class="btn-group mb-2">' +
                                '        <input type="color" data-target="' + response.label.id + '" value="' + $('#label-color').val() + '" class="label-color-input" style="height: 37px;">' +
                                '        <input type="text" data-target="' + response.label.id + '" value="' + name + '" class="form form-control w-100 label-name-input d-inline"' +
                                '               style="display: inline !important;">' +
                                '            <button type="button" data-target="' + response.label.id + '" class="btn btn-secondary col-2 remove-label">' +
                                '                <i class="fa fa-trash text-white"></i>' +
                                '            </button>' +
                                '        </div>' +
                                '</li>'
                            );
                            $('#labels').append(
                                '<option value="' + response.label.id + '" id="option-tag-' + response.label.id + '" style="' + response.label.color + '">' +
                                name +
                                '</option>'
                            )

                        },
                        error: function (response) {
                            errorMessage(response.responseJSON.errors)
                        }
                    })
                }
            })

            $('#create-new-relations').on('click', function () {
                let checklistID = $('#checklist-select').val()
                let labelID = $('#labels').val()

                $.ajax({
                    type: 'post',
                    url: "{{ route('create.checklist.relation') }}",
                    data: {
                        checklistId: checklistID,
                        labelId: labelID
                    },
                    success: function (label) {
                        successMessage('Метка успешно добавлена к проекту')
                        let labelsBlock = $('.col-8[data-action="labels"][data-id="' + checklistID + '"]').children('ul').eq(0)
                        let color = $('.label-color-input[data-target="' + labelID + '"]').val()
                        let text = $('.label-name-input[data-target="' + labelID + '"]').val()

                        labelsBlock.append(
                            '<li class="checklist-label"' +
                            ' data-name="' + text + '"' +
                            ' data-target="' + checklistID + '"' +
                            ' data-id="' + labelID + '"' +
                            ' data-toggle="tooltip"' +
                            '    data-placement="top" title="' + text + '">' +
                            '         <span class="fas fa-square"' +
                            '               style="color: ' + color + ' !important;"' +
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

            $('#removeRelation').on('click', function () {
                $.ajax({
                    type: 'post',
                    url: "{{ route('remove.checklist.relation') }}",
                    data: {
                        labelID: labelID,
                        checkListID: checkListID
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

            $(document).on('click', '.remove-label', function () {
                if (confirm('Вы собираетесь удалить метку, она будет автоматически удалена у ваших чеклистов. Подтвердите действие.')) {
                    let $element = $(this)

                    $.ajax({
                        type: 'get',
                        url: '/remove-label/' + $element.attr('data-target'),
                        success: function (message) {
                            successMessage(message)
                            $('#option-tag-' + $element.attr('data-target')).remove()
                            $('.checklist-label[data-id="' + $element.attr('data-target') + '"]').remove()
                            $element.parents().eq(1).remove()
                        },
                        error: function (response) {
                            errorMessage(response.responseJSON.errors)
                        }
                    })
                }
            })

            $(document).on('change', '.label-color-input', function () {
                $.ajax({
                    type: 'post',
                    url: "{{ route('edit.label') }}",
                    data: {
                        id: $(this).attr('data-target'),
                        type: 'color',
                        target: $(this).val(),
                    },
                    success: function (message) {
                        successMessage(message)
                    }
                })
            })

            $(document).on('change', '.label-name-input', function () {
                $.ajax({
                    type: 'post',
                    url: "{{ route('edit.label') }}",
                    data: {
                        id: $(this).attr('data-target'),
                        type: 'name',
                        target: $(this).val(),
                    },
                    success: function (message) {
                        successMessage(message)
                    }
                })
            })

            let filterLabel
            $(document).on('click', '.checklist-label', function () {
                labelID = $(this).attr('data-id')
                checkListID = $(this).attr('data-target')
                removedLI = $(this)
                filterLabel = $(this).attr('data-name')
            })

            $('#add-in-filter').on('click', function () {
                $('#tags').val(filterLabel)
                loadChecklists()

                $('#closeRemoveRelationModal').trigger('click')
            })

            $(document).on('change', '.edit-checklist', function () {
                let ID = $(this).attr('data-target')

                $.ajax({
                    type: 'post',
                    url: "{{ route('edit.checklist.task') }}",
                    data: {
                        id: $(this).attr('data-target'),
                        type: $(this).attr('data-type'),
                        value: $(this).val(),
                    },
                    success: function (response) {
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

            $('#add-new-checklist').on('click', function () {
                $.ajax({
                    type: 'get',
                    url: "{{ route('checklist.stubs') }}",
                    data: {
                        labelID: labelID,
                        checkListID: checkListID
                    },
                    success: function (response) {
                        basicTasks = response
                        renderStubs(response)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })

                $('#createNewProject').addClass('d-flex')

                if ($('#tasks').children('li').length === 0) {
                    $('#add-new-task').trigger('click')
                }
            })

            $("#createNewProject").on("hidden.bs.modal", function () {
                $('#createNewProject').removeClass('d-flex')
            })

            $(document).on('click', '.remove-task', function () {
                $(this).parents().eq(3).remove()
            })

            $(document).on('click', '.rotate-checklist', function () {
                rotateButton = $(this)
            })

            $('#rotate-checklist').on('click', function () {
                $.ajax({
                    url: '/restore-checklist/' + rotateButton.attr('data-id'),
                    type: 'get',
                    success: function (message) {
                        successMessage(message)
                        $('#rotateModal > div > div > div.modal-footer > button.btn.btn-default').trigger('click')
                        rotateButton.parents().eq(4).hide(300)
                        setTimeout(() => {
                            rotateButton.parents().eq(4).remove()
                        }, 301)
                    },
                    errors: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            $(document).on('click', '.remove-checklist', function () {
                removedButton = $(this)
            })

            $(document).on('click', '.select-id', function () {
                removedButton = $(this)
            })

            $(document).on('click', '.add-subtask', function () {
                let id = $(this).attr('data-id')

                $('#subtasks-' + id).append(
                    '<li data-id="' + counter + '-' + subTaskCounter + '">' +
                    '    <div class="card">' +
                    '        <div class="card-header d-flex flex-row justify-content-between"' +
                    '             id="heading' + subTaskCounter + '">' +
                    '            <div class="d-flex w-75">' +
                    '                <div class="form-group col-4">' +
                    '                    <label>Название задачи</label>' +
                    '                    <input data-id="name-' + counter + '-' + subTaskCounter + '" type="text" class="form form-control"' +
                    '                           placeholder="Название задачи">' +
                    '                </div>' +
                    '                <div class="form-group col-4">' +
                    '                    <label>Статус</label>' +
                    '                    <select data-id="status-' + counter + '-' + subTaskCounter + '" class="custom custom-select">' +
                    '                        <option value="new">Новая</option>' +
                    '                        <option value="in_work">В работе</option>' +
                    '                        <option value="ready">Готово</option>' +
                    '                        <option value="expired">Просрочена</option>' +
                    '                    </select>' +
                    '                </div>' +
                    '                <div class="form-group col-4">' +
                    '                    <label>Дедлайн</label>' +
                    '                    <input data-id="deadline-' + counter + '-' + subTaskCounter + '" type="datetime-local" class="form form-control">' +
                    '                </div>' +
                    '            </div>' +
                    '            <div style="display: flex; justify-content: center; align-items: center; margin-top: 13px;">' +
                    '                <button class="btn btn-sm btn-default" data-toggle="collapse"' +
                    '                        data-target="#collapse' + counter + '-' + subTaskCounter + '"' +
                    '                        aria-expanded="true" aria-controls="collapse' + counter + '-' + subTaskCounter + '">' +
                    '                    <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Скрыть - Показать"></i>' +
                    '                </button>' +
                    '                <button class="btn btn-sm btn-default remove-task"' +
                    '                        data-toggle="tooltip"' +
                    '                        data-placement="top" title="Удалить">' +
                    '                    <i class="fa fa-trash"></i>' +
                    '                </button>' +
                    '            </div>' +
                    '        </div>' +
                    '        <div id="collapse' + counter + '-' + subTaskCounter + '" class="collapse" aria-labelledby="heading' + counter + '-' + subTaskCounter + '"' +
                    '             data-parent="#accordionExample' + counter + '-' + subTaskCounter + '">' +
                    '            <div class="card-body">' +
                    '                <textarea id="description-' + counter + '-' + subTaskCounter + '" cols="30" rows="10" class="form-control" placeholder="Описание"></textarea>' +
                    '            </div>' +
                    '            <div class="accordion" id="accordionExample' + counter + '-' + subTaskCounter + '">' +
                    '                <ol id="subtasks-' + counter + '-' + subTaskCounter + '"></ol>' +
                    '            </div>' +
                    '            <div class="card-footer">' +
                    '                <button class="btn btn-default add-subtask" data-id="' + counter + '-' + subTaskCounter + '">' +
                    '                    Добавить подзадачу' +
                    '                </button>' +
                    '            </div>' +
                    '        </div>' +
                    '    </div>' +
                    '</li>'
                )

                subTaskCounter++
            })

            $(document).on('click', '#archived-checklists', function () {
                $('#custom-tabs-three-profile').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5">' +
                    '    <img src="/img/1485.gif">' +
                    '</div>'
                )

                $.ajax({
                    type: 'get',
                    url: "{{ route('checklist.archive') }}",
                    success: function (lists) {
                        let cards = ''

                        if (lists.length > 0) {
                            $.each(lists, function (k, v) {
                                let labels =
                                    '<div class="col-8">' +
                                    '    <ul class="fc-color-picker">'

                                $.each(v.labels, function (index, label) {
                                    labels +=
                                        '<li class="checklist-label" data-name="' + label.name + '" data-target="' + v.id + '" data-id="' + label.id + '" ' +
                                        '    data-toggle="tooltip" data-placement="top" ' +
                                        '    title="' + label.name + '">' +
                                        '    <span class="fas fa-square" style="color: ' + label.color + ' !important;" data-toggle="modal" data-target="#removeRelationModal"></span>' +
                                        '</li>'
                                })

                                labels += '</ul></div>'

                                let totalTasks = v.ready + v.work + v.expired

                                cards +=
                                    '<div class="col-4"><div class="card">' +
                                    '    <div class="card-header">' +
                                    '        <div class="card-title d-flex justify-content-between w-100">' +
                                    '            <div class="d-flex align-items-baseline">' +
                                    '                <img src="/storage/' + v.icon + '" alt="fav icon" class="icon mr-2"> ' +
                                    '                <a href="' + v.url + '" target="_blank"' +
                                    '                    data-toggle="tooltip" data-placement="top"' +
                                    '                    title="' + v.url + '">' + new URL(v.url)['origin'] + '</a>' +
                                    '            </div>' +
                                    '            <div>' +
                                    '                <button class="btn btn-default rotate-checklist" data-id="' + v.id + '" data-toggle="modal" data-target="#rotateModal">' +
                                    '                    <i class="fa-solid fa-rotate-left" data-toggle="tooltip" data-placement="top"' +
                                    '                       title="Восстановить из архива"></i>' +
                                    '                </button>' +
                                    '            </div>' +
                                    '        </div>' +
                                    '    </div>' +
                                    '    <div class="card-body updated-font-size">' +
                                    '        <div class="d-flex">' +
                                    '            <div class="d-flex flex-column col-8">' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Всего задач:</span> <span>' + totalTasks + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Новые</span> <span>' + v.new + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">В работе:</span> <span>' + v.work + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Не активные:</span> <span>' + v.inactive + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Готовые:</span> <span>' + v.ready + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Просрочены:</span> <span>' + v.expired + '</span>' +
                                    '                </div>' +
                                    '            </div>' +
                                    '            <div class="d-flex col-4 flex-column align-items-end">' +
                                    '            </div>' +
                                    '        </div>' +
                                    '        <div class="row mt-3">' +
                                    '            <div class="col-6">'
                                    + labels +
                                    '            </div>' +
                                    '            <div class="col-6 d-flex align-items-end justify-content-end">' +
                                    '                <button class="btn btn-flat btn-secondary remove-checklist" ' +
                                    '                        data-toggle="modal"' +
                                    '                        data-target="#removeModal" data-id="' + v.id + '">' +
                                    '                            Удалить из архива' +
                                    '                 </button>' +
                                    '            </div>' +
                                    '        </div>' +
                                    '    </div>' +
                                    '</div></div>'
                            })

                            $('#custom-tabs-three-profile').html(cards)
                            refreshTooltips()
                        } else {
                            $('#custom-tabs-three-profile').html('<p>В вашем архиве ничего нет</p>')
                        }
                    },
                    error: function (response) {
                    }
                })
            })

            $(document).on('click', '#custom-tabs-three-home-tab', function () {
                loadChecklists()
            })

            $('#move-to-archive').on('click', function () {
                $.ajax({
                    url: '/move-checklist-to-archive/' + removedButton.attr('data-id'),
                    type: 'get',
                    success: function (message) {
                        successMessage(message)
                        removedButton.parents().eq(4).hide(300)
                        setTimeout(() => {
                            removedButton.parents().eq(4).remove()
                        }, 301)

                        loadChecklists($('.page-item.active > .page-link').attr('data-id'))
                    },
                    errors: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            $('#add-task').on('click', function () {
                $('#tasks').append(
                    '<li data-id="' + counter + '">' +
                    '    <div class="card">' +
                    '    <div class="card-header d-flex flex-row justify-content-between"' +
                    '         id="heading' + counter + '">' +
                    '        <div class="d-flex w-75">' +
                    '            <div class="form-group col-4">' +
                    '                <label>Название задачи</label>' +
                    '                <input data-id="name-' + counter + '" type="text" class="form form-control"' +
                    '                       placeholder="Название задачи">' +
                    '            </div>' +
                    '            <div class="form-group col-4">' +
                    '                <label>Статус</label>' +
                    '                <select data-id="status-' + counter + '" class="custom custom-select">' +
                    '                    <option value="new">Новая</option>' +
                    '                    <option value="in_work">В работе</option>' +
                    '                    <option value="expired">Просрочен</option>' +
                    '                    <option value="ready">Готово</option>' +
                    '                </select>' +
                    '            </div>' +
                    '            <div class="form-group col-4">' +
                    '                <label>Дедлайн</label>' +
                    '                <input data-id="deadline-' + counter + '" type="datetime-local" class="form form-control">' +
                    '            </div>' +
                    '        </div>' +
                    '        <div style="display: flex; justify-content: center; align-items: center; margin-top: 13px;">' +
                    '            <button class="btn btn-sm btn-default" data-toggle="collapse"' +
                    '                    data-target="#collapse' + counter + '"' +
                    '                    aria-expanded="true" aria-controls="collapse' + counter + '">' +
                    '                <i class="fa fa-eye" data-toggle="tooltip" data-placement="top"' +
                    '                   title="Скрыть - Показать"></i>' +
                    '            </button>' +
                    '            <button class="btn btn-sm btn-default remove-task"' +
                    '                    data-toggle="tooltip"' +
                    '                    data-placement="top" title="Удалить">' +
                    '                <i class="fa fa-trash"></i>' +
                    '            </button>' +
                    '        </div>' +
                    '    </div>' +
                    '    <div id="collapse' + counter + '" class="collapse" aria-labelledby="heading' + counter + '"' +
                    '         data-parent="#accordionExample">' +
                    '        <div class="card-body">' +
                    '            <textarea id="description-' + counter + '" cols="30" rows="10" class="form-control" placeholder="Описание"></textarea>' +
                    '        </div>' +
                    '        <ol id="subtasks-' + counter + '"></ol>' +
                    '        <div class="card-footer">' +
                    '            <button class="btn btn-default add-subtask" data-id="' + counter + '">' +
                    '                Добавить подзадачу' +
                    '            </button>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>' +
                    '</li>'
                )

                counter++
            })

            $('#remove').on('click', function () {
                $.ajax({
                    type: 'get',
                    url: '/remove-checklist/' + removedButton.attr('data-id'),
                    success: function (message) {
                        successMessage(message)
                        let $targetElement = removedButton.parents().eq(4)

                        $targetElement.animate({
                            width: 0,
                            opacity: 0
                        }, 1000, function () {
                            $(this).remove();
                        });

                        $('#removeModal > div > div > div.modal-footer > button.btn.btn-default').trigger('click')
                    }
                })
            })

            function parseTree($object) {
                let $dataId = $object.attr('data-id')
                let objects = []
                let $subtasks = []

                let object = {
                    name: $('input[data-target="' + $dataId + '"][data-type="name"]').val(),
                    status: $('select[data-target="' + $dataId + '"][data-type="status"]').val(),
                    description: $('.pre-description[data-id="' + $dataId + '"]').val(),
                    deadline: $('input[data-type="deadline"][data-target="' + $dataId + '"]').val(),
                    start: $('input[data-type="start"][data-target="' + $dataId + '"]').val(),
                    active_after: $('input[data-type="active_after"][data-target="' + $dataId + '"]').val(),
                }

                if ($('#subtasks-' + $dataId).children('li').length > 0) {
                    $.each($('#subtasks-' + $dataId).children('li'), function () {
                        $subtasks.push(parseTree($(this)))
                    })
                }

                object.subtasks = $subtasks
                objects.push(object)

                return objects
            }

            function errorMessage(errors, time = 5000) {
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
                }, time)
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

            $(document).on('change', '#count', function () {
                localStorage.setItem('SEO_CHECKLIST_COUNT', $(this).val())
                loadChecklists(0, true)
            })

            let searchTimeout
            $(document).on('input', '#name', function () {
                clearTimeout(searchTimeout)

                searchTimeout = setTimeout(() => {
                    loadChecklists()
                }, 600)
            })

            $(document).on('input', '#tags', function () {
                clearTimeout(searchTimeout)

                searchTimeout = setTimeout(() => {
                    loadChecklists()
                }, 600)
            })

            $(document).on('click', '.page-link', function () {
                $('.page-item.active').removeClass('active')
                $(this).parent().addClass('active')

                loadChecklists($(this).attr('data-id'), false)
            })

            function loadChecklists(page = 0, renderPaginate = false) {
                $('#custom-tabs-three-profile').html('')
                $('#lists').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5">' +
                    '    <img src="/img/1485.gif">' +
                    '</div>'
                )

                $.ajax({
                    type: 'post',
                    url: "{{ route('get.checklists') }}",
                    data: {
                        countOnPage: $('#count').val(),
                        url: $('#name').val(),
                        label_name: $('#tags').val(),
                        skip: page * $('#count').val()
                    },
                    success: function (response) {
                        renderChecklists(response.lists)
                        if (renderPaginate) {
                            renderPagination(response)
                        }
                    },
                    error: function (response) {

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

            function renderChecklists(lists) {
                let cards = ''
                let options = ''

                if (lists.length === 0) {
                    $('#empty-message').show(300)
                } else {
                    $('#empty-message').hide(300)
                }

                $.each(lists, function (k, v) {
                    let totalTasks = v.ready + v.work + v.expired + v.inactive + v.new
                    let labels =
                        '<div class="col-8" data-action="labels" data-id="' + v.id + '">' +
                        '    <ul class="fc-color-picker">'

                    $.each(v.labels, function (index, label) {
                        labels +=
                            '<li class="checklist-label" data-name="' + label.name + '" data-target="' + v.id + '" data-id="' + label.id + '" ' +
                            '    data-toggle="tooltip" data-placement="top" ' +
                            '    title="' + label.name + '">' +
                            '    <span class="fas fa-square" style="color: ' + label.color + ' !important;" data-toggle="modal" data-target="#removeRelationModal"></span>' +
                            '</li>'
                    })

                    labels += '</ul></div>'

                    options += '<option value="' + v.id + '">' + v.url + '</option>'

                    cards +=
                        '<div class="col-4"><div class="card">' +
                        '    <div class="card-header">' +
                        '        <div class="card-title d-flex justify-content-between w-100">' +
                        '            <div class="d-flex align-items-center">' +
                        '                <img src="/storage/' + v.icon + '" alt="fav icon" class="icon mr-2"> ' +
                        '                <a href="' + v.url + '" target="_blank"' +
                        '                    data-toggle="tooltip" data-placement="top" class="edited-site-' + v.id + '"' +
                        '                    title="' + v.url + '">' + new URL(v.url)['origin'] + '</a>' +
                        '            </div>' +
                        '            <div>' +
                        '                <button class="btn btn-default select-id" data-toggle="modal" data-target="#projectModal"  data-id="' + v.id + '">' +
                        '                    <i class="fa fa-trash" data-toggle="tooltip" data-placement="top"' +
                        '                       title="{{ __('Archive it') }}"></i>' +
                        '                </button>' +
                        '            </div>' +
                        '        </div>' +
                        '    </div>' +
                        '    <div class="card-body updated-font-size">' +
                        '        <div class="d-flex">' +
                        '            <div class="d-flex flex-column col-8">' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Всего задач:</span> <span>' + totalTasks + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Новые:</span> <span>' + v.new + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">В работе:</span> <span>' + v.work + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Не активные:</span> <span>' + v.inactive + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Готовые:</span> <span>' + v.ready + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Просроченые:</span> <span>' + v.expired + '</span>' +
                        '                </div>' +
                        '            </div>' +
                        '            <div class="d-flex col-4 flex-column align-items-end">' +
                        '                <div>' +
                        '                    <a target="_blank" href="{{ route('relevance.history') }}" class="text-dark" data-toggle="tooltip" data-placement="top"' +
                        '                       title="Анализ релевантности"><i class="fa-regular fa-star"></i></a>' +
                        '                </div>' +
                        '                <div style="margin-right: 1px">' +
                        '                    <a target="_blank" href="/monitoring" class="fa fa-chart-line text-dark" data-toggle="tooltip" data-placement="top"' +
                        '                       title="Мониторинг позиций"></a>' +
                        '                </div>' +
                        '                <div style="margin-right: 3px">' +
                        '                    <a target="_blank" href="/meta-tags" class="fa fa-heading text-dark" data-toggle="tooltip" data-placement="top"' +
                        '                       title="Мониторинг метатегов"></a>' +
                        '                </div>' +
                        '                <div>' +
                        '                    <a target="_blank" href="/site-monitoring" class="fa fa-edit text-dark" data-toggle="tooltip" data-placement="top"' +
                        '                       title="Мониторинг сайтов"></a>' +
                        '                </div>' +
                        '            </div>' +
                        '        </div>' +
                        '        <div class="d-flex mt-3 justify-content-between">' +
                        '            <div class="row col-7">'
                        + labels +
                        '            </div>' +
                        '            <div class="d-flex col-5 flex-column align-items-end">' +
                        '                <a class="btn btn-flat btn-secondary" href="/checklist-tasks/' + v.id + '" target="_blank">Просмотр задач</a>' +
                        '            </div>' +
                        '        </div>' +
                        '    </div>' +
                        '</div>' +
                        '</div>'
                })

                $('#lists').html(cards)
                $('#checklist-select').html(options)
                refreshTooltips()
            }

            function refreshTooltips() {
                $('[data-toggle="tooltip"]').tooltip('dispose');
                $('[data-toggle="tooltip"]').tooltip()
            }

            function getRandomInt(max) {
                return Math.floor(Math.random() * max);
            }

            $('#add-new-stub').on('click', function () {
                $('.add-new-subtask').hide(300)
                $('#stubs').append(stub(getRandomInt(999999999), true))
            })

            $('#add-new-task').on('click', function () {
                $('.add-new-subtask').hide(300)
                let id = getRandomInt(99999)
                $('#tasks').append(stub(id))

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

                refreshTooltips()
            })

            $(document).on('click', '.add-new-pre-subtask-stub', function () {
                let ID = $(this).attr('data-id')
                $('#subtasks-' + ID).append(stub(getRandomInt(999999999), true))
            });

            $(document).on('click', '.add-new-pre-subtask', function () {
                let ID = $(this).attr('data-id')
                let randomID = getRandomInt(999999)
                $('#subtasks-' + ID).append(stub(randomID))

                refreshTooltips()

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
                let newDate = new Date(new Date($start.val()).getTime() + (value * count_ml_in_day)).toISOString().slice(0, 16)

                $deadline.val(newDate)
            })

            $(document).on('change', '.task-status', function () {
                let $id = $(this).attr('data-target')

                refreshTooltips()
                if ($(this).val() === 'deactivated') {
                    $('.deactivated[data-target="' + $id + '"]').show(300)
                } else {
                    $('.deactivated[data-target="' + $id + '"]').hide()
                }
            })

            function stub(id, stub = false) {
                let date = new Date().toISOString().slice(0, 16);

                if (stub) {
                    return '<li data-id="' + id + '" class="default">' +
                        '    <span class="text-muted" style="font-size: 1rem; letter-spacing: 0">Задача №: ' + id + '</span>' +
                        '    <div class="tools d-flex" style="float: right">' +
                        '        <div class="btn-group pl-2">' +
                        '            <button class="btn btn-sm btn-default add-new-pre-subtask-stub" data-id="' + id + '"><i class="fa fa-plus"></i></button>' +
                        '            <button class="btn btn-sm btn-default remove-pre-task"><i class="fa fa-trash"></i></button>' +
                        '        </div>' +
                        '    </div>' +
                        '</li>' +
                        '<div class="collapse" id="collapse-description-' + id + '">' +
                        '    <div class="card card-body"><textarea class="pre-description" data-id="' + id + '"></textarea></div>' +
                        '</div>' +
                        '<ol id="subtasks-' + id + '" class="mt-3"></ol>'
                } else {
                    return '<li data-id="' + id + '" class="default d-flex">' +
                        '    <input type="text" class="form form-control hide-border" data-type="name" placeholder="Без названия" data-target="' + id + '">' +
                        '    <div class="tools d-flex" style="float: right">' +
                        '        <input class="form form-control datetime-counter" type="number" step="1" value="0" min="0" data-target="' + id + '" value="0" data-toggle="tooltip" data-placement="left" title="Количество дней на выполнение">' +
                        '        <input class="form form-control datetime" value="' + date + '" data-type="start" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата начала">' +
                        '        <input class="form form-control datetime" value="' + date + '" data-type="deadline" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата окончания">' +
                        '        <select data-id="status-' + id + '" data-target="' + id + '" class="custom custom-select task-status" data-type="status" data-toggle="tooltip" data-placement="left" title="Статус задачи">' +
                        '            <option value="new" selected>Новая</option>' +
                        '            <option value="in_work">В работе</option>' +
                        '            <option value="ready">Готово</option>' +
                        '            <option value="expired">Просрочено</option>' +
                        '            <option value="deactivated">Не активная</option>' +
                        '        </select>' +
                        '        <input class="form form-control deactivated" style="display: none" data-type="active_after" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Сделать задачу активной после:">' +
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

            }

            $('#save-basic-stub').on('click', function () {
                if ($(this).val() === 'basic') {
                    $('.block-from-hide').hide(300)
                } else {
                    $('.block-from-hide').show(300)
                }
            })

            $('#save-stub').on('change', function () {
                if ($(this).val() === 'no') {
                    $('#dynamic-stub').parent().hide(300)
                } else {
                    $('#dynamic-stub').parent().show(300)
                }
            })

            $(document).on('click', '#save-new-checklist', function () {
                $(this).attr('disabled', true)
                $('#loader').show(300)

                let tasks = [];

                $.each($('#tasks').children('li'), function () {
                    tasks.push(parseTree(($(this))))
                })

                $.ajax({
                    type: 'post',
                    url: "{{ route('store.checklist') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        url: $('#url').val(),
                        tasks: tasks,
                        saveStub: $('#save-stub').val(),
                        dynamicStub: $('#dynamic-stub').val()
                    },
                    success: function (message) {
                        successMessage(message)
                        $('#loader').hide(300)
                        $('#save-new-checklist').attr('disabled', false)

                        $('#createNewProject > div > div > div.modal-footer.d-flex.justify-content-between > div:nth-child(2) > button.btn.btn-default').trigger('click')
                        $('#url').val('')
                        $('#tasks').html('')

                        loadChecklists(0, true)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                        $('#save-new-checklist').attr('disabled', false)
                        $('#loader').hide(300)
                    }
                })
            })

            $('#create-new-stub').on('click', function () {
                if ($('#stubs').html() === '') {
                    $('#add-new-stub').trigger('click')
                }
            })

            $(document).on('click', '#save-new-stubs', function () {
                $('#loader-stubs').show(300)
                $('#save-new-stubs').attr('disabled', true)
                let stubs = [];

                $.each($('#stubs').children('li'), function () {
                    stubs.push(parseTree(($(this))))
                })

                $.ajax({
                    type: 'post',
                    url: "{{ route('store.stubs') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        stubs: stubs,
                        action: $('#save-stub-action').val()
                    },
                    success: function (message) {
                        $('#loader-stubs').hide(300)
                        $('#save-new-stubs').attr('disabled', false)
                        successMessage(message)
                        $('#createNewSTub > div > div > div.modal-footer.d-flex.justify-content-between > div:nth-child(2) > button.btn.btn-default').trigger('click')
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                        $('#loader-stubs').hide(300)
                        $('#save-new-stubs').attr('disabled', false)
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

            $(document).on('click', '#set-stub', function () {
                let basicID = $('.ribbon-wrapper.ribbon-lg').parent().attr('data-id')

                if (basicID === undefined) {
                    errorMessage(['Шаблон не выбран'])
                } else {
                    $('#tasks').html(generateTasks(JSON.parse(basicTasks[basicID].tree)))
                    refreshTooltips()
                }
            })

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
                        '        <select data-id="status-' + id + '" data-target="' + id + '" class="custom custom-select task-status" data-type="status" data-toggle="tooltip" data-placement="left" title="Статус задачи">' +
                        '            <option value="new" selected>Новая</option>' +
                        '            <option value="in_work">В работе</option>' +
                        '            <option value="ready">Готово</option>' +
                        '            <option value="expired">Просрочено</option>' +
                        '            <option value="deactivated">Не активная</option>' +
                        '        </select>' +
                        '        <input class="form form-control deactivated" style="display: none" data-type="active_after" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Сделать задачу активной после:">' +
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

            let basicTasks

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
                    html += generateNestedStubs(JSON.parse(tasks.tree), true)
                    html += button
                    html += '</ol>'
                });

                $('#stubs-place').html(html)
            }

            $(document).on('click', '.remove-stub', function () {
                let ID = $(this).attr('data-id')
                let $parent = $(this).parent()

                if (confirm('Вы действительно хотите удалить шаблон?')) {
                    $.ajax({
                        type: 'get',
                        url: '/remove-checklist-stub/' + ID,
                        success: function (message) {
                            successMessage(message)
                            $parent.remove()
                        },
                        error: function (response) {
                            errorMessage(response.responseJSON.errors)
                        }
                    })
                }
            })

            $(document).on('click', '#classic-stubs', function () {
                $('#custom-tabs-three-profile').html('')
                $('#classic-stubs-place').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5">' +
                    '    <img src="/img/1485.gif">' +
                    '</div>'
                )

                $.ajax({
                    type: 'get',
                    url: "{{ route('checklist.classic.stubs') }}",
                    success: function (stubs) {
                        let html = ''
                        if (stubs.length > 0) {
                            $.each(stubs, function (index, task) {
                                let button = '<button class="btn btn-sm btn-default remove-stub" data-id="' + task.id + '"><i class="fa fa-trash"></i></button>'
                                html += '<ol class="stubs card card-body col-4 mt-4" data-id="' + index + '">'
                                html += generateNestedStubs(JSON.parse(task.tree))
                                html += button
                                html += '</ol>'
                            });
                        } else {
                            html = '<p>Базовые шаблоны отсутсвуют</p>'
                        }

                        $('#classic-stubs-loader').remove()
                        $('#classic-stubs-place').html(html)
                    }
                })
            })

            $(document).on('click', '#personal-stubs', function () {
                $('#custom-tabs-three-profile').html('')
                $('#personal-stubs-place').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5">' +
                    '    <img src="/img/1485.gif">' +
                    '</div>'
                )

                $.ajax({
                    type: 'get',
                    url: "{{ route('checklist.personal.stubs') }}",
                    success: function (stubs) {
                        let html = ''

                        if (stubs.length > 0) {
                            $.each(stubs, function (index, stub) {
                                html += '<ol class="stubs card card-body col-4 mt-4" data-id="' + index + '">'
                                html += generateNestedStubs(JSON.parse(stub.tree), true)
                                html += '<button class="btn btn-sm btn-default remove-stub" data-id="' + stub.id + '">' +
                                    '<i class="fa fa-trash"></i>' +
                                    '</button>'
                                html += '</ol>'
                            });
                        } else {
                            html = '<p>У вас нет личных шаблонов</p>'
                        }

                        $('#personal-stubs-place').html(html)
                    }
                })
            })

            $(document).on('click', '.relevance-star', function () {
                getNewProjects("{{ route('checklist.relevance.projects') }}")
            })

            $(document).on('click', '.position-star', function () {
                getNewProjects("{{ route('checklist.monitoring.projects') }}")
            })

            $(document).on('click', '.metatag-star', function () {
                getNewProjects("{{ route('checklist.metatags.projects') }}")
            })

            $(document).on('click', '.domain-monitoring-star', function () {
                getNewProjects("{{ route('checklist.domain.monitoring.projects') }}")
            })

            function getNewProjects($route) {
                $.ajax({
                    type: 'get',
                    url: $route,
                    success: function (projects) {
                        let html = ''

                        $.each(projects, function (k, v) {
                            html +=
                                '<div><input type="checkbox" class="custom custom-checkbox mr-2 project-checkbox new-project-variable" data-target="' + v + '"><span>' + v + '</span>'
                        })

                        if (html === '') {
                            $('#place-from-projects').html('Нечего добавлять')
                        } else {
                            $('#place-from-projects').html('<p>Проекты которые ещё не были добавлены:</p>' + html + '<br><br><button class="btn btn-sm btn-default" data-action="mark" id="mark-all">Выделить всё</button>')
                        }
                    }
                })
            }

            $(document).on('click', '#mark-all', function () {
                if ($(this).attr('data-action') === 'mark') {
                    $('.project-checkbox').prop('checked', true)
                    $(this).attr('data-action', 'clear')
                    $(this).html('Снять выделение')
                } else {
                    $('.project-checkbox').prop('checked', false)
                    $(this).attr('data-action', 'mark')
                    $(this).html('Выделить всё')
                }
            })

            $(document).on('click', '.remove-variable', function () {
                $(this).parent().remove()
            })

            $(document).on('click', '#add-multiply-projects', function () {
                $('#add-multiply-projects').attr('disabled', true)
                $('#multiple-loader').show(300)
                let urls = []

                $.each($('.new-project-variable'), function () {
                    if ($(this).is(':checked')) {
                        urls.push($(this).attr('data-target'))
                    }
                })

                if (urls.length === 0) {
                    errorMessage(['Проекты не выбраны'])

                    $('#add-multiply-projects').attr('disabled', false)
                    $('#multiple-loader').hide(300)

                    return;
                }

                $.ajax({
                    type: 'post',
                    url: "{{ route('checklist.multiply.create') }}",
                    data: {
                        urls: urls
                    },
                    success: function (response) {
                        $('#add-multiply-projects').attr('disabled', false)
                        $('#multiple-loader').hide(300)
                        loadChecklists($('.page-item.active > .page-link').attr('data-id'))
                        $('#close-multiply-projects').trigger('click')

                        if (response.fails.length > 0) {
                            errorMessage(response.fails, 10000)
                        }
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                        $('#add-multiply-projects').attr('disabled', false)
                        $('#multiple-loader').hide(300)
                    }
                })
            })

            function generateNestedStubs(stubs, each = true) {
                let $listItem = ''

                if (each) {
                    $.each(stubs, function (k, stub) {
                        stub = stub[0] ?? stub
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
                        if (stub.subtasks && stub.subtasks.length > 0) {
                            stub.subtasks.forEach(function (subtask) {
                                $subList += generateNestedStubs(subtask, false);
                            });
                        }

                        $subList += '</ol>';
                        $listItem += $subList
                    });
                } else {
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

            $(document).on('click', '.read-notification', function () {
                let button = $(this)
                let ID = $(this).attr('data-id')
                let badge = $('.badge.badge-success[data-id="' + $(this).attr('data-id') + '"]')

                $.ajax({
                    type: 'get',
                    url: '/checklist/read-notification/' + ID,
                    success: function () {
                        badge.removeClass('badge-success')
                        badge.addClass('badge-info')
                        badge.html('Прочитано')

                        button.remove()

                        let counter = Number($('#notification-counter').html()) - 1
                        if (counter == 0) {
                            $('#notification-counter').hide(300)
                        }

                        $('#notification-counter').html(counter)
                    }
                })
            })

            $(document).on('click', '.delete-notification', function () {
                let $parent = $(this).parents().eq(2)
                let ID = $(this).attr('data-id')

                if (confirm('Вы действительно хотите удалить уведомление?')) {
                    $.ajax({
                        type: 'get',
                        url: '/checklist/delete-notification/' + ID,
                        success: function () {
                            $parent.remove()
                        }
                    })
                }
            })

            getNotifications()
            $('#notification').on('click', function () {
                getNotifications()
            })

            function getNotifications() {
                $('#notification-tab').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5">' +
                    '    <img src="/img/1485.gif">' +
                    '</div>'
                )

                $.ajax({
                    type: 'get',
                    url: "{{ route('checklist.notifications') }}",
                    success: function (notifications) {
                        let counter = 0
                        let html = ''
                        $.each(notifications, function (key, notification) {
                            if (notification.status === 'notification') {
                                counter++

                                html +=
                                    '<div class="callout callout-info">' +
                                    '    <div class="d-flex">' +
                                    '        <h5 class="col-9">У вас есть просроченая задача "' + notification.task.name + '" в проекте ' +
                                    '           <a href="' + notification.task.project.url + '" target="_blank">' + notification.task.project.url + '</a>' +
                                    '           <span class="badge badge-success" data-id="' + notification.id + '">Новое</span>' +
                                    '        </h5>' +
                                    '        <div class="col-3 d-flex justify-content-end">' +
                                    '            <button class="btn btn-sm btn-flat btn-default read-notification mr-2" data-id="' + notification.id + '">Пометить прочитанным</button>' +
                                    '            <button class="btn btn-sm btn-flat btn-default delete-notification" data-id="' + notification.id + '">Удалить</button>' +
                                    '        </div>' +
                                    '     </div>' +
                                    '    <a href="/checklist-tasks/' + notification.task.project.id + '?search_task=' + notification.task.name + '" target="_blank">Просмотреть задачу</a>' +
                                    '</div>'
                            } else {
                                html +=
                                    '<div class="callout callout-info">' +
                                    '    <div class="d-flex">' +
                                    '         <h5 class="col-9">У вас есть просроченая задача "' + notification.task.name + '" в проекте ' +
                                    '              <a href="' + notification.task.project.url + '" target="_blank">' + notification.task.project.url + '</a>' +
                                    '              <span class="badge badge-info">Прочитано</span>' +
                                    '         </h5>' +
                                    '         <div class="col-3 d-flex justify-content-end">' +
                                    '             <button class="btn btn-sm btn-flat btn-default delete-notification" data-id="' + notification.id + '">Удалить</button>' +
                                    '         </div>' +
                                    '    </div>' +
                                    '    <a href="/checklist-tasks/' + notification.task.project.id + '?search_task=' + notification.task.name + '" target="_blank">Просмотреть задачу</a>' +
                                    '</div>'
                            }
                        })

                        if (counter > 0) {
                            $('#notification-counter').show(300)
                            $('#notification-counter').html(counter)
                        } else {
                            $('#notification-counter').hide(300)
                            $('#notification-counter').html(counter)
                            html = 'У вас нет уведомлений'
                        }

                        $('#notification-tab').html(html)
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })

            }
        </script>
    @endslot
@endcomponent
