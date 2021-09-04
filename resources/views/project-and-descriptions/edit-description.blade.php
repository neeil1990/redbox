@component('component.card', ['title' => __('Edit a description')])

    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
    @endslot
    <div class="card-body">
        <form action="{{route('save.edit.description')}}" method="POST" class="col-lg-12 col-sm-12 mb-5">
            @csrf
            <input type="hidden"
                   name="description_id"
                   value="{{$description->id}}">
            <div class="form-group">
                <label>{{__('Description')}}</label>
                {!! Form::textarea('description', $description->description, ['id' => 'description','class' => 'form-control mb-3 description' . ($errors->has('description') ? ' is-invalid' : '')]) !!}
                @error('description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <input type="submit" class="btn btn-secondary" value="{{__('Save changes')}}">
            <input type="reset" class="btn btn-default ml-1" value="{{__('Clear')}}" onclick="resetText()">
            <a href="{{ route('projects') }}" class="btn btn-default btn-flat">{{__('Back')}}</a>
        </form>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/summernote-bs4.css') }}"></script>
        <script>
            $(function () {
                $('.description').summernote({
                    lang: 'ru-RU',
                    minHeight: 350
                });
            });

            function resetText() {
                document.querySelector('.note-editable.card-block').innerHTML = ''
            }
        </script>
    @endslot
@endcomponent
