@component('component.card', ['title' => __('Edit news')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
    @endslot
    <div class="card-body">
        <form action="{{ route('save.edit.news') }}" method="POST" class="col-lg-12 col-sm-12" id="summernote-form">
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
                <label>{{__('Content')}}</label>
                {!! Form::textarea('content',  $news->content , ['id' => 'content','class' => 'form-control mb-3', 'required']) !!}
                <input type="hidden" name="id" value="{{ $news->id }}">
            </div>
            <div>
                <input type="submit" class="btn btn-secondary mr-2" value="{{__('Edit')}}">
                <button class="btn btn-default btn-flat mr-2"
                        type="button"
                        data-toggle="modal"
                        data-target="#clear-text">{{__('Clear')}}
                </button>
                <a href="{{ route('news') }}" class="btn btn-default btn-flat">{{ __('Back') }}</a>
            </div>
            <div id="scroll_to_bottom"></div>
        </form>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            $(function () {
                let content = $('#content')
                content.summernote({
                    minHeight: 350
                });

                $(".btn.btn-default.ml-1").click(function () {
                    if (content.summernote('codeview.isActivated')) {
                        content.summernote('codeview.deactivate');
                        content.summernote('code', '');
                        content.summernote('codeview.activate');
                    } else {
                        content.summernote('code', '');
                    }
                });
            });

            $(document).on("submit", "#summernote-form", function () {
                if (content.summernote('codeview.isActivated')) {
                    content.summernote('codeview.deactivate');
                }
            });
        </script>
    @endslot
@endcomponent
