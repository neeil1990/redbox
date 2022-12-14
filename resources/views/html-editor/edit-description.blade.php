@component('component.card', ['title' => __('Edit a text')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
    @endslot
    <form action="{{ route('save.edit.description') }}" method="POST" class="col-12 mb-5">
        @csrf
        <input type="hidden"
               name="description_id"
               value="{{ $project->id }}">
        <div class="form-group">
            <label>{{__('Text')}}</label>
            <textarea name="description" id="description" class="form-control mb-3">{!! $project->description !!}</textarea>
            @error('description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div>
            <input type="submit" class="btn btn-secondary mr-2" value="{{__('Save the project')}}">
            <a href="{{ route('HTML.editor') }}"
               class="btn btn-default btn-flat">{{__('Back to projects')}}</a>
        </div>
        <div id="scroll_to_bottom"></div>
    </form>
    @slot('js')
        <script src="{{ asset('/plugins/ckeditor/ckeditor.js') }}" type="text/javascript" charset="utf-8"></script>
        <script>
            let editor = CKEDITOR.replace('description');
            $(document).ready(function () {
                setTimeout(() => {
                    console.clear()
                    $('#cke_93').remove()
                }, 300)
            })
        </script>
    @endslot
@endcomponent
