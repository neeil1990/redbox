
<div class="form-group">
    {!! Form::label('tariff', 'Тариф') !!}
    {!! Form::select('tariff', $select, null, ['class' => 'form-control']) !!}
    @error('tariff') <span class="error invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    {!! Form::label('value', 'Основное значение') !!}
    {!! Form::number('value', 0, ['class' => 'form-control', 'min' => 0]) !!}
    @error('value') <span class="error invalid-feedback">{{ $message }}</span> @enderror
    <span class="valid-feedback d-block">Обязательное поле.</span>
</div>
