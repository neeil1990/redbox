<div class="form-group">
    {!! Form::label('name', 'Имя проекта') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Введите имя проекта', 'required']) !!}
    <div class="invalid-feedback">{{ __('Please fill the field') }}</div>
</div>

<div class="form-group">
    {!! Form::label('url', 'URL домена') !!}
    {!! Form::text('url', null, ['class' => 'form-control', 'placeholder' => 'Введите URL домена (example.com) или страницы (example.com/page/)', 'required']) !!}
    <div class="invalid-feedback">{{ __('Please fill the field') }}</div>
</div>
