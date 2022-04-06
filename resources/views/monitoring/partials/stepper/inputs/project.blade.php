<div class="form-group">
    {!! Form::label('name', 'Имя проекта') !!}
    {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Введите имя проекта']) !!}
    @error('name') <span class="error invalid-feedback d-block">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    {!! Form::label('url', 'URL домена') !!}
    {!! Form::text('url', null, ['class' => 'form-control' . ($errors->has('url') ? ' is-invalid' : ''), 'placeholder' => 'Введите URL домена (example.com) или страницы (example.com/page/)']) !!}
    @error('url') <span class="error invalid-feedback d-block">{{ $message }}</span> @enderror
</div>
