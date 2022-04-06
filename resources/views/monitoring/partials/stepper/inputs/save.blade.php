<div class="form-group">
    {!! Form::label('competitors', 'Мои конкуренты') !!}
    {!! Form::text('competitors[]', null, ['class' => 'form-control' . ($errors->has('competitors') ? ' is-invalid' : ''), 'placeholder' => 'Введите URL домена']) !!}
    @error('competitors') <span class="error invalid-feedback d-block">{{ $message }}</span> @enderror
</div>
