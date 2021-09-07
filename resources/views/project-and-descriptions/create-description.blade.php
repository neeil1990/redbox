@component('component.card', ['title' => __('Add text')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
    @endslot
    <div class="card-body">
        <div class="scroll-to d-flex flex-column">
            <a href="#header-nav-bar" class="fa fa-arrow-circle-up scroll_arrow"></a>
            <a href="#scroll_to_bottom" class="fa fa-arrow-circle-down scroll_arrow"></a>
        </div>
        <form action="{{ route('save.description') }}" method="POST" class="col-lg-12 col-sm-12" id="summernote-form">
            <div class="modal fade" id="clear-text" tabindex="-1" role="dialog" aria-hidden="true">
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
                <label>{{__('Text')}}</label>
                {!! Form::textarea('description', null, ['id' => 'description','class' => 'form-control mb-3' . ($errors->has('description') ? ' is-invalid' : '')]) !!}
                @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div id="scroll_to_bottom"></div>
        </form>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            //this **language** variable put in the TextEditorController.getLanguage()
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

            $(".scroll_arrow").on("click", function (e) {
                e.preventDefault();
                var anchor = $(this).attr('href');
                $('html, body').stop().animate({
                    scrollTop: $(anchor).offset().top - 60
                }, 800);
            });

            $(document).on("submit", "#summernote-form", function (e) {
                if ($('#description').summernote('codeview.isActivated')) {
                    $('#description').summernote('codeview.deactivate');
                }
            });
        </script>
    @endslot
@endcomponent
