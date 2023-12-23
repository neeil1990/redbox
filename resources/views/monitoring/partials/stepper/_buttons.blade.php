@if(in_array('back', $buttons))
    <a href="{{ route('monitoring.index') }}" class="btn btn-secondary">Вернутся в проекты</a>
@endif

@if(in_array('previous', $buttons))
    <a class="btn btn-secondary" onclick="stepper.previous()">Назад</a>
@endif

@if(in_array('next', $buttons))
    <a class="btn btn-success" onclick="stepper.next()">Вперед</a>
@endif

@if(in_array('action', $buttons))
    <a href="{{ route('monitoring.index') }}" class="btn btn-success">Ваши проекты</a>
@endif
