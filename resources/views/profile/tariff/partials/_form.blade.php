<div class="form-group">
    {!! Form::label('settings', 'Название') !!}
    {!! Form::select('settings', $settings, null, ['class' => 'form-control']) !!}
    @error('settings') <span class="error invalid-feedback">{{ $message }}</span> @enderror
</div>

