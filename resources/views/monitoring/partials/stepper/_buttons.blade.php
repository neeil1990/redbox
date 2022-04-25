@if(in_array('previous', $buttons))
    <a class="btn btn-secondary" onclick="stepper.previous()">Назад</a>
@endif

@if(in_array('next', $buttons))
    <a class="btn btn-success" onclick="stepper.next()">Вперед</a>
@endif

@if(in_array('action', $buttons))
    <button type="submit" class="btn btn-success">Сохранить</button>
@endif
