@if(in_array('previous', $buttons))
    <button class="btn btn-secondary" onclick="stepper.previous()">Назад</button>
@endif

@if(in_array('next', $buttons))
    <button class="btn btn-success" onclick="stepper.next()">Вперед</button>
@endif

@if(in_array('action', $buttons))
    <button type="submit" class="btn btn-success">Сохранить</button>
@endif
