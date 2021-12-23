@component('component.card', ['title' => __('Edit a text')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
    @endslot
    <div class="modal fade" id="clear-description" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog w-25" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>{{__('Clear text')}}</p>
                    <p>{{__('Are you sure?')}}</p>
                </div>
                <div class="modal-footer">
                    <input type="reset" class="btn btn-default ml-1" value="{{__('Clear')}}"
                           data-dismiss="modal">
                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        {{__('Back')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <form action="{{route('save.edit.description')}}" method="POST" class="col-lg-12 col-sm-12 mb-5"
          id="summernote-form">
        @csrf
        <input type="hidden"
               name="description_id"
               value="{{$description->id}}">
        <div class="form-group">
            <label>{{__('Text')}}</label>
            {!! Form::textarea('description', $description->description, ['id' => 'description','class' => 'form-control mb-3 description' . ($errors->has('description') ? ' is-invalid' : '')]) !!}
            @error('description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div>
            <input type="submit" class="btn btn-secondary mr-2" value="{{__('Save the project')}}">
            <button class="btn btn-default btn-flat mr-2" type="button" data-toggle="modal"
                    data-target="#clear-text">
                {{__('Clear')}}
            </button>
            <a href="{{ route('HTML.editor') }}"
               class="btn btn-default btn-flat">{{__('Back to projects')}}</a>
        </div>
        <div id="scroll_to_bottom"></div>
    </form>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            //this **language** variable put in the TextEditorController.createView()
            $(function () {
                $('#description').summernote({
                    lang: language,
                    minHeight: 350
                });
            });

            $(document).ready(function () {
                $(".btn.btn-default.ml-1").click(function () {
                    if ($('#description').summernote('codeview.isActivated')) {
                        $('#description').summernote('codeview.deactivate');
                        $('#description').summernote('code', '');
                        $('#description').summernote('codeview.activate');
                    } else {
                        $('#description').summernote('code', '');
                    }
                });
            });

            $(document).on("submit", "#summernote-form", function (e) {
                if ($('#description').summernote('codeview.isActivated')) {
                    $('#description').summernote('codeview.deactivate');
                }
            });
        </script>
    @endslot
@endcomponent
