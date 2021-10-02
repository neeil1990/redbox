@component('component.card', ['title' => __('Create project')])
@section('content')
    <h3 class="pt-2 pb-2">{{ __('This module allows you to create services that are displayed on the main page') }}</h3>
    <span
        class="text-info"> {{ __('When you create a project, you need to manually add the localization text to the item') }}</span>
    <p class="text-info">######################## Main page ########################</p>
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
        <div class="well well-sm clearfix">
            <button class="btn btn-secondary pull-right" title="Save" type="submit">{{ __('Create') }}</button>
            <a href="http://redbox/public/main-projects" class="btn btn-default"> {{ __('Back') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@endcomponent
