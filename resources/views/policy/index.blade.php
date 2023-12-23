@component('component.card', ['title' => __('Privacy Policy and Agree Terms')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
    @endslot
    <form action="{{ route('edit.policy.files') }}" method="POST" class="col-lg-12 col-sm-12 mb-5" id="summernote-form">
        @csrf

        <label for="type">Тип документа</label>
        <select name="type" id="type" class="custom-select">
            <option value="policy_ru">Policy ru</option>
            <option value="policy_en">Policy en</option>
            <option value="terms_ru">Terms ru</option>
            <option value="terms_en">Terms en</option>
        </select>

        <div class="form-group mt-3">
            <label>{{__('Text')}}</label>
            {!! Form::textarea('description', null, ['id' => 'description','class' => 'form-control mb-3' . ($errors->has('description') ? ' is-invalid' : '')]) !!}
            @error('description') <span class="error invalid-feedback">{{ $message }}</span>@enderror
        </div>

        <input type="submit" class="btn btn-secondary mr-2" value="{{__('Save Document')}}">
        <button class="btn btn-default btn-flat mr-2" type="button" data-toggle="modal"
                data-target="#clear-text">
            {{__('Clear')}}
        </button>
    </form>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script>
            $(function () {
                $('#description').summernote({
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

            $(document).ready(function () {
                $.ajax({
                    type: "post",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        type: 'policy_ru',
                    },
                    url: "{{ route('get.policy.document') }}",
                    success(response) {
                        $('.note-editable.card-block').html(response.document)
                    }
                });

                $('#type').on('change', function () {
                    $.ajax({
                        type: "post",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: $(this).val(),
                        },
                        url: "{{ route('get.policy.document') }}",
                        success(response) {
                            $('.note-editable.card-block').html(response.document)
                        }
                    });
                })
            })
        </script>
    @endslot
@endcomponent
