@component('component.card', ['title' => __('Create a project')])

    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/list-comparison/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>@endslot
    <div class="card-body">
        <form action="{{route('save.project')}}" method="POST" class="col-lg-12 col-sm-12 mb-5">
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
                {!! Form::text('project_name', null, ['class' => 'form-control mb-3 project_name_input' . ($errors->has('project_name') ? ' is-invalid' : ''), 'placeholder' => __('Project name')]) !!}
                @error('project_name') <span class="error invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>{{__('Short description')}}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('You can leave this field empty, it will be generated automatically')}}
                            </span>
                        </span>
                    </span>
                </label>
                {!! Form::text('short_description', null, ['class' => 'form-control mb-3 short_description_input' . ($errors->has('short_description') ? ' is-invalid' : ''),'placeholder'=>__('You can leave this field empty, it will be generated automatically')]) !!}
                @error('short_description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>{{__('Description')}}</label>
                {!! Form::textarea('description', null, ['id' => 'description','class' => 'form-control mb-3' . ($errors->has('description') ? ' is-invalid' : '')]) !!}
                @error('description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
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
                    lang: 'ru-RU',
                    minHeight: 350
                });
            });

            function resetText() {
                document.querySelector('.note-editable.card-block').innerHTML = ''
                document.querySelector('.project_name_input').value = ''
                document.querySelector('.short_description_input').value = ''
            }
        </script>
    @endslot
@endcomponent
