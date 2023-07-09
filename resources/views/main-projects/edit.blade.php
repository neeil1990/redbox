@component('component.card', ['title' => __('Edit project') . " $data->title"])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
    @endslot
    @section('content')
        {!! Form::open(['action' => ['MainProjectsController@update',$data->id], 'method' => 'PUT'])!!}
        <div class="col-md-6">
            <h4 class="pt-2 pb-2">{{ __('Edit project') }} {{ $data->title }}</h4>
            <span
                class="text-muted"> {{ __('When you update a project, you need to manually changed the localization text to the item') }}</span>
            <p class="text-muted">######################## Main page ########################</p>
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
                        {!! Form::text("title", $data->title ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="position">{{ __('Position in the menu') }}</label>
                        {!! Form::number("position", $data->position ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="description">{{ __('Project description') }}</label>
                        {!! Form::textarea("description", $data->description ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="link">{{ __('Link') }}</label>
                        {!! Form::text("link", $data->link ,["class" => "form-control","required" => "required"]) !!}
                    </div>

                    <div class="form-group required">
                        {!! Form::label("icon") !!}
                        {!! Form::text("icon", $data->icon ,["class" => "form-control","required" => "required", 'placeholder' => '<i class="fas fa-address-book"></i>']) !!}
                    </div>

                    <div class="text-muted mt-3 pb-3">
                        <div>Команды и сами иконки можно посмотреть на сайтах:</div>
                        <div>
                            <a href="https://fontawesome.com/v5.15/icons?d=gallery&p=2"
                               target="_blank">https://fontawesome.com</a>
                        </div>
                        <div>
                            <a href="https://useiconic.com/open/" target="_blank">https://useiconic.com/open</a>
                        </div>
                        <div>
                            <a href="https://ionic.io/ionicons" target="_blank">https://ionic.io/ionicons</a>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label for="access">{{ __('Access') }}</label>
                        {!! Form::select("access[]",  $roles, $data->access, ["class" => "form-control", "multiple"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="show">Показывать обычным пользователям</label>
                        <input type="checkbox" name="show" @if($data->show) checked @endif>
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
                                    PasswordGeneratorController - <span
                                        class="text-muted">Название контроллера</span> <br>
                                    @index - <span class="text-muted">Метод, будет накапливать счётчик посещения и обновления страниц</span> <br>
                                    !generatePassword - <span class="text-muted">Метод, который будет накапливать счётчик действий</span> <br> <br>

                                    <span
                                        class="text-muted">Путь до кода - app/Http/Middleware/VisitStatistics.php</span> <br> <br>
                                    <span class="text-info">Методы контроллера которые не были описани при помощи дирректив (@, !) будут игнорироваться</span>
                                </span>
                            </span>
                        </span>
                        {!! Form::textarea("controller", $data->controller ,["class" => "form-control"]) !!}
                    </div>

                    <div class="form-group required">
                        <label for="color">{{ __('Color module') }}</label>
                        <input type="color" id="color" name="color" class="form form-control col-1"
                               value="{{ $data->color }}">
                    </div>

                    <div class="form-group required">
                        <label for="buttons">Какие кнопки отслеживаем</label>
                        <textarea name="buttons" id="buttons" cols="10" rows="10"
                                  class="form-control">@isset($data->buttons){{ implode("\r\n", json_decode($data->buttons)) }}@endisset</textarea>
                    </div>
                </div>
            </div>

            <div class="well well-sm clearfix mt-3 pb-5">
                <button class="btn btn-success pull-right" type="submit">{{ __('Update') }}</button>
                <a href="{{ url('main-projects') }}" class="btn btn-default"> {{ __('Back') }}</a>
            </div>

        </div>

        {!! Form::close() !!}
    @endsection
@endcomponent
