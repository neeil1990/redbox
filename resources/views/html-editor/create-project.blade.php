@component('component.card', ['title' => __('Create a project')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/list-comparison/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
    @endslot
    <form action="{{ route('store.project') }}" method="POST" class="col-lg-12 col-sm-12 mb-5" id="summernote-form">
        @csrf
        <div class="form-group">
            <label>{{__('Project name')}}</label>
            <input type="text"
                   name="project_name"
                   class="form-control mb-3"
                   placeholder="{{ __('Project name') }}"
                   value="@isset($request['project_name']) {{ $request['project_name'] }} @endif">
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
            <input type="text"
                   name="short_description"
                   class="form-control mb-3"
                   placeholder="{{ __('You can leave this field empty, it will be generated automatically') }}"
                   value="@isset($request['short_description']) {{ $request['short_description'] }} @endif">
            @error('short_description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>{{__('Text')}}</label>
            <textarea name="description" id="description" class="form-control mb-3">@isset($request['description'])
                    {{ $request['description'] }}
                @endif</textarea>
            @error('description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <input type="submit" class="btn btn-secondary mr-2" value="{{__('Save the project')}}">
        @if($showButton)
            <a href="{{ route('HTML.editor') }}"
               class="btn btn-default btn-flat">{{__('Back to projects')}}</a>
        @endif
        <div id="scroll_to_bottom"></div>
    </form>
    @slot('js')
        <script src="{{ asset('/plugins/ckeditor/ckeditor.js') }}" type="text/javascript" charset="utf-8"></script>
        <script>
            let editor = CKEDITOR.replace('description');
            $('#cke_93').remove()
            $(document).ready(function () {
                setTimeout(() => {
                    console.clear()
                }, 300)
            })
        </script>
    @endslot
@endcomponent
