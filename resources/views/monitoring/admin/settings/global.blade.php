<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Global settings') }}</h3>
    </div>

    <div class="card-body">
        <div class="row">
            @foreach($settings['fields'] as $field)
            <div class="col-6">
                <div class="form-group">
                    {!! Form::label($field['label']) !!}
                    <div class="input-group">
                        @if($field['type'] == 'number')
                            {!! Form::number($field['name'], $settings['request'][$field['name']], ['class' => 'form-control', 'placeholder' => $field['placeholder']]) !!}
                        @elseif($field['type'] == 'time')
                            {!! Form::text($field['name'], $settings['request'][$field['name']], ['class' => 'form-control time', 'placeholder' => $field['placeholder']]) !!}
                        @else
                            {!! Form::text($field['name'], $settings['request'][$field['name']], ['class' => 'form-control', 'placeholder' => $field['placeholder']]) !!}
                        @endif
                        <div class="input-group-append">
                            <a href="{{ route('monitoring.admin.settings.delete', $field['name']) }}" class="input-group-text"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card-footer">
        {!! Form::submit(__('Save'), ['class' => 'btn btn-success']) !!}
    </div>
</div>

