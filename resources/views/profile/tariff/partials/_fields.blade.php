
@forelse($fields as $field)
<div class="form-group">
    {!! Form::label('fields', $field['tariff']) !!}
    {!! Form::number("fields[$field[id]]", $field['value'], ['class' => 'form-control', 'min' => 0]) !!}
    @error('fields') <span class="error invalid-feedback">{{ $message }}</span> @enderror
</div>
@empty
    <p>Нет значений</p>
@endforelse
