@component('component.card', ['title' =>  __('SEO Checklist') ])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/keyword-generator/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <style>
            .fa.fa-eye:hover {
                color: black;
            }

            #projects-for-repeat-tasks {
                width: 466px;
            }

            #repeat-table_length > label > select {
                width: 100px;
                margin-left: 5px;
                margin-right: 5px;
            }

            f
            #repeat-table_length > label {
                display: flex;
            }

            #repeat-table_wrapper > div:nth-child(3) > div.col-sm-12.col-md-7,
            #repeat-table_wrapper > div:nth-child(1) > div:nth-child(2) {
                display: flex;
                justify-content: flex-end;
            }

            .callout a:hover {
                color: #007bff !important;
            }

            .stub-style {
                width: 85px;
                height: 20px;
                letter-spacing: 0;
            }

            i {
                transition: 0.3s;
                cursor: pointer;
            }

            .width {
                width: 150px;
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
            }

            .accordion.stubs.card.card-body {
                cursor: pointer;
            }

            #tasks .custom.custom-select {
                width: 160px;
            }

            .hide-border {
                border: none;
            }

            .hide-border:active, .hide-border:focus {
                border: 1px solid #ced4da !important;
            }
        </style>

        <style>
            .board {
                width: 100%;
            }

            #todo-form {
                padding: 32px 32px 0;
            }

            #todo-form input {
                padding: 12px;
                margin-right: 12px;
                width: 225px;

                border-radius: 4px;
                border: none;

                box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.25);
                background: white;

                font-size: 14px;
                outline: none;
            }

            #todo-form button {
                padding: 12px 32px;

                border-radius: 4px;
                border: none;

                box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.25);
                background: #ffffff;
                color: black;

                font-weight: bold;
                font-size: 14px;
                cursor: pointer;
            }

            /* ---- BOARD ---- */
            .lanes {
                display: flex;
                align-items: flex-start;
                justify-content: start;
                gap: 16px;

                padding: 10px 1px;

                overflow-x: scroll;
                height: 100%;
            }

            .lanes::-webkit-scrollbar {
                height: 6px;
            }

            /* Оставляем только ползунок */
            .lanes::-webkit-scrollbar-thumb {
                background-color: #ccc;
            }

            .heading {
                font-size: 18px;
                margin-bottom: 0;
                font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            }

            .swim-lane {
                display: flex;
                flex-direction: column;
                gap: 12px;
                box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
                border: 0 solid rgba(0, 0, 0, .125);
                padding: 12px;
                border-radius: 4px;
                width: 275px;
                max-height: 100%;
                overflow-y: auto;

                flex-shrink: 0;
            }

            .swim-lane p {
                margin-bottom: 0 !important;
            }

            .swim-lane::-webkit-scrollbar {
                width: 2px;
                height: 0;
            }

            /* Оставляем только ползунок */
            .swim-lane::-webkit-scrollbar-thumb {
                background-color: #ccc;
            }

            .swim-lane-top {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .swim-lane-hide {
                cursor: pointer;
                transition: 0.5s ease;
                transform: rotate(0deg);
            }

            .task {
                background: white;
                color: black;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                padding: 12px;
                border-radius: 10px;
                font-size: 16px;
                cursor: move;
                border: 1px solid rgba(0, 0, 0, .125);
            }

            .task:hover {
                background-color: #f7f7f7;
            }

            .is-dragging {
                scale: 1.05;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                background: rgb(50, 50, 50);
                color: white;
            }

            .task-microcard-block-1 a {
                font-size: 11px;
                text-decoration: none;
                color: #787878;
            }

            .task-microcard-label {
                color: #787878;
                font-size: 12px;
                margin-right: 3px;
            }

            .task-microcard-date-start,
            .task-microcard-date-end {
                font-size: 13px;
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
                        <a class="nav-link" id="custom-tabs-three-kanban-tab" data-toggle="pill"
                           href="#custom-tabs-three-kanban" role="tab" aria-controls="custom-tabs-three-kanban"
                           aria-selected="false">Канбан</a>
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
                        <a class="nav-link d-flex align-items-center" id="repeat-tasks" data-toggle="pill"
                           href="#repeat-tasks-tab" role="tab" aria-controls="repeat-tasks-tab"
                           aria-selected="false">
                            Повторяющиеся задачи
                        </a>
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
            <div class="card-body" style="margin-bottom: 30px;">
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
                                <div class="d-flex col-xs-12 col-xl-6 align-items-center"
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

                                    <button class="btn btn-secondary mr-1" data-toggle="modal"
                                            data-target="#createNewProject"
                                            id="add-new-checklist">
                                        Добавить проект
                                    </button>

                                    <button type="button" class="btn btn-secondary mr-1" data-toggle="modal"
                                            data-target="#modalLabel">
                                        Управление метками
                                    </button>

                                    <button id="create-new-stub" class="btn btn-secondary" data-toggle="modal"
                                            data-target="#createNewSTub">
                                        Добавить шаблон
                                    </button>
                                </div>
                                <div class="d-flex col-xs-12 col-xl-6 align-items-center justify-content-between">
                                    <div class="form-group">
                                        <label for="count">Количество проектов</label>
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
                                    <div class="form-group">
                                        <label for="name">URL проекта</label>
                                        <input type="text" id="name" name="name" class="form form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="tags">Фильтр по меткам</label>
                                        <input type="text" id="tags" name="tags" class="form form-control">
                                    </div>
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

                        <p id="empty-message" style="display: none">У вас нет активных проектов с заданными
                            фильтрами</p>
                        <ul class="pagination d-flex justify-content-end w-100" id="pagination"></ul>
                    </div>
                    <div class="tab-pane fade row d-flex" id="custom-tabs-three-kanban" role="tabpanel"
                         aria-labelledby="custom-tabs-three-kanban">
                        <div class="board">
                            <div class="btn-group mb-3">
                                <button class="btn btn-sm btn-default change-visible-state" data-target="expired-todo">
                                    Просроченные
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="today-todo">
                                    Сегодня
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="nextday-todo">
                                    Завтра
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="next-monday">
                                    Понедельник
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="next-tuesday">
                                    Вторник
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state"
                                        data-target="next-wednesday">Среда
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="next-thursday">
                                    Четверг
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="next-friday">
                                    Пятница
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="next-saturday">
                                    Суббота
                                </button>
                                <button class="btn btn-sm btn-default change-visible-state" data-target="next-sunday">
                                    Воскресенье
                                </button>
                            </div>
                            <div class="lanes">
                                <div class="swim-lane" id="expired-todo">
                                    <div class="swim-lane-top" style="height: 35.98px;">
                                        <h3 class="heading" style="margin-top: -12px;">Просроченные
                                            (<span id="expired-count-tasks">0</span>)</h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="today-todo">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Сегодня (<span id="toDay-count-tasks">0</span>)
                                            <p id="todayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="nextday-todo">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Завтра (<span id="tomorrow-count-tasks">0</span>)
                                            <p id="tomorrowDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="next-monday">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Понедельник (<span id="monday-count-tasks">0</span>)
                                            <p id="mondayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="next-tuesday">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Вторник (<span id="tuesday-count-tasks">0</span>)
                                            <p id="tuesdayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="next-wednesday">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Среда (<span id="wednesday-count-tasks">0</span>)
                                            <p id="wednesdayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="next-thursday">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Четверг (<span id="thursday-count-tasks">0</span>)
                                            <p id="thursdayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="next-friday">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Пятница (<span id="friday-count-tasks">0</span>)
                                            <p id="fridayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="next-saturday">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Суббота (<span id="saturday-count-tasks">0</span>)
                                            <p id="saturdayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="swim-lane" id="next-sunday">
                                    <div class="swim-lane-top">
                                        <h3 class="heading">Воскресенье (<span id="sunday-count-tasks">0</span>)
                                            <p id="sundayDate" class="task-microcard-label"></p>
                                        </h3>
                                        <div class="swim-lane-hide"><i class="btn btn-default btn-sm fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane fade row d-flex" id="custom-tabs-three-profile" role="tabpanel"
                         aria-labelledby="archived-checklists"></div>
                    @if(\App\User::isUserAdmin())
                        <div class="tab-pane fade" id="classic-tabs-stub" role="tabpanel"
                             aria-labelledby="classic-stubs">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Фильтры
                                    </h3>
                                </div>
                                <div class="card-body row">
                                    <div class="d-flex col-xs-12 col-xl-6 align-items-center"
                                         style="margin-top: 10px;">
                                        <button id="create-new-stub" class="btn btn-secondary" data-toggle="modal"
                                                data-target="#createNewSTub">
                                            Добавить шаблон
                                        </button>
                                    </div>
                                    <div class="d-flex col-xs-12 col-xl-6 align-items-center justify-content-end">
                                        <div class="form-group">
                                            <label for="count-classic-stub">Количество шаблонов</label>
                                            <select name="count-classic-stub" id="count-classic-stub"
                                                    class="custom custom-select">
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
                                        <div class="form-group ml-3">
                                            <label for="name-classic-stub">Название шаблона</label>
                                            <input type="text" id="name-classic-stub" name="name-classic-stub"
                                                   class="form form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="classic-stubs-place" class="d-flex row"></div>
                            <ul class="pagination d-flex justify-content-end w-100" id="classic-pagination"></ul>
                        </div>
                    @endif
                    <div class="tab-pane fade" id="personal-tabs-stub" role="tabpanel"
                         aria-labelledby="personal-stubs">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Фильтры
                                </h3>
                            </div>
                            <div class="card-body row">
                                <div class="d-flex col-xs-12 col-xl-6 align-items-center"
                                     style="margin-top: 10px;">
                                    <button id="create-new-stub" class="btn btn-secondary" data-toggle="modal"
                                            data-target="#createNewSTub">
                                        Добавить шаблон
                                    </button>
                                </div>
                                <div class="d-flex col-xs-12 col-xl-6 align-items-center justify-content-end">
                                    <div class="form-group">
                                        <label for="count-personal-stub">Количество шаблонов</label>
                                        <select name="count-personal-stub" id="count-personal-stub"
                                                class="custom custom-select">
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
                                    <div class="form-group ml-3">
                                        <label for="name-personal-stub">Название шаблона</label>
                                        <input type="text" id="name-personal-stub" name="name-personal-stub"
                                               class="form form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="personal-stubs-place" class="d-flex row"></div>
                        <ul class="pagination d-flex justify-content-end w-100" id="personal-pagination"></ul>
                    </div>
                    <div class="tab-pane fade" id="repeat-tasks-tab" role="tabpanel"
                         aria-labelledby="personal-stubs">
                        <button id="get-projects" type="button" class="btn btn-secondary mb-3" data-toggle="modal"
                                data-target="#myModal">Добавить повторяющиеся задачи
                        </button>

                        <div class="modal" id="myModal">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h4 class="modal-title">Добавление повторяющихся задач</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Название задачи</label>
                                            <input type="text" name="name" id="repeat-name" class="form form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="date_start">Дата первого запуска</label>
                                            <input type="datetime-local" name="date_start" id="repeat_date_start"
                                                   class="form form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="repeat_every">Повторять каждые N дней</label>
                                            <input type="number" name="repeat_every" id="repeat_repeat_every"
                                                   class="form form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="deadline_every">Количество дней на выполнение</label>
                                            <input type="number" name="deadline_every" id="repeat_deadline_every"
                                                   class="form form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="weekends">Учитывать выходные?</label>
                                            <select name="weekends" id="repeat_weekends" class="custom-select">
                                                <option value="1">Да</option>
                                                <option value="0">Нет</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Описание задачи</label>
                                            <textarea name="description" id="repeat_description" cols="10" rows="10"
                                                      class="form form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="projects-for-repeat-tasks">Проекты</label>
                                            <select name="projects-for-repeat-tasks" id="projects-for-repeat-tasks"
                                                    multiple></select>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success"
                                                id="save-new-repeat-task">{{ __('Save') }}</button>
                                        <button type="button" class="btn btn-default" id="close-repeat-modal"
                                                data-dismiss="modal">{{ __('Close') }}</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <table id="repeat-table" class="table table-bordered table-hover dataTable dtr-inline">
                            <thead>
                            <tr>
                                <th>
                                    <input type="text" class="form-control filter-input"
                                           style="width: 250px"
                                           placeholder="Название"
                                           data-index="0">
                                </th>
                                <th>
                                    <input type="text" class="form-control filter-input"
                                           placeholder="Описание"
                                           data-index="1">
                                </th>
                                <th style="width: 200px;">
                                    <input type="date" class="form-control filter-input"
                                           style="width: 200px;"
                                           placeholder="Дата запуска"
                                           data-index="2">
                                </th>
                                <th>
                                    <input type="text" class="form form-control filter-input"
                                           placeholder="Повторять каждые N дней" data-index="3">
                                </th>
                                <th>
                                    <select name="weekends"
                                            class="filter-input custom-select" data-index="4">
                                        <option value="1">Да</option>
                                        <option value="0">Нет</option>
                                    </select>
                                </th>
                                <th>
                                    <input type="text" class="form-control filter-input"
                                           placeholder="Количество дней на выполнение"
                                           data-index="5">
                                </th>
                                <th style="width: 150px"></th>
                            </tr>
                            <tr>
                                <th>Название</th>
                                <th style="width: 200px;">Описание</th>
                                <th>Дата следующего запуска</th>
                                <th>Повторяется каждые N дней</th>
                                <th>Учитываются выходные</th>
                                <th>Количество дней на выполнение</th>
                                <th style="width: 150px"></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                    <div class="tab-pane fade" id="notification-tab" role="tabpanel"></div>
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
                    Поместить проект в архив ?
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
                <div class="modal-body d-flex overflow-auto">
                    <div class="col-12">
                        <div class="mb-3" style="display: none">
                            <label for="dynamic-stub">Динамичный шаблон</label>
                            <span class="__helper-link ui_tooltip_w">
                                <i class="fa fa-question-circle" style="color: grey"></i>
                                <span class="ui_tooltip __bottom">
                                    <span class="ui_tooltip_content" style="width: 300px">
                                        Динамичный шаблон отслеживает задачи и вложенность задач у создаваемого проекта и имеет идентичную иерархию задач
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
                            <div id="accordionExample" class="mt-3">
                                <ol id="tasks" class="overflow-auto"></ol>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <label for="save-stub">Сохранение шаблона</label>
                        <span class="__helper-link ui_tooltip_w">
                                <i class="fa fa-question-circle" style="color: grey"></i>
                                <span class="ui_tooltip __top">
                                    <span class="ui_tooltip_content" style="width: 300px">
                                        <p>Личный шаблон - это шаблон который доступен только вам</p>
                                        @if(\App\User::isUserAdmin())
                                            <p class="text-info">
                                                Базовы шаблон - это шаблон который доступен всем пользователям (эта часть подсказки видна только админам)
                                            </p>
                                        @endif
                                    </span>
                                </span>
                            </span>
                        <select name="save-stub" id="save-stub" class="custom-select">
                            <option value="no" selected>Не сохранять шаблон</option>
                            <option value="personal">Личный шаблон</option>
                            @if(\App\User::isUserAdmin())
                                <option value="classic">Базовый шаблон</option>
                                <option value="all">Базовый и личный шаблон</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="project-start-date">Запуск проекта</label>
                        <select name="project-start-date" id="project-start-date" class="custom custom-select">
                            <option value="now">Сейчас</option>
                            <option value="wait">Отложенный запуск</option>
                        </select>
                    </div>
                    <div class="form form-group">
                        <label for="count-wait-days">Сделать активным через</label>
                        <input type="number" step="1" min="0" value="0"
                               class="form form-control ml-1 mr-1"
                               id="count-wait-days"
                               name="count-wait-days" disabled>
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
                        <div class="form-group">
                            <label for="stub-name">Название шаблона</label>
                            <input type="text" class="form form-control" id="stub-name" name="stub-name">
                        </div>
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
                                <button class="btn btn-secondary" id="add-new-stub">Добавить пункт</button>
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

    <div class="modal fade" id="updateTaskModal" tabindex="-1" aria-labelledby="updateTaskModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateTaskModalLabel">Изменение задачи</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="change-name-task">Название задачи</label>
                        <input id="change-name-task" type="text" data-type="name" class="form form-control update-task">
                    </div>
                    <div class="form-group">
                        <label for="change-task-state">Статус задачи</label>
                        <select id="change-task-state" class="custom custom-select update-task" data-type="status">
                            <option value="new">Новая</option>
                            <option value="in_work">В работе</option>
                            <option value="ready">Готово</option>
                            <option value="expired">Просрочена</option>
                            <option value="repeat">Повторяющаяся</option>
                        </select>
                    </div>
                    <div class="form-group d-flex flex-row">
                        <div class="w-50 mr-2">
                            <label for="change-task-start-date">Дата начала</label>
                            <input type="datetime-local" id="change-task-start-date" data-type="date_start"
                                   class="form form-control update-task">
                        </div>
                        <div class="w-50">
                            <label for="change-task-end-date">Дата окончания</label>
                            <input type="datetime-local" id="change-task-end-date" data-type="deadline"
                                   class="form form-control update-task">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="change-description-task">Описание задачи</label>
                        <textarea name="change-description-task"
                                  id="change-description-task"
                                  cols="10" rows="10"
                                  class="form form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/jszip.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/vfs_fonts.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/html5.min.js') }}"></script>

        <script src="{{ asset('plugins/checklist/common.js') }}"></script>
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            $(function () {
                if (localStorage.getItem('SEO_CHECKLIST_COUNT') !== null) {
                    $('#count').val(localStorage.getItem('SEO_CHECKLIST_COUNT'))
                }
                if (localStorage.getItem('SEO_CHECKLIST_CLASSIC_COUNT') !== null) {
                    $('#count-classic-stub').val(localStorage.getItem('SEO_CHECKLIST_CLASSIC_COUNT'))
                }
                if (localStorage.getItem('SEO_CHECKLIST_PERSONAL_COUNT') !== null) {
                    $('#count-personal-stub').val(localStorage.getItem('SEO_CHECKLIST_PERSONAL_COUNT'))
                }
                loadChecklists(0, true)
            })

            let editedID
            let editedTimeout
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
                    '                        <option value="repeat">Повторяющаяся</option>' +
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

            $(document).on('click', '#add-task', function () {
                $('#tasks').append(
                    '<li data-id="' + counter + '">' +
                    '    <div class="card">' +
                    '    <div class="card-header d-flex flex-row justify-content-between" id="heading' + counter + '">' +
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
                    '                    <option value="expired">Просрочено</option>' +
                    '                    <option value="ready">Готово</option>' +
                    '                    <option value="repeat">Повторяющаяся</option>' +
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

                                let totalTasks = v.tasks.length

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
                                    '    <div class="card-body">' +
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
                                    '                    <span class="width">Готовые:</span> <span>' + v.ready + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Повторяющиеся:</span> <span>' + v.repeat + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Отложенные:</span> <span>' + v.inactive + '</span>' +
                                    '                </div>' +
                                    '                <div class="d-flex row">' +
                                    '                    <span class="width">Просроченые:</span> <span>' + v.expired + '</span>' +
                                    '                </div>' +
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

            $('#custom-tabs-three-kanban').on('click', '.swim-lane-hide', function () {
                let $this = $(this)
                if ($this.attr('hide-blocks') == 'true') {
                    $this.attr('hide-blocks', false)
                    $this.parents().eq(1).find('.dl-task-info.task').fadeIn(500)
                } else {
                    $this.attr('hide-blocks', true)
                    $this.parents().eq(1).find('.dl-task-info.task').fadeOut(500)
                }
                return;
                $(this).toggleClass('rotate');

                var swimLane = $(this).closest('.swim-lane');
                var currentWidth = swimLane.data('width') || swimLane.css('width') || swimLane.width(); // получаем текущую ширину .swim-lane
                var newWidth = currentWidth === '37px' ? '275px' : '37px';
                swimLane.animate({
                    'width': newWidth
                }, 500);

                swimLane.data('width', newWidth);
                if (newWidth === '37px') {
                    swimLane.find('.task').slideUp();
                    swimLane.find('.swim-lane-top').css('flex-flow', 'column-reverse');
                    swimLane.find('.heading').animate({
                        'width': '10px',
                        'margin-top': '30px'
                    }, 500);
                } else {
                    swimLane.find('.task').slideDown()
                    swimLane.find('.swim-lane-top').css('flex-flow', '');
                    swimLane.find('.heading').animate({
                        'width': '90%',
                        'margin-top': '0'
                    }, 500);
                }


                var swimLaneId = swimLane.attr('id');
                var swimLaneState = {
                    width: newWidth,
                    isHidden: (newWidth === '37px')
                };

                localStorage.setItem(swimLaneId, JSON.stringify(swimLaneState));
            });

            $(document).on('click', '#custom-tabs-three-kanban-tab', function () {
                renderKanban()
            })

            function renderKanban() {
                $.ajax({
                    type: 'post',
                    url: "{{ route('get.checklistsKanban') }}",
                    success: function (response) {
                        $('.dl-task-info.task').remove()

                        let cards = {
                            'expired': '#expired-todo',
                            'toDay': '#today-todo',
                            'tomorrow': '#nextday-todo',
                            'monday': '#next-monday',
                            'tuesday': '#next-tuesday',
                            'wednesday': '#next-wednesday',
                            'thursday': '#next-thursday',
                            'friday': '#next-friday',
                            'saturday': '#next-saturday',
                            'sunday': '#next-sunday'
                        }

                        $.each(cards, function (day, element) {
                            $('#' + day + '-count-tasks').html(response[day].length)

                            let variable = ''

                            $.each(response[day], function (item, value) {
                                variable += renderTask(value);
                            })
                            $(element).append(variable)
                        })

                        refreshTooltips()

                        let dayDates = [
                            'todayDate',
                            'tomorrowDate',
                            'mondayDate',
                            'tuesdayDate',
                            'wednesdayDate',
                            'thursdayDate',
                            'fridayDate',
                            'saturdayDate',
                            'sundayDate',
                        ]

                        $.each(dayDates, function (i, prefix) {
                            $('#' + prefix).html(response[prefix])
                        })

                        $('.swim-lane').each(function () {
                            var swimLaneId = $(this).attr('id');
                            var swimLaneState = JSON.parse(localStorage.getItem(swimLaneId));
                            if (swimLaneState) {
                                $(this).css('width', swimLaneState.width);
                                if (swimLaneState.isHidden) {
                                    $(this).find('.task').slideUp(0);
                                }
                            }
                        });

                        let draggables = document.querySelectorAll(".task");
                        let droppables = document.querySelectorAll(".swim-lane");

                        draggables.forEach((task) => {
                            task.addEventListener("dragstart", () => {
                                task.classList.add("is-dragging");
                            });

                            task.addEventListener("dragend", () => {
                                let deadline = $(task).find('.task-microcard-date-end.task-microcard-interact')
                                let newStatus = $(task).find('.task-microcard-date-start.task-microcard-interact').eq(0)

                                task.classList.remove("is-dragging");
                                let id = event.target.getAttribute("data-id");
                                let status = event.target.getAttribute("data-status");
                                let parent = event.target.parentNode.id;

                                let deadlineDate = undefined
                                try {
                                    deadlineDate = $(task).parent().find('p.task-microcard-label').html().trim()
                                } catch (e) {
                                }

                                if (status === 'expired') {
                                    status = 'new'
                                }
                                if (parent === 'expired-todo') {
                                    status = 'expired'
                                }

                                $.ajax({
                                    type: 'post',
                                    data: {
                                        id: id,
                                        deadline: deadlineDate,
                                        status: status,
                                    },
                                    url: "{{ route('save.checklistsKanban') }}",
                                    success: function (response) {
                                        recalculateCountTasks()
                                        deadline.html(response.deadline)
                                        newStatus.html(response.status)
                                    },
                                    error: function (response) {
                                    }
                                })
                            });
                        });

                        droppables.forEach((zone) => {
                            zone.addEventListener("dragover", (e) => {
                                e.preventDefault();

                                let bottomTask = insertAboveTask(zone, e.clientY);
                                let curTask = document.querySelector(".is-dragging");

                                if (!bottomTask) {
                                    zone.appendChild(curTask);
                                } else {
                                    zone.insertBefore(curTask, bottomTask);
                                }
                            });
                        });

                        let insertAboveTask = (zone, mouseY) => {
                            let els = zone.querySelectorAll(".task:not(.is-dragging)");

                            let closestTask = null;
                            let closestOffset = Number.NEGATIVE_INFINITY;

                            els.forEach((task) => {
                                let {top} = task.getBoundingClientRect();

                                let offset = mouseY - top;

                                if (offset < 0 && offset > closestOffset) {
                                    closestOffset = offset;
                                    closestTask = task;
                                }
                            });

                            return closestTask;
                        };
                    },
                    error: function (response) {

                    }
                })
            }

            $(document).on('click', '.change-visible-state', function () {
                let $this = $(this)
                let target = $this.attr('data-target')

                if ($this.hasClass('hover')) {
                    localStorage.removeItem(target + '_localstorage_item')

                    $this.removeClass('hover')
                    $('#' + target).show(300)
                } else {
                    localStorage.setItem(target + '_localstorage_item', true)

                    $this.addClass('hover')
                    $('#' + target).hide(300)
                }
            })

            $.each($('.change-visible-state'), function (k, item) {
                if (localStorage.getItem($(this).attr('data-target') + '_localstorage_item')) {
                    $(this).trigger('click')
                }
            })

            function renderTask(value) {
                let dateObj = new Date(value.date_start);
                let day = dateObj.getDate();
                let month = dateObj.getMonth() + 1;
                let year = dateObj.getFullYear();
                let date_start = (day < 10 ? '0' : '') + day + '.' + (month < 10 ? '0' : '') + month + '.' + year;

                dateObj = new Date(value.deadline);
                day = dateObj.getDate();
                month = dateObj.getMonth() + 1;
                year = dateObj.getFullYear();
                let deadline = (day < 10 ? '0' : '') + day + '.' + (month < 10 ? '0' : '') + month + '.' + year;

                let status

                if (value.status === 'new') {
                    status = 'Новая'
                } else if (value.status === 'expired') {
                    status = 'Просроченая'
                } else if (value.status === 'in_work') {
                    status = 'В работе'
                } else {
                    status = 'Готовая'
                }

                return '<div data-id="' + value.id + '" data-deadline="' + value.deadline + '" data-status="' + value.status + '" class="dl-task-info task" draggable="true">' +
                    '<div class="b-task-microcard has-menu">' +
                    '   <div class="task-microcard-project text-size-s text-style-stt d-flex justify-content-between" style="word-break: break-word;">' +
                    '       <a target="_blank" href="' + value.project.url + '" data-toggle="tooltip" data-placement="top" title="' + value.project.url + '">' +
                    new URL(value.project.url)['origin'] + '</a>' +
                    '   <button class="btn btn-default btn-sm get-task-info" data-id="' + value.id + '" type="button" data-toggle="modal" data-target="#updateTaskModal"><i class="fa fa-edit"></i></button>' +
                    '   </div>' +
                    '   <div class="task-microcard-title task-microcard-block-2 tr-taskstatus-color-3377C3 tr-taskstatus-style-0 text-size-l text-style-stb">' +
                    '       <a href="/checklist-tasks/' + value.project.id + '" target="_blank">' + value.name + '</a> ' +
                    '   </div>' +
                    '   <div class="task-microcard-block-3 task-microcard-row label-enable text-size-m text-style-stt">' +
                    '           <span class="task-microcard-label">Статус:</span>' +
                    '           <span class="task-microcard-date-start task-microcard-interact">' + status + '</span> ' +
                    '       </div>' +
                    '   </div>' +
                    '   <div class="task-microcard-block-3 task-microcard-row label-enable text-size-m text-style-stt">' +
                    '           <span class="task-microcard-label">Сроки:</span>' +
                    '           <span class="task-microcard-date-start task-microcard-interact">' + date_start + '</span>  —' +
                    '           <span class="task-microcard-date-end task-microcard-interact">' + deadline + '</span>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>';
            }

            function recalculateCountTasks() {
                $.each($('.swim-lane'), function (i, item) {
                    let count = $(item).children('.dl-task-info.task').length

                    $(item).find('.swim-lane-top > h3 > span').html(count)
                })
            }

            let $targetTaskId

            $(document).on('click', '.get-task-info', function () {
                $targetTaskId = $(this).attr('data-id')
                $.ajax({
                    type: 'get',
                    url: `/checklist-task/${$targetTaskId}`,
                    success: function (response) {
                        $('#change-description-task').summernote('destroy');

                        $('#change-name-task').val(response.name)
                        $('#change-task-state').val(response.status)
                        $('#change-task-start-date').val(response.date_start)
                        $('#change-task-end-date').val(response.deadline)
                        $('#change-description-task').val(response.description)

                        $('#change-description-task').summernote({
                            minHeight: 350,
                            lang: "ru-RU",
                            onChange: function (contents, $editable) {
                                editedID = $editable.parents().eq(2).find('textarea:first-child').attr('data-id')
                                clearTimeout(editedTimeout)
                                editedTimeout = setTimeout(() => {
                                    $.ajax({
                                        type: 'post',
                                        url: "{{ route('edit.checklist.task') }}",
                                        data: {
                                            id: $targetTaskId,
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
                        })
                    }
                })
            })

            $(document).on('change', '.update-task ', function () {
                let type = $(this).attr('data-type')
                let value = $(this).val()

                $.ajax({
                    type: 'post',
                    url: "{{ route('edit.checklist.task') }}",
                    data: {
                        id: $targetTaskId,
                        type: type,
                        value: value,
                    },
                    success: function (response) {
                        successMessage('Успешно')
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            $('#updateTaskModal').on('hidden.bs.modal', function () {
                renderKanban()
            });

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
                    '                    <option value="expired">Просроченая</option>' +
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

                if ($(this).attr('data-type') === 'pagination') {
                    loadChecklists($(this).attr('data-id'), false)
                }
                if ($(this).attr('data-type') === 'classic') {
                    loadClassicStubs($(this).attr('data-id'), false)
                }
                if ($(this).attr('data-type') === 'personal') {
                    loadPersonalStubs($(this).attr('data-id'), false)
                }
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
                            renderPagination(response.paginate, '#pagination', 'pagination')
                        }
                    },
                    error: function (response) {

                    }
                })
            }

            function renderPagination(paginate, target, type) {
                let pagination = ''

                if (paginate > 1) {
                    for (let i = 0; i < paginate; i++) {
                        let html = i + 1

                        if (i === 0) {
                            pagination += '<li class="page-item active"><a href="#" class="page-link" data-type="' + type + '" data-id="' + i + '">' + html + '</a></li>'
                        } else {
                            pagination += '<li class="page-item"><a href="#" class="page-link" data-type="' + type + '" data-id="' + i + '">' + html + '</a></li>'
                        }
                    }
                }

                $(target).html(pagination)
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
                    let totalTasks = v.tasks.length

                    let labels =
                        '<div class="col-8" data-action="labels" data-id="' + v.id + '">' +
                        '    <ul class="fc-color-picker">'

                    let statistics = ''

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

                    if (v.statistics) {
                        statistics += '<div> Кол-во слов: ' + v.statistics.words + '</div>' +
                            '<div> Cр.позиция: ' + v.statistics.middle + '</div>' +
                            '<div> Топ 10: ' + v.statistics.top10 + '% </div>' +
                            '<div> Топ 100: ' + v.statistics.top100 + '% </div>'
                    }

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
                        '    <div class="card-body">' +
                        '        <div class="d-flex">' +
                        '            <div class="d-flex flex-column col-6">' +
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
                        '                    <span class="width">Готовые:</span> <span>' + v.ready + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Повторяющиеся:</span> <span>' + v.repeat + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Отложенные:</span> <span>' + v.inactive + '</span>' +
                        '                </div>' +
                        '                <div class="d-flex row">' +
                        '                    <span class="width">Просроченые:</span> <span>' + v.expired + '</span>' +
                        '                </div>' +
                        '            </div>' +
                        '            <div class="d-flex col-4 flex-column">' + statistics + '</div>' +
                        '            <div class="d-flex col-2 flex-column align-items-end">' +
                        '                <div>' +
                        '                    <a target="_blank" href="{{ route('relevance.history') }}" data-target="' + v.url + '" class="fa-regular fa-star text-dark localstorage-item" data-toggle="tooltip" data-placement="top"' +
                        '                       title="Анализ релевантности"></a>' +
                        '                </div>' +
                        '                <div style="margin-right: 1px">' +
                        '                    <a target="_blank" href="/monitoring" data-target="' + v.url + '" class="fa fa-chart-line text-dark localstorage-item" data-toggle="tooltip" data-placement="top"' +
                        '                       title="Мониторинг позиций"></a>' +
                        '                </div>' +
                        '                <div style="margin-right: 3px">' +
                        '                    <a target="_blank" href="/meta-tags" data-target="' + v.url + '" class="fa fa-heading text-dark localstorage-item" data-toggle="tooltip" data-placement="top"' +
                        '                       title="Мониторинг метатегов"></a>' +
                        '                </div>' +
                        '                <div>' +
                        '                    <a target="_blank" href="{{ route('site.monitoring') }}" data-target="' + v.url + '" class="fa fa-edit text-dark localstorage-item" data-toggle="tooltip" data-placement="top"' +
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
                    lang: "ru-RU"
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
                    lang: "ru-RU"
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
                    lang: "ru-RU"
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

                let newDate = new Date(new Date(value).getTime() + ($days.val() * count_ml_in_day)).toISOString().slice(0, 16)
                $deadline.val(newDate)

            })

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

            function stub(id, stub = false) {
                let date = new Date().toISOString().slice(0, 16);

                if (stub) {
                    return '<li data-id="' + id + '" class="default d-flex justify-content-between" style="height: 46px">' +
                        '    <span class="text-muted d-flex justify-content-center align-items-center" style="letter-spacing: 0">Задача №: ' + id + '</span>' +
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
                        '        <select data-id="status-' + id + '" data-target="' + id + '" class="custom custom-select task-status" data-type="status" data-toggle="tooltip" data-placement="left" title="Статус задачи">' +
                        '            <option value="new" selected>Новая</option>' +
                        '            <option value="in_work">В работе</option>' +
                        '            <option value="ready">Готово</option>' +
                        '            <option value="expired">Просрочено</option>' +
                        '            <option value="deactivated">Не активная</option>' +
                        '            <option value="repeat">Повторяющаяся</option>' +
                        '        </select>' +
                        '        <select class="custom custom-select" data-target="' + id + '" data-type="weekends" data-toggle="tooltip" data-placement="left" title="Учитывать выходные дни?" style="display: none">' +
                        '               <option value="1">Да</option>' +
                        '               <option value="0">Нет</option>' +
                        '        </select>' +
                        '        <input class="form form-control datetime-repeat-counter" type="number" step="1" min="1" data-target="' + id + '" data-type="repeat_after" value="1" data-toggle="tooltip" data-placement="left" title="Повторять каждые N дней" style="display:none; width: 55px">' +
                        '        <input class="form form-control datetime-counter" type="number" step="1" value="0" min="0" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Количество дней на выполнение">' +
                        '        <input class="form form-control datetime" value="' + date + '" data-type="start" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата начала">' +
                        '        <input class="form form-control datetime" value="' + date + '" data-type="deadline" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Дата окончания">' +
                        '        <input class="form form-control deactivated" data-type="active_after" type="datetime-local" data-target="' + id + '" data-toggle="tooltip" data-placement="left" title="Сделать задачу активной после:" style="display: none">' +
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
                if ($('#project-start-date').val() === 'wait' && $('#count-wait-days').val() === '0') {
                    errorMessage(['Укажите количество дней, через которые проект будет активен'], 5000)
                    return;
                }

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
                        dynamicStub: $('#dynamic-stub').val(),
                        projectStartDate: $('#project-start-date').val(),
                        waitDays: $('#count-wait-days').val()
                    },
                    success: function (response) {
                        successMessage(response.message)
                        $('#loader').hide(300)
                        $('#save-new-checklist').attr('disabled', false)

                        $('#createNewProject > div > div.modal-content.col-9.mr-2 > div.modal-footer.d-flex.justify-content-between > div:nth-child(4) > button.btn.btn-default').trigger('click')
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

                if (stubs.length === 0) {
                    errorMessage(['Шаблон должен содержать структуру задач'])
                    $('#loader-stubs').hide(300)
                    $('#save-new-stubs').attr('disabled', false)
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: "{{ route('store.stub') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: $('#stub-name').val(),
                        stubs: stubs,
                        action: $('#save-stub-action').val()
                    },
                    success: function (message) {
                        $('#loader-stubs').hide(300)
                        $('#save-new-stubs').attr('disabled', false)
                        successMessage(message)
                        $('#createNewSTub > div > div > div.modal-footer.d-flex.justify-content-between > div:nth-child(2) > button.btn.btn-default').trigger('click')

                        if ($('#save-stub-action').val() === 'classic') {
                            loadClassicStubs()
                        } else {
                            loadPersonalStubs()
                        }
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

            function renderStubs(tasks) {
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

                $('#stubs-place').html(html)
            }

            $(document).on('click', '#classic-stubs', function () {
                loadClassicStubs()
            })

            $(document).on('change', '#count-classic-stub', function () {
                localStorage.setItem('SEO_CHECKLIST_CLASSIC_COUNT', $(this).val())
                loadClassicStubs()
            })

            let loadTimeout
            $(document).on('input', '#name-classic-stub', function () {
                clearTimeout(loadTimeout)

                loadTimeout = setTimeout(() => {
                    loadClassicStubs()
                }, 300)
            })

            function loadClassicStubs(page = 0, pagination = true) {
                $('#save-stub-action').val('classic')
                $('#custom-tabs-three-profile').html('')
                $('#classic-stubs-place').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5">' +
                    '    <img src="/img/1485.gif">' +
                    '</div>'
                )

                $.ajax({
                    url: "{{ route('checklist.classic.stubs') }}",
                    type: 'post',
                    data: {
                        name: $('#name-classic-stub').val(),
                        count: $('#count-classic-stub').val(),
                        skip: page * $('#count-classic-stub').val()
                    },
                    success: function (response) {
                        $('#classic-stubs-loader').remove()
                        $('#classic-stubs-place').html(renderStubsHtml(response.stubs))

                        if (pagination) {
                            renderPagination(response.paginate, '#classic-pagination', 'classic')
                        }
                    }
                })
            }

            function loadPersonalStubs(page = 0, pagination = true) {
                $('#save-stub-action').val('personal')
                $('#custom-tabs-three-profile').html('')
                $('#personal-stubs-place').html(
                    '<div class="d-flex justify-content-center align-items-center w-100 mt-5">' +
                    '    <img src="/img/1485.gif">' +
                    '</div>'
                )

                $.ajax({
                    type: 'post',
                    data: {
                        name: $('#name-personal-stub').val(),
                        count: $('#count-personal-stub').val(),
                        skip: page * $('#count-personal-stub').val()
                    },
                    url: "{{ route('checklist.personal.stubs') }}",
                    success: function (response) {
                        $('#personal-stubs-place').html(renderStubsHtml(response.stubs))

                        if (pagination) {
                            renderPagination(response.paginate, '#personal-pagination', 'personal')
                        }
                    }
                })
            }

            $(document).on('click', '#personal-stubs', function () {
                loadPersonalStubs()
            })

            $(document).on('change', '#count-personal-stub', function () {
                localStorage.setItem('SEO_CHECKLIST_PERSONAL_COUNT', $(this).val())
                loadPersonalStubs()
            })

            $(document).on('input', '#name-personal-stub', function () {
                clearTimeout(loadTimeout)

                loadTimeout = setTimeout(() => {
                    loadPersonalStubs()
                }, 300)
            })

            function renderStubsHtml(stubs) {
                let html = ''

                if (stubs.length > 0) {
                    $.each(stubs, function (index, stub) {
                        html += '<div class="col-xl-3 col-xs-6"><div class="card">'
                        html += '<div class="card-header d-flex justify-content-between">'
                            + '<input type="text" value="' + stub.name + '" data-id="' + stub.id + '" class="form form-control hide-border stub-name col-10">' +
                            '<button class="btn btn-default remove-stub-card" data-id="' + stub.id + '"><i class="fa fa-trash"></i></button></div>'
                        html += '<ol class="stubs card-body" data-id="' + index + '">'
                        html += generateNestedStubs(JSON.parse(stub.tree), true)
                        html += '</ol></div></div>'
                    });
                } else {
                    html = '<p>У вас нет личных шаблонов</p>'
                }

                return html;
            }

            $(document).on('change', '.stub-name', function () {
                let ID = $(this).attr('data-id')
                let name = $(this).val()

                $.ajax({
                    type: 'post',
                    url: "{{ route('edit.stub') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: ID,
                        name: name
                    },
                    success: function (response) {
                        successMessage('Успешно')
                    },
                    error: function (response) {
                        errorMessage(['Ошибка'])
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
                        '     <div style="height: 20px;">' +
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

            let repeatTable = $('#repeat-table').DataTable({
                processing: true,
                serverSide: true,
                lengthMenu: [10, 25, 50, 100],
                pageLength: 50,
                order: [[0, 'desc']],
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: [6]
                    }
                ],
                ajax: "{{ route('get.repeat.tasks') }}",
                columns: [
                    {
                        name: 'name',
                        data: function (row) {
                            return '<input class="form form-control change-value" data-target="' + row.id + '" data-name="name" value="' + row.name + '">'
                        },
                    },
                    {
                        name: 'description',
                        data: function (row) {
                            let description

                            if (row.description == null) {
                                description = ''
                            } else {
                                description = row.description
                            }

                            return '<button class="btn btn-default btn-sm mb-3" type="button" data-toggle="collapse" data-target="#collapseExample' + row.id + '" aria-expanded="false" aria-controls="collapseExample">' +
                                '<i class="fa fa-eye"></i>' +
                                '</button>' +
                                '<div class="collapse" id="collapseExample' + row.id + '"> ' +
                                '    <div class="card card-body">' +
                                '        <textarea class="form form-control change-value textarea-summernote" data-target="' + row.id + '" data-name="description">' + description + '</textarea>' +
                                '    </div>' +
                                '</div>'
                        },
                    },
                    {
                        name: 'date_start',
                        data: function (row) {
                            return '<input class="form form-control change-value" type="datetime-local" data-target="' + row.id + '" data-name="date_start" value="' + row.date_start + '">'
                        },
                    },
                    {
                        name: 'repeat_every',
                        data: function (row) {
                            return '<input class="form form-control change-value" data-target="' + row.id + '" data-name="repeat_every" value="' + row.repeat_every + '">'
                        },
                    },
                    {
                        name: 'weekends',
                        data: function (row) {
                            if (row.weekends) {
                                return '<select class="custom custom-select change-value" data-target="' + row.id + '" data-name="weekends">' +
                                    '    <option value="1" selected>Да</option>' +
                                    '    <option value="0">Нет</option>' +
                                    '</select>'
                            } else {
                                return '<select class="custom custom-select change-value" data-target="' + row.id + '" data-name="weekends">' +
                                    '    <option value="1">Да</option>' +
                                    '    <option value="0" selected>Нет</option>' +
                                    '</select>'
                            }
                        },
                    },
                    {
                        name: 'deadline_every',
                        data: function (row) {
                            return '<input class="form form-control change-value" data-target="' + row.id + '" data-name="weekends" value="' + row.deadline_every + '">'
                        },
                    },
                    {
                        name: 'projects',
                        data: function (row) {
                            return '<div class="btn-group">' +
                                '<a class="btn btn-sm btn-secondary" href="' + row.project.url + '" target="_blank">Сайт</a>' +
                                '<a class="btn btn-sm btn-secondary" href="/checklist-tasks/' + row.project.id + '" target="_blank">Задачи</a>' +
                                '<button class="btn btn-sm btn-danger remove-repeat-task" data-target="' + row.id + '">Удалить</button>' +
                                '</div>'
                        },
                    },
                ],
                language: {
                    sEmptyTable: "Нет данных для отображения",
                    sInfo: "Показано с _START_ по _END_ из _TOTAL_ записей",
                    sInfoEmpty: "Показано 0 записей",
                    sInfoFiltered: "(отфильтровано из _MAX_ записей)",
                    sLengthMenu: "Показывать _MENU_ записей на странице",
                    sSearch: "Поиск:",
                    sZeroRecords: "Нет соответствующих записей",
                    searchPlaceholder: 'Поиск',
                    paginate: {
                        "first": "«",
                        "last": "»",
                        "next": "»",
                        "previous": "«"
                    },
                },
                drawCallback: function () {
                    $('#repeat-table').wrap('<div style="width: 100%; overflow: auto"></div>')
                    $('#repeat-table').css({
                        width: '100%'
                    })

                    let timeout
                    $('.filter-input').unbind().on('input', function () {
                        clearTimeout(timeout)
                        timeout = setTimeout(() => {
                            repeatTable.column($(this).attr('data-index')).search($(this).val()).draw();
                        }, 500)
                    });

                    $(document).on('change', '.change-value', function () {
                        let id = $(this).attr('data-target')
                        let name = $(this).attr('data-name')
                        let value = $(this).val()

                        $.ajax({
                            type: 'post',
                            url: "{{ route('edit.repeat.task') }}",
                            data: {
                                id: id,
                                name: name,
                                value: value,
                            },
                            success: function () {
                                successMessage('Изменения применены')
                            }
                        })
                    })

                    $('.textarea-summernote').summernote({
                        minHeight: 350,
                        callbacks: {
                            onChange: function (contents, $editable) {
                                editedID = $editable.parents().eq(2).find('textarea:first-child').attr('data-target')
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
                            },
                        },
                        lang: "ru-RU"
                    })

                    $(document).on('click', '.remove-repeat-task', function () {
                        let id = $(this).attr('data-target')

                        if (confirm('Удалить задачу?')) {
                            $.ajax({
                                type: 'post',
                                url: "{{ route('remove.repeat.task') }}",
                                data: {
                                    id: id,
                                },
                                success: function (response) {
                                    successMessage('Задача была удалена')
                                    repeatTable.draw()
                                },
                                error: function (response) {
                                    errorMessage(response.responseJSON.errors)
                                }
                            })
                        }
                    })
                }
            })

            getNotifications()
            $('#notification').on('click', function () {
                getNotifications()
            })

            $('#repeat_description').summernote({
                minHeight: 350,
                lang: "ru-RU",
            })

            $('#get-projects').on('click', function () {
                $.ajax({
                    type: 'get',
                    url: "{{ route('get.all.checklists') }}",
                    success: function (checklists) {
                        let options = ''
                        $.each(checklists, function (i, item) {
                            options += '<option value="' + item.id + '" data-toggle="tooltip" data-placement="top" title="' + item.url + '">' +
                                new URL(item.url)["host"] +
                                '</option>'
                        })

                        $('#projects-for-repeat-tasks').html(options)
                        $('#projects-for-repeat-tasks').select2({theme: 'bootstrap4'});
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors)
                    }
                })
            })

            $('#save-new-repeat-task').on('click', function () {
                $.ajax({
                    type: 'post',
                    data: {
                        name: $('#repeat-name').val(),
                        description: $('#repeat_description').val(),
                        date_start: $('#repeat_date_start').val(),
                        repeat_every: $('#repeat_repeat_every').val(),
                        deadline_every: $('#repeat_deadline_every').val(),
                        weekends: $('#repeat_weekends').val(),
                        ids: $('#projects-for-repeat-tasks').val()
                    },
                    url: "{{ route('store.repeat.tasks') }}",
                    success: function (response) {
                        successMessage(response.message)
                        repeatTable.draw()
                        $('#close-repeat-modal').trigger('click')
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.errors, 10000)
                    }
                })
            })

            function getNotifications() {
                $('#custom-tabs-three-profile').html('')
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

            $(document).on('click', '.localstorage-item', function () {
                localStorage.setItem('redbox_localstorage_item', $(this).attr('data-target'))
            })

            $(document).on('change', '#project-start-date', function () {
                $('#count-wait-days').attr('disabled', $(this).val() === 'now')
            })
        </script>
    @endslot
@endcomponent
