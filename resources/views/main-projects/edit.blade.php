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
            {!! Form::text("title", $data->title ,["class"=>"form-control","required"=>"required"]) !!}
        </div>

        <div class="form-group required">
            {!! Form::label("description") !!}
            {!! Form::textarea("description", $data->description ,["class"=>"form-control","required"=>"required"]) !!}
        </div>

        <div class="form-group required">
            {!! Form::label("link") !!}
            {!! Form::text("link", $data->link ,["class"=>"form-control","required"=>"required"]) !!}
        </div>
        <div class="well well-sm clearfix">
            <button class="btn btn-success pull-right" title="Save" type="submit">{{ __('Update') }}</button>
            <a href="http://redbox/public/main-projects" class="btn btn-default"> {{ __('Back') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@endcomponent
