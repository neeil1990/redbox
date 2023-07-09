@component('component.card', ['title' => __('Create project')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
    @endslot
    @section('content')
        <h4 class="pt-2 pb-2">{{ __('This module allows you to add services that are displayed on the main page') }}</h4>
        <span
            class="text-muted"> {{ __('When you create a project, you need to manually add the localization text to the item') }}</span>
        <p class="text-muted">######################## Main page ########################</p>
        {!! Form::open(['action' =>'MainProjectsController@store', 'method' => 'POST'])!!}
        <div class="col-md-6 mt-3">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group required">
                        <label for="title">{{ __('Title') }}</label>
                        {!! Form::text("title", null ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="position">{{ __('Position in the menu') }}</label>
                        {!! Form::number("position", null ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="description">{{ __('Project description') }}</label>
                        {!! Form::textarea("description", null ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="link">{{ __('Link') }}</label>
                        {!! Form::text("link", null ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="icon">{{ __('icon') }}</label>
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question"></i>
                            <span class="ui_tooltip __right" style="min-width: 550px;">
                                <span class="ui_tooltip_content">
                                    Если иконка оторбражается не корректно, попробуйте добавить в класс "fas" <br>
                                    было "fa-address-book"<br> стало "<b>fas</b> fa-address-book"
                                </span>
                            </span>
                        </span>
                        {!! Form::text("icon", null ,["class" => "form-control","required" => "required", 'placeholder' => '<i class="fas fa-address-book"></i>']) !!}
                    </div>

                    <div class="text-muted mb-3 mt-3">
                        <span>Команды и сами иконки можно посмотреть на сайтах:</span> <br>
                        <span><a href="https://fontawesome.com/v5.15/icons?d=gallery&p=2" target="_blank">https://fontawesome.com</a></span>
                        <br>
                        <span><a href="https://useiconic.com/open/"
                                 target="_blank">https://useiconic.com/open</a></span> <br>
                        <span><a href="https://ionic.io/ionicons" target="_blank">https://ionic.io/ionicons</a></span>
                        <br>
                    </div>

                    <div class="form-group required">
                        <label for="access">{{ __('Access') }}</label>
                        {!! Form::select("access[]",  $roles, [], ["class" => "form-control", "multiple"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="show">Показывать обычным пользователям</label>
                        <input type="checkbox" name="show" checked>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Настройки статистики</h3>

                    <div class="form-group required">
                        <label for="description">{{ __('Controller') }}</label>
                        <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question"></i>
                    <span class="ui_tooltip __right" style="min-width: 550px;">
                        <span class="ui_tooltip_content">
                            Контроллер - связан с модулем, если контроллер указан, тогда для модуля начнёт собираться статистика.<br>
                            Пример: <br>
                            PasswordGeneratorController - <span class="text-muted">Название контроллера</span> <br>
                            @index - <span class="text-muted">Метод, будет накапливать счётчик посещения и обновления страниц</span> <br>
                            !generatePassword - <span class="text-muted">Метод, который будет накапливать счётчик действий</span> <br> <br>

                            <span class="text-muted">Путь до кода - app/Http/Middleware/VisitStatistics.php</span> <br> <br>
                            <span class="text-info">Методы контроллера которые не были описани при помощи дирректив (@, !) будут игнорироваться</span>
                        </span>
                    </span>
                </span>
                        {!! Form::textarea("controller", null ,["class" => "form-control"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="color">{{ __('Color module') }}</label>
                        <input type="color" id="color" name="color" class="form form-control col-1">
                    </div>

                    <div class="form-group required">
                        <label for="buttons">Какие кнопки отслеживаем</label>
                        {!! Form::textarea("buttons", null ,["class" => "form-control"]) !!}
                    </div>
                </div>
            </div>

            <div class="well well-sm clearfix mt-3 pb-5">
                <button class="btn btn-success pull-right" type="submit">{{ __('Add') }}</button>
                <a href="{{ url('main-projects') }}" class="btn btn-default"> {{ __('Back') }}</a>
            </div>

        </div>
        {!! Form::close() !!}
    @endsection
@endcomponent
