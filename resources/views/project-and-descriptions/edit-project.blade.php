@component('component.card', ['title' => __('Edit a project')])

    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
    @endslot
    <div class="card-body">
        <form action="{{route('save.edit.project')}}" method="POST" class="col-lg-12 col-sm-12 mb-5">
            @csrf
            <input type="hidden" name="project_id" value="{{$project->id}}">
            <div class="form-group">
                <label>{{__('Project name')}}</label>
                {!! Form::text('project_name', $project->project_name, ['class' => 'form-control mb-3' . ($errors->has('project_name') ? ' is-invalid' : ''), 'placeholder' => __('Project name')]) !!}
                @error('project_name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label>{{__('Short description')}}</label>
                {!! Form::text('short_description', $project->short_description, ['class' => 'form-control mb-3' . ($errors->has('short_description') ? ' is-invalid' : ''), 'placeholder' => __('Short description')]) !!}
                @error('short_description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <input type="submit" class="btn btn-secondary" value="{{__('Save changes')}}">
            <a href="{{ route('projects') }}" class="btn btn-default btn-flat">{{__('Back')}}</a>
        </form>
    </div>
@endcomponent
