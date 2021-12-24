@component('component.card', ['title' => __('Create project')])
@section('content')
    <h4 class="pt-2 pb-2">{{ __('This module allows you to add services that are displayed on the main page') }}</h4>
    <span
        class="text-muted"> {{ __('When you create a project, you need to manually add the localization text to the item') }}</span>
    <p class="text-muted">######################## Main page ########################</p>
    {!! Form::open(['action' =>'DescriptionProjectForAdminController@store', 'method' => 'POST'])!!}
    <div class="col-md-6 mt-3">
        <div class="form-group required">
            {!! Form::label("title") !!}
            {!! Form::text("title", null ,["class"=>"form-control","required"=>"required"]) !!}
        </div>
        <div class="form-group required">
            {!! Form::label("description") !!}
            {!! Form::textarea("description", null ,["class"=>"form-control","required"=>"required"]) !!}
        </div>
        <div class="form-group required">
            {!! Form::label("link") !!}
            {!! Form::text("link", null ,["class"=>"form-control","required"=>"required"]) !!}
        </div>
        <div class="form-group required">
            {!! Form::label("icon") !!}
            {!! Form::text("icon", null ,["class"=>"form-control","required"=>"required", 'placeholder' => '<i class="fas fa-address-book"></i>']) !!}
        </div>
        <div class="form-group required">
            {!! Form::label("access") !!}
            {!! Form::select("access[]",  $roles, [], ["class" => "form-control", "multiple"]) !!}
        </div>
        <div class="text-muted mb-3">
            <span>Команды и сами иконки можно посмотреть на сайтах:</span> <br>
            <span><a href="https://fontawesome.com/v5.15/icons?d=gallery&p=2" target="_blank">https://fontawesome.com</a></span> <br>
            <span><a href="https://useiconic.com/open/" target="_blank">https://useiconic.com/open</a></span> <br>
            <span><a href="https://ionic.io/ionicons" target="_blank">https://ionic.io/ionicons</a></span> <br>
        </div>
        <div class="well well-sm clearfix pb-5">
            <button class="btn btn-success pull-right" title="Save" type="submit">{{ __('Update') }}</button>
            <a href="{{ url('main-projects') }}" class="btn btn-default"> {{ __('Back') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@endcomponent
