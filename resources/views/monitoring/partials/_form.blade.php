
<div class="form-group">
    {!! Form::label('tariff', 'Тариф') !!}
    {!! Form::select('tariff', [], null, ['class' => 'form-control']) !!}
    @error('tariff') <span class="error invalid-feedback">{{ $message }}</span> @enderror
    <span class="valid-feedback d-block">Выберите тариф.</span>
</div>


<div class="form-group">
    {!! Form::submit('Купить', ['class' => 'btn btn-success']) !!}
</div>

