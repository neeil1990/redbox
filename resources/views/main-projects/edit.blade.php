@component('component.card', ['title' => __('Edit project')])
@section('content')
    {!! Form::open(['action' =>['DescriptionProjectForAdminController@update',$data->id], 'method' => 'PUT'])!!}
    <div class="col-md-6">
        <div class="form-group required">
            {!! Form::label("title") !!}
            {!! Form::text("title", $data->title ,["class"=>"form-control","required"=>"required"]) !!}
        </div>

        <div class="form-group required">
            {!! Form::label("description") !!}
            {!! Form::text("description", $data->description ,["class"=>"form-control","required"=>"required"]) !!}
        </div>

        <div class="form-group required">
            {!! Form::label("link") !!}
            {!! Form::text("link", $data->link ,["class"=>"form-control","required"=>"required"]) !!}
        </div>
        <div class="well well-sm clearfix">
            <button class="btn btn-success pull-right" title="Save" type="submit">Update</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@endcomponent
