@component('component.card', ['title' => __('Behavior')])

    @slot('css')

    @endslot

    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Requests') }}</h3>
            </div>

            {!! Form::open(['method' => 'PATCH', 'route' => ['behavior.update', $behavior->id]]) !!}
            <div class="card-body">

                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            {!! Form::text('phrases[]', null, ['class' => 'form-control' . ($errors->has('phrases') ? ' is-invalid' : '')]) !!}
                            @error('phrases') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::number('count[]', 1, ['min' => 1, 'max' => 100, 'class' => 'form-control' . ($errors->has('count') ? ' is-invalid' : ''), 'placeholder' => __('Count')]) !!}
                            @error('count') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="button" id="adding-request" class="btn btn-block btn-default">{{ __('Add key phrases request') }}</button>
            </div>
            <div class="card-footer">
                {!! Form::submit(__('Save'), ['class' => 'btn btn-secondary float-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    @slot('js')
        <script>
            $('#adding-request').click(function(){
                let btn = $(this);
                let input = btn.prev();
                input.clone().insertAfter($('.row').last()).find('input[type="text"]').val('');
            });
        </script>
    @endslot


@endcomponent
