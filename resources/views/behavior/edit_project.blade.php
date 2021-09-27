@component('component.card', ['title' => __('Behavior')])

    @slot('css')

    @endslot

    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Edit behavior') }}</h3>
            </div>

            {!! Form::model($behavior, ['method' => 'PATCH', 'route' => ['behavior.update_project', $behavior->id]]) !!}
            <div class="card-body">
                <div class="form-group">
                    {!! Form::label('domain', __('Domain')) !!}
                    {!! Form::text('domain', null, ['class' => 'form-control' . ($errors->has('domain') ? ' is-invalid' : ''), 'placeholder' => __('Domain')]) !!}
                    @error('domain') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('minutes', __('Minutes on site')) !!}
                    {!! Form::number('minutes', null, ['min' => 1, 'max' => 60, 'class' => 'form-control' . ($errors->has('minutes') ? ' is-invalid' : ''), 'placeholder' => __('Minutes on site')]) !!}
                    @error('minutes') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('clicks', __('Clicks on site')) !!}
                    {!! Form::number('clicks', null, ['min' => 1, 'max' => 100, 'class' => 'form-control' . ($errors->has('clicks') ? ' is-invalid' : ''), 'placeholder' => __('Clicks on site')]) !!}
                    @error('clicks') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('pages', __('Pages visit')) !!}
                    {!! Form::number('pages', null, ['min' => 1, 'max' => 100, 'class' => 'form-control' . ($errors->has('pages') ? ' is-invalid' : ''), 'placeholder' => __('Pages visit')]) !!}
                    @error('pages') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('description', __('Description')) !!}
                    {!! Form::textarea('description', null, ['class' => 'form-control' . ($errors->has('description') ? ' is-invalid' : ''), 'placeholder' => __('Description')]) !!}
                    @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer">
                {!! Form::submit(__('Save'), ['class' => 'btn btn-secondary float-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    @slot('js')

    @endslot


@endcomponent
