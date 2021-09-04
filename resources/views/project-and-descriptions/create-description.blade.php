@component('component.card', ['title' => __('Projects')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
    @endslot
    <div class="card-body">
        <form action="{{ route('save.description') }}" method="POST" class="col-lg-12 col-sm-12">
            @csrf
            <div class="form-group">
                <label>{{__('Project name')}}</label>
                <select class="form-control input-sm" name="project_id">
                    @foreach($projects as $project)
                        <option value="{{$project->id}}">{{$project->project_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{__('Description')}}</label>
                {!! Form::textarea('description', null, ['id' => 'description','class' => 'form-control mb-3' . ($errors->has('description') ? ' is-invalid' : '')]) !!}
                @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <input type="submit" class="btn btn-secondary" value="{{__('Save the project')}}">
            <input type="reset" class="btn btn-default ml-1" value="{{__('Clear')}}" onclick="resetText()">
            <a href="{{ route('projects') }}" class="btn btn-default btn-flat">{{__('Back')}}</a>
        </form>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script>
            $(function () {
                $('#description').summernote({
                    minHeight: 350
                });
            });

            function resetText() {
                document.querySelector('.note-editable.card-block').innerHTML = ''
            }
        </script>
    @endslot
@endcomponent
