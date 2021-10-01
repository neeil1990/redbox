@component('component.card', ['title' => __('Create project')])
@section('content')
    <h4 class="pt-2 pb-2">This module allows you to create services that are displayed on the main page</h4>
    {!! Form::open(['action' =>'DescriptionProjectForAdminController@store', 'method' => 'POST'])!!}
    <div class="col-md-6">
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
            <button class="btn btn-success pull-right" title="Save" type="submit">Create</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@endcomponent
