<div class="form-group">
    {!! Form::label('name', 'Название') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    {!! Form::label('code', 'Уникальный код') !!}
    {!! Form::text('code', null, ['class' => 'form-control', 'pattern' => '^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$']) !!}
    @error('code') <span class="error invalid-feedback">{{ $message }}</span> @enderror
    <span class="valid-feedback d-block">Обязательное поле.</span>
</div>

<div class="form-group">
    {!! Form::label('description', 'Описание') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    {!! Form::label('message', 'Сообщение. {TARIFF} = Название тарифа. {VALUE} = Значение переменной.') !!}
    {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
    @error('message') <span class="error invalid-feedback">{{ $message }}</span> @enderror
</div>
