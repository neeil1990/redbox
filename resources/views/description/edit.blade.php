@extends('layouts.app')

@section('css')
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Save description') }}</h3>
                </div>
                {!! Form::model($description, ['id' => 'descriptionForm', 'method' => 'PATCH', 'route' => ['description.update', $description->code]]) !!}
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('position', __('Position')) !!}
                        {!! Form::select('position', ['top' => __('Top'), 'bottom' => __('Bottom')], null, ['id' => 'description-position', 'class' => 'custom-select rounded-0']) !!}
                        @error('position') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', __('Description')) !!}
                        {!! Form::textarea('description', null, ['id' => 'summernote', 'placeholder' => __('Description')]) !!}
                        @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer">
                    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary float-right']) !!}
                    {!! Form::button(__('Apply'), ['id' => 'save-description', 'class' => 'btn btn-success mr-2 float-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Summernote -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

    <!-- Page specific script -->
    <script>
        $(function () {
            $('#description-position').change(function () {
                var position = "{{ request()->route('position') }}";
                if(position){
                    var str = "{{request()->path()}}";
                    var url = "/" + str.replace(position, $(this).val());

                    window.location.replace(url);
                }
            });

            // Summernote
            $('#summernote').summernote({
                minHeight: 300,
            });

            $(document).on("submit","#descriptionForm",function(e){
                if ($('#summernote').summernote('codeview.isActivated')) {
                    $('#summernote').summernote('codeview.deactivate');
                }
            });

            $('#save-description').click(function(){
                $.ajax({
                    url: "{{ route('description.update', $description->code) }}",
                    type: 'PATCH',
                    data: {description: $('#summernote').summernote('code')},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(data) {
                        if(data.id){
                            $(document).Toasts('create', {
                                class: 'bg-success',
                                title: "{{ __('Save') }}",
                                subtitle: "{{ __('Close') }}",
                                body: "{{ __('Description saved!') }}",
                                autohide: true,
                                delay: 1000,
                            });
                        }
                    }
                });
                return false;
            });
        })
    </script>
@stop
