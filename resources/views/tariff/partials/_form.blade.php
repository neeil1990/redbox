
<div class="form-group">
    {!! Form::label('tariff', 'Тариф') !!}
    {!! Form::select('tariff', $select['tariffs'], null, ['class' => 'form-control']) !!}
    @error('tariff') <span class="error invalid-feedback">{{ $message }}</span> @enderror
    <span class="valid-feedback d-block">Выберите тариф.</span>
</div>

<div class="form-group">
    {!! Form::label('period', 'Период') !!}
    <select name="period" id="period" class="form-control">
        @foreach($select['periods'] as $key => $value)
            <option value="{{ $key }}"> {{ __($value) }}</option>
        @endforeach
    </select>
    @error('period') <span class="error invalid-feedback">{{ $message }}</span> @enderror
    <span class="valid-feedback d-block">Выберите период.</span>
</div>

