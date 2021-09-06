@component('component.card', ['title' => __('Projects')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
    @endslot
    <div class="card-body">
        <form action="{{ route('save.description') }}" method="POST" class="col-lg-12 col-sm-12">
            <div class="modal fade" id="clear-text" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog w-25" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p>{{__('Clear text')}}</p>
                            <p>{{__('Are you sure?')}}</p>
                        </div>
                        <div class="modal-footer">
                            <input type="reset" class="btn btn-default ml-1" value="{{__('Clear')}}"
                                   onclick="resetText()" data-dismiss="modal">
                            <button type="button"
                                    class="btn btn-default"
                                    data-dismiss="modal">
                                {{__('Back')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
            <div class="d-flex justify-content-between">
                <div>
                    <input type="submit" class="btn btn-secondary" value="{{__('Save the project')}}">
                    <button class="mr-2 ml-2 btn btn-default btn-flat" type="button" data-toggle="modal"
                            data-target="#clear-text">
                        {{__('Clear')}}
                    </button>
                </div>
                <div>
                    <a href="{{ route('projects') }}" class="btn btn-default btn-flat">{{__('Back')}}</a>
                </div>
            </div>
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
