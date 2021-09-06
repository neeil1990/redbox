@component('component.card', ['title' => __('Edit a text')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
    @endslot
    <div class="card-body">
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
        <form action="{{route('save.edit.description')}}" method="POST" class="col-lg-12 col-sm-12 mb-5">
            @csrf
            <input type="hidden"
                   name="description_id"
                   value="{{$description->id}}">
            <div class="form-group">
                <label>{{__('Text')}}</label>
                {!! Form::textarea('description', $description->description, ['id' => 'description','class' => 'form-control mb-3 description' . ($errors->has('description') ? ' is-invalid' : '')]) !!}
                @error('description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="d-flex justify-content-between">
                <div>
                    <input type="submit" class="btn btn-secondary" value="{{__('Save changes')}}">
                    <button class="mr-2 ml-2 btn btn-default btn-flat" type="button" data-toggle="modal"
                            data-target="#clear-description">
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
        <script src="{{ asset('plugins/summernote/summernote-bs4.css') }}"></script>
        <script>
            $(function () {
                $('.description').summernote({
                    lang: 'ru-RU',
                    minHeight: 350
                });
            });

            $(document).ready(function () {
                $(".btn.btn-default.ml-1").click(function () {
                    $('#description').summernote('code', '');
                });
            });
        </script>
    @endslot
@endcomponent
