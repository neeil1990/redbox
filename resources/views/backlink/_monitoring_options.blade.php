{!! Form::label('Связать проект с сайтом из мониторинга позиций') !!}
{!! Form::select('monitoring_project_id', $options, $value, ['class' => implode(' ', array_merge($class, ['monitoring-options']))]) !!}
