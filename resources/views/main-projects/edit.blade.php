@component('component.card', ['title' => __('Edit project')])
    @section('content')
        <h4 class="pt-2 pb-2">{{ __('This module allows you to change the services that are displayed on the main page') }}</h4>
        <span
            class="text-muted"> {{ __('When you update a project, you need to manually changed the localization text to the item') }}</span>
        <p class="text-muted">######################## Main page ########################</p>
        {!! Form::open(['action' =>['DescriptionProjectForAdminController@update',$data->id], 'method' => 'PUT'])!!}
        <div class="col-md-6">
            <div class="form-group required">
                {!! Form::label("title") !!}
                {!! Form::text("title", $data->title ,["class" => "form-control","required" => "required"]) !!}
            </div>

            <div class="form-group required">
                {!! Form::label("description") !!}
                {!! Form::textarea("description", $data->description ,["class" => "form-control","required" => "required"]) !!}
            </div>

            <div class="form-group required">
                {!! Form::label("link") !!}
                {!! Form::text("link", $data->link ,["class" => "form-control","required" => "required"]) !!}
            </div>

            <div class="form-group required">
                {!! Form::label("icon") !!}
                {!! Form::text("icon", $data->icon ,["class" => "form-control","required" => "required", 'placeholder' => '<i class="fas fa-address-book"></i>']) !!}
            </div>

            <div class="form-group required">
                {!! Form::label("access") !!}
                {!! Form::select("access[]",  $roles, $data->access, ["class" => "form-control", "multiple"]) !!}
            </div>

            <div class="form-group required">
                {!! Form::label("show") !!}
                <input type="checkbox" name="show" @if($data->show) checked @endif>
            </div>

            <div class="text-muted mb-3">
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

            <div class="well well-sm clearfix pb-5">
                <button class="btn btn-success pull-right" title="Save" type="submit">{{ __('Update') }}</button>
                <a href="{{ url('main-projects') }}" class="btn btn-default"> {{ __('Back') }}</a>
            </div>

        </div>
        {!! Form::close() !!}
    @endsection
@endcomponent
